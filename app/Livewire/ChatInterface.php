<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\Ai\AiOrchestrator;
use Illuminate\Support\Facades\Log;

class ChatInterface extends Component
{
    use \Livewire\WithFileUploads;

    public $sessionId;
    public $input = '';
    public $upload; // File Attachment
    public $messages = [];
    public $isTyping = false;
    
    public $availableProviders = [];
    public $activeProviderId;

    public $routers = [];
    public $activeRouterId;
    public $connectionStatus = null; // 'success' or 'error'
    public $connectionMessage = '';

    public function mount()
    {
        // Load available providers for the switcher
        $this->availableProviders = \App\Models\AiProvider::where('is_active', true)->get();
        
        // Load Routers
        $this->routers = \App\Models\Router::all();
        $this->activeRouterId = session('active_router_id', $this->routers->first()->id ?? null);

        
        // 1. Resolve Provider Preference
        // Priority: Session > Last Chat Session > Default First
        $lastProviderId = session('active_provider_id');
        
        if (!$lastProviderId) {
             $lastSession = ChatSession::where('user_id', 1)->latest()->first();
             $lastProviderId = $lastSession ? $lastSession->provider_id : ($this->availableProviders->first()->id ?? 1);
        }

        $this->activeProviderId = $lastProviderId;

        // 2. Resolve Chat Session
        // We'll try to resume the latest session to keep history, or create new if explicitly requested (logic can be added later)
        // For now, let's just get the latest session for this user.
        $session = ChatSession::where('user_id', 1)->latest()->first();
        
        if (!$session) {
            $session = ChatSession::create([
                'user_id' => 1,
                'provider_id' => $this->activeProviderId,
                'title' => 'New Session - ' . now()->format('H:i')
            ]);
        } else {
            // Update the session's provider to match current selection if different
            // actually, we should probably switch the VIEW to match the session if we are resuming?
            // User requested: "Selected model does not change until changed manually".
            // So if I select Gemini, and refresh, I want Gemini.
            // If I reuse an old session that was OpenAI, do I switch the session to Gemini? Yes, dynamic switching.
             if ($session->provider_id != $this->activeProviderId) {
                 $session->provider_id = $this->activeProviderId;
                 $session->save();
             }
        }
        
        $this->sessionId = $session->id;
        $this->loadMessages();
    }

    public function updatedActiveProviderId($value)
    {
        // Persist preference
        session(['active_provider_id' => $value]);

        // Update the current session's provider
        $session = ChatSession::find($this->sessionId);
        if ($session) {
            $session->provider_id = $value;
            $session->save();
        }
    }

    public function updatedActiveRouterId($value)
    {
        session(['active_router_id' => $value]);
        $this->connectionStatus = null; // Reset status on change
    }

    public function checkConnection()
    {
        $this->connectionStatus = null;
        
        if (!$this->activeRouterId) {
            $this->connectionStatus = 'error';
            $this->connectionMessage = 'No Router Selected';
            return;
        }

        try {
            $router = \App\Models\Router::find($this->activeRouterId);
            if (!$router) throw new \Exception("Router not found");

            // Perform full API handshake to verify credentials
            $adapter = \App\Services\Router\RouterOsFactory::make($router);
            
            // If we got here, connection and login succeeded (Factory calls connect())
            $version = $router->os_version ?? 'Unknown';
            $this->connectionStatus = 'success';
            $this->connectionMessage = "Connected to {$router->name} (RouterOS $version) successfully!";
            
            // Optional: Close connection explicitly if adapter supports it, 
            // but PHP Request shutdown will likely handle it.
            if (method_exists($adapter, 'disconnect')) {
                $adapter->disconnect();
            }

        } catch (\Exception $e) {
            Log::error("Connection Check Failed: " . $e->getMessage());
            $this->connectionStatus = 'error';
            // User friendly error mapping
            $msg = $e->getMessage();
            if (str_contains($msg, 'login failure') || str_contains($msg, 'cannot log in')) {
                $this->connectionMessage = "Login Failed! Check Username/Password in Edit Router.";
            } elseif (str_contains($msg, 'timed out') || str_contains($msg, '10060')) {
                $this->connectionMessage = "Connection Timed Out. Check IP, Port and Firewall.";
            } else {
                 $this->connectionMessage = "Error: $msg";
            }
        }
    }

    public function newSession()
    {
        $this->messages = [];
        
        // Create new session
        $session = ChatSession::create([
            'user_id' => 1,
            'provider_id' => $this->activeProviderId,
            'title' => 'New Session - ' . now()->format('H:i')
        ]);
        
        $this->sessionId = $session->id;
        $this->input = '';
        $this->isTyping = false;
        
        // Dispatch event to clear frontend state if needed
        $this->dispatch('message-sent'); // Clears pending message if any
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::where('session_id', $this->sessionId)
            ->oldest()
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'ui_widget' => $msg->ui_widget,
                    'tool_calls' => $msg->tool_calls
                ];
            })
            ->toArray();
    }

    public function sendMessage(AiOrchestrator $ai, $content = null)
    {
        $userMsgContent = $content ?? $this->input;
        
        if (empty(trim($userMsgContent))) return;

        // Reset input if it hasn't been cleared by frontend yet
        $this->input = '';
        $this->isTyping = true;
        
        // Handle Attachment (OCR/Vision)
        $attachmentContent = '';
        if ($this->upload) {
            Log::info("ChatInterface: File upload detected.", ['name' => $this->upload->getClientOriginalName()]);
            try {
                // Determine Provider
                $provider = \App\Models\AiProvider::find($this->activeProviderId);
                
                // OCR / Text Extraction
                $extracted = $ai->processOCR($provider, $this->upload);
                Log::info("ChatInterface: OCR Extracted.", ['length' => strlen($extracted)]);
                
                $attachmentContent = "\n\n[Attached File Content]:\n" . $extracted;
                
                $this->upload = null; // Clear after processing
            } catch (\Exception $e) {
                Log::error("ChatInterface: File Processing Error: " . $e->getMessage());
                $attachmentContent = "\n\n[Error reading file: " . $e->getMessage() . "]";
            }
        } else {
             Log::info("ChatInterface: No upload detected.");
        }
        
        $fullContent = $userMsgContent . $attachmentContent;

        // 1. Save User Message
        ChatMessage::create([
            'session_id' => $this->sessionId,
            'role' => 'user',
            'content' => $fullContent
        ]);

        $this->loadMessages();
        
        try { // Restored try block

            // 2. Prepare Validated History (Fixes 3230 Error by filtering broken turns)
            $history = $this->getValidHistory();
            
            // 3. Configure AI Service with Selected Provider
            $provider = \App\Models\AiProvider::find($this->activeProviderId);
            if ($provider) {
                $ai->setProvider($provider);
            }

            // 4. Call AI Service
            // Note: AiOrchestrator::send returns an array compatible with OpenAI format
            $response = $ai->send($userMsgContent, $history);
            
            $content = $response['choices'][0]['message']['content'] ?? '';
            $toolCalls = $response['choices'][0]['message']['tool_calls'] ?? null;
            $uiWidget = null;

            // 5. Handle Tool Calls (The AI wants to do something)
            if ($toolCalls) {
                foreach ($toolCalls as $toolCall) {
                    $fnName = $toolCall['function']['name'] ?? 'unknown';
                    Log::info("ChatInterface: AI requested tool '$fnName'", ['args' => $toolCall['function']['arguments'] ?? '']);
                    
                    // If AI wants to generate vouchers, we show the Form Widget first
                    if ($fnName === 'generate_hotspot_vouchers') {
                        $uiWidget = [
                            'type' => 'form',
                            'tool' => 'generate_hotspot_vouchers',
                            'fields' => [
                                ['name' => 'count', 'label' => 'Quantity', 'type' => 'number', 'default' => 10],
                                ['name' => 'profile', 'label' => 'User Profile', 'type' => 'text', 'default' => 'default'],
                                ['name' => 'server', 'label' => 'Hotspot Server', 'type' => 'text', 'default' => 'all']
                            ]
                        ];
                        // If content is empty but we have a tool, add a default message
                        if (empty($content)) $content = "I can help with that. Please confirm the details below:";
                    }
                }
            }

            // 6. Save Assistant Response
            ChatMessage::create([
                'session_id' => $this->sessionId,
                'role' => 'assistant',
                'content' => $content,
                'tool_calls' => $toolCalls,
                'ui_widget' => $uiWidget
            ]);

        } catch (\Exception $e) {
            Log::error($e);
            ChatMessage::create([
                'session_id' => $this->sessionId,
                'role' => 'system',
                'content' => "AI Connection Error: " . $e->getMessage() . ". Ensure your AI Provider settings are correct in the Admin Panel."
            ]);
        }

        $this->isTyping = false;
        $this->loadMessages();
        $this->dispatch('message-sent');
    }

    /**
     * Handles autonomous tool execution triggered by the frontend loop.
     */
    public function handleAutoToolExecution($toolName, $toolCallId, $args)
    {
        // 1. Validate
        if ($toolName !== 'execute_router_command') return;

        // 2. Log "Thinking" / System Message
        // We might want to show this briefly or just have it in history.
        // User requested "Thinking" visualization.
        // We can add a "system" message that shows the command being executed.
        
        $this->messages[] = [
            'id' => uniqid(),
            'role' => 'system',
            'content' => "🤖 Executing: " . ($args['command'] ?? 'Unknown Command') . "...",
            'ui_widget' => null, 
            'tool_calls' => null
        ];
        
        // Save to DB to persist this "thought"
        ChatMessage::create([
            'session_id' => $this->sessionId,
            'role' => 'system',
            'content' => "Executing: " . ($args['command'] ?? 'Unknown Command')
        ]);

        $output = "Error: Tool execution failed.";

        try {
            // 3. Execute Tool
            $tool = new \App\Mcp\Tools\ExecuteRouterCommandTool();
            $routerId = $this->activeRouterId ?? \App\Models\Router::first()->id ?? null;
            
            if (!$routerId) throw new \Exception("No Router configured.");

            $result = $tool->execute($args, $routerId);
            
            $output = $result['status'] === 'success' 
                ? json_encode($result['output']) 
                : "Error: " . ($result['error'] ?? 'Unknown error') . ". Suggestion: " . ($result['suggestion'] ?? '');

        } catch (\Exception $e) {
            $output = "System Error: " . $e->getMessage();
        }

        // 4. Add Tool Result to Conversation History (Role: tool)
        // OpenAI format requires role: tool, tool_call_id, content.
        // Our ChatMessage model supports 'role'. We'll store it as 'tool' role.
        // NOTE: Our `loadMessages` maps DB columns. We need to ensure DB 'role' supports 'tool'.
        // Schema update said: enum('user', 'assistant', 'system', 'tool'). Yes, it effectively supports it if migration ran.
        // If 'tool' enum is missing in DB (if I didn't run a migration for it), I might need to use 'system' with prefix.
        // Let's assume 'system' for safety if migration wasn't verified, BUT I should use 'tool' for AI context.
        // The migration `2024_01_01...` had `enum('role', ['user', 'assistant', 'system', 'tool'])`. So we are good.

        ChatMessage::create([
            'session_id' => $this->sessionId,
            'role' => 'tool', // OpenAI 'tool' role
            'content' => $output,
            'tool_calls' => ['tool_call_id' => $toolCallId] // Store ID to link back
        ]);
        
        // 5. Trigger AI Again/Continue Loop
        // We pass 'null' as content because we are continuing the turn with updated history.
        // We need `sendMessage` to pick up the latest messages.
        $this->loadMessages();
        
        // Call sendMessage but indicate it's a "Tool Response" turn.
        // Current sendMessage uses $this->input if content is null.
        // We need to bypass that.
        $this->continueConversation();
    }

    public function continueConversation()
    {
        $this->isTyping = true;
        // RELOAD MESSAGES FIRST to get the LATEST state from DB (including the tool output just saved)
        $this->loadMessages();
        
        try {
            // CHECK: Do we have pending tool calls?
            // If the last assistant message had multiple calls (e.g. 2 calls), 
            // we must ensure we have 2 'tool' messages following it before replying.
            
            $messages = collect($this->messages);
            $lastAssistantMsg = $messages->where('role', 'assistant')->last();
            
            if ($lastAssistantMsg && !empty($lastAssistantMsg['tool_calls'])) {
                $toolCalls = $lastAssistantMsg['tool_calls'];
                
                // Fallback: If for some reason casting didn't work, decode it manually.
                if (is_string($toolCalls)) {
                    $toolCalls = json_decode($toolCalls, true);
                }

                if (is_array($toolCalls)) {
                    $callCount = count($toolCalls);
                    Log::info("Debug 3230: Found Last Assistant Msg [{$lastAssistantMsg['id']}] with $callCount tool calls.");
                    
                    // Count how many 'tool' messages exist AFTER this assistant message
                    $allMsgIds = array_column($this->messages, 'id');
                    $lastAssistantIndex = array_search($lastAssistantMsg['id'], $allMsgIds);
                    
                    if ($lastAssistantIndex !== false) {
                        $subsequentToolMsgs = 0;
                        for ($i = $lastAssistantIndex + 1; $i < count($this->messages); $i++) {
                            if ($this->messages[$i]['role'] === 'tool') {
                                $subsequentToolMsgs++;
                            }
                        }
                        
                        Log::info("Debug 3230: Found $subsequentToolMsgs subsequent tool responses.");

                        // LOGIC: If we have fewer answers than questions, STOP.
                        if ($subsequentToolMsgs < $callCount) {
                            Log::info("ChatInterface: STOPPING. Waiting for all tool outputs. ($subsequentToolMsgs / $callCount)");
                            $this->isTyping = false; 
                            return; // EXIT. Wait for next tool output.
                        } else {
                            Log::info("Debug 3230: Count matches. Proceeding to call AI.");
                        }
                    }
                } else {
                     Log::warning("Debug 3230: tool_calls is not an array or valid JSON", ['raw' => $lastAssistantMsg['tool_calls']]);
                }
            }
            
            $ai = app(AiOrchestrator::class);
            
            // 2. Prepare Validated History
            $history = $this->getValidHistory();
            
            // Configure Provider
            $provider = \App\Models\AiProvider::find($this->activeProviderId);
            if ($provider) $ai->setProvider($provider);

            // Pass generic confirmation. 
            // In OpenAI, you usually send the tool outputs and get a response immediately.
            // The 'user' message is optional if the last msg was 'tool'.
            // However, AiOrchestrator::send wrapper usually forces a user msg.
            // Let's rely on standard flow.
            $response = $ai->send("Tool outputs submitted.", $history);
            
            $content = $response['choices'][0]['message']['content'] ?? '';
            $toolCalls = $response['choices'][0]['message']['tool_calls'] ?? null;
             
            ChatMessage::create([
                'session_id' => $this->sessionId,
                'role' => 'assistant',
                'content' => $content,
                'tool_calls' => $toolCalls
            ]);
            
        } catch (\Exception $e) {
             Log::error($e);
             ChatMessage::create([
                'session_id' => $this->sessionId,
                'role' => 'system',
                'content' => "Error in autonomous loop: " . $e->getMessage()
            ]);
        }
        
        $this->isTyping = false;
        $this->loadMessages();
        $this->dispatch('message-sent');
    }



    /**
     * Sanitizes history to ensure every Assistant message with tool_calls 
     * has the exact corresponding number of Tool responses. 
     * If a turn is incomplete, we strip the 'tool_calls' from the assistant message 
     * to prevent API errors (3230), effectively treating it as a text-only turn or dropping it.
     */
    public function getValidHistory()
    {
        $validHistory = [];
        $messages = $this->messages; // Array from loadMessages()
        
        $skipUntilIndex = -1;

        for ($i = 0; $i < count($messages); $i++) {
            if ($i <= $skipUntilIndex) continue;

            $m = $messages[$i];
            
            // Basic Formatting
            $msg = [
                'role' => $m['role'], 
                'content' => $m['content'] ?? ''
            ];

            // Handle Assistant with Tools
            if ($m['role'] === 'assistant' && !empty($m['tool_calls'])) {
                $toolCalls = $m['tool_calls'];
                if (is_string($toolCalls)) $toolCalls = json_decode($toolCalls, true);
                
                if (is_array($toolCalls) && count($toolCalls) > 0) {
                    $callCount = count($toolCalls);
                    $subsequentToolMsgs = 0;
                    $toolMsgIndices = [];

                    // Look ahead for tool responses
                    for ($j = $i + 1; $j < count($messages); $j++) {
                        if ($messages[$j]['role'] === 'tool') {
                            $subsequentToolMsgs++;
                            $toolMsgIndices[] = $j;
                        } else {
                            // Hit a user or system message, stop looking
                            if ($messages[$j]['role'] !== 'assistant') break; 
                        }
                    }

                    // VALIDATION LOGIC
                    if ($subsequentToolMsgs == $callCount) {
                        // Perfect match. Add this message with tools.
                        $msg['tool_calls'] = $toolCalls;
                        $validHistory[] = $msg;
                        
                        // Add the tool responses
                        foreach ($toolMsgIndices as $idx) {
                             $tm = $messages[$idx];
                             $tMsg = ['role' => 'tool', 'content' => $tm['content'] ?? ''];
                             if (!empty($tm['tool_calls']['tool_call_id'])) {
                                 $tMsg['tool_call_id'] = $tm['tool_calls']['tool_call_id'];
                             }
                             $validHistory[] = $tMsg;
                        }
                        
                        $skipUntilIndex = end($toolMsgIndices); // Skip processed tool msgs
                        continue; 
                    } else {
                        // MISMATCH (The cause of Error 3230)
                        Log::warning("ChatInterface: Dropping tool_calls from Msg ID {$m['id']} due to mismatch. Expected $callCount, Found $subsequentToolMsgs");
                        
                        // Option A: Drop the tools, keep content (if any)
                        // This "heals" the history by making it look like a text response.
                        // However, if content is null, we must ensure it's not null.
                        if (empty($msg['content'])) {
                            $msg['content'] = "[System: Invalid Tool Call History Skipped]";
                        }
                        // Do NOT add 'tool_calls' key. Ensure it's unset.
                        if (isset($msg['tool_calls'])) unset($msg['tool_calls']);
                        
                        $validHistory[] = $msg;
                        
                        // We do NOT add the partial tool responses, as they would be orphaned.
                        if ($subsequentToolMsgs > 0) {
                             $skipUntilIndex = end($toolMsgIndices);
                        }
                        continue;
                    }
                }
            }

            // Handle Tool messages (Should be handled by loop above, but if orphaned tool msg appears?)
            if ($m['role'] === 'tool') {
                // If we reach here, it's an orphaned tool message (parent was skipped or text-only).
                // Log it.
                // Log::info("ChatInterface: Skipping orphaned tool message ID {$m['id']}");
                continue; 
            }

            $validHistory[] = $msg;
        }

        // Log::info("ChatInterface: History validated. Size: " . count($validHistory));
        return $validHistory;
    }

    public function render()
    {
        return view('livewire.chat-interface')->layout('layouts.app');
    }
}
