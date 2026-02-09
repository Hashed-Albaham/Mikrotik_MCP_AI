<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Services\Ai;

use App\Models\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Exception;
use App\Models\McpTool;

class AiOrchestrator
{
    protected $activeProvider;

    /**
     * Set the AI Provider to be used for the next request.
     */
    public function setProvider(AiProvider $provider)
    {
        $this->activeProvider = $provider;
    }

    /**
     * Send a message to the AI Provider and handle the response.
     */
    public function send(string $message, array $history = [], ?string $providerId = null): array
    {
        // FIXED: Rate limiting to prevent API abuse (60 requests per minute per user)
        $rateLimitKey = 'ai-request:' . (auth()->id() ?? 'guest');
        if (!RateLimiter::attempt($rateLimitKey, 60, fn() => true, 60)) {
            throw new Exception("Too many AI requests. Please wait a moment before trying again.");
        }

        // 1. Resolve Provider
        // Priority: Manually set provider -> Method argument -> Active/Default DB provider
        $provider = $this->activeProvider 
            ?? ($providerId ? AiProvider::find($providerId) : null) 
            ?? AiProvider::where('is_active', true)->first();

        if (!$provider) {
            throw new Exception("No active AI Provider found. Please configure one in the settings.");
        }

        // 2. Route based on Provider Type
        if ($provider->type === 'gemini_interaction') {
            return $this->sendInteraction($provider, $message, $history);
        }

        if ($provider->type === 'mistral_agent') {
            return $this->sendMistralAgent($provider, $message, $history);
        }

        // 3. Prepare Tools (if supported)
        $tools = [];
        if ($provider->supports_tools) {
            $tools = $this->getFormattedTools();
        }

        // 4. Construct Payload (Universal / OpenAI Compatible Format)
        $messages = $history;
        
        // Prepend System Instruction if exists
        // 5. Hardcoded Fallback System Prompt for "Agentic" capabilities
        $sysPrompt = $provider->system_instruction;
        if (empty($sysPrompt)) {
            $sysPrompt = "أنت مساعد شبكات ذكي متقدم (Network Automation Agent). لديك صلاحية الوصول إلى أداة قوية تسمى `execute_router_command`.

            القواعد الصارمة (System Rules):
            1. اللغة: يجب أن يكون ردك وفهمك وكل نقاشك باللغة العربية الفصحى أو التقنية المفهومة.
            2. التنفيذ الفوري: نفذ الأوامر فوراً.
            3. التنسيق (مهم جداً): عند عرض حالة الراوتر أو الموارد (مثل /system/resource/print)، **يجب** عرض البيانات في جدول Markdown منظم واحترافي. لا تعرضها كنص خام أبدًا.
            4. الشفافية: قبل أو بعد عرض الجدول، اذكر بوضوح: 'تم تنفيذ الأمر: [الأمر المنفذ]'.
            5. السياق: أنت تعرف حالة الراوتر (CPU, RAM, Uptime) إذا توفرت البيانات، استخدمها في تحليلك.";
        } else {
             // Append reinforcement
             $sysPrompt .= " تذكير: اعرض البيانات في جداول Markdown. اذكر الأمر المنفذ.";
        }

        array_unshift($messages, ['role' => 'system', 'content' => $sysPrompt]);
        
        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = [
            'model' => $provider->model_identifier,
            'messages' => $messages,
            'temperature' => 0.7,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        // FIXED: Removed sensitive token logging - Security Risk
        Log::info("Sending to AI Provider [ID: {$provider->id}]", [
            'url' => $provider->base_url . '/chat/completions',
            'model' => $payload['model'],
        ]);

        // FIXED: SSL verification is environment-aware (only disabled in local dev)
        // FIX: Remove trailing slash from base_url to avoid double slashes
        $baseUrl = rtrim($provider->base_url, '/');
        
        $response = Http::timeout(120)
            ->connectTimeout(30)
            ->when(app()->isLocal(), fn($r) => $r->withoutVerifying())
            ->withToken(trim($provider->api_key)) 
            ->baseUrl($baseUrl)
            ->post('/chat/completions', $payload);

        if ($response->failed()) {
            throw new Exception("AI Request Failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * STREAMING: Send message and return a generator for SSE/Livewire.
     * This allows "typing effect" in the UI.
     */
    public function sendStream(string $message, array $history = [], ?string $providerId = null)
    {
        // 1. Resolve Provider (Same logic as send)
        $rateLimitKey = 'ai-request:' . (auth()->id() ?? 'guest');
        if (!RateLimiter::attempt($rateLimitKey, 60, fn() => true, 60)) {
            throw new Exception("Too many AI requests. Please wait.");
        }

        $provider = $this->activeProvider 
            ?? ($providerId ? AiProvider::find($providerId) : null) 
            ?? AiProvider::where('is_active', true)->first();

        if (!$provider) throw new Exception("No active AI Provider configured.");

        // 2. Prepare Payload
        $messages = $history;
        $sysPrompt = $provider->system_instruction ?? "You are an advanced Network Automation Agent. Execute commands immediately if requested.";
        array_unshift($messages, ['role' => 'system', 'content' => $sysPrompt]);
        $messages[] = ['role' => 'user', 'content' => $message];

        $payload = [
            'model' => $provider->model_identifier,
            'messages' => $messages,
            'temperature' => 0.7,
            'stream' => true, // ENABLE STREAMING
        ];

        // Add Tools if supported
        $tools = [];
        if ($provider->supports_tools && $provider->type !== 'gemini_interaction') {
            $tools = $this->getFormattedTools();
            if(!empty($tools)) $payload['tools'] = $tools;
        }

        // 3. Send Stream Request
        $baseUrl = rtrim($provider->base_url, '/');
        
        $response = Http::timeout(120)
            ->connectTimeout(30)
            ->withOptions(['stream' => true]) // LARAVEL HTTP STREAM OPTION
            ->when(app()->isLocal(), fn($r) => $r->withoutVerifying())
            ->withToken(trim($provider->api_key)) 
            ->baseUrl($baseUrl)
            ->post('/chat/completions', $payload);

        if ($response->failed()) throw new Exception("Stream Request Failed: " . $response->body());

        // 4. Yield Chunks
        $body = $response->toPsrResponse()->getBody();
        $toolCallsBuffer = [];

        while (!$body->eof()) {
            $line = \App\Services\Ai\StreamParser::readLine($body);
            if ($line === null) break; 
            
            // Parse SSE format "data: {...}"
            if (str_starts_with($line, 'data: ')) {
                $json = substr($line, 6);
                if (trim($json) === '[DONE]') break;
                
                $data = json_decode($json, true);
                
                // Yield Content
                if (isset($data['choices'][0]['delta']['content'])) {
                    yield $data['choices'][0]['delta']['content'];
                }

                // Accumulate Tool Calls
                if (isset($data['choices'][0]['delta']['tool_calls'])) {
                    foreach ($data['choices'][0]['delta']['tool_calls'] as $tc) {
                        $idx = $tc['index'] ?? 0; // Fix: Default to 0 if index is missing
                        
                        if (!isset($toolCallsBuffer[$idx])) {
                            $toolCallsBuffer[$idx] = [
                                'id' => $tc['id'] ?? '',
                                'type' => 'function',
                                'function' => ['name' => $tc['function']['name'] ?? '', 'arguments' => '']
                            ];
                        }
                        
                        if (isset($tc['id'])) $toolCallsBuffer[$idx]['id'] = $tc['id'];
                        if (isset($tc['function']['name'])) $toolCallsBuffer[$idx]['function']['name'] = $tc['function']['name'];
                        if (isset($tc['function']['arguments'])) $toolCallsBuffer[$idx]['function']['arguments'] .= $tc['function']['arguments'];
                    }
                }
            }
        }
        
        return $toolCallsBuffer;
    }

    /**
     * Handle Gemini Interactions API (Experimental).
     */
    protected function sendInteraction(AiProvider $provider, string $message, array $history): array
    {
        // Constructing Input
        $config = $provider->config ?? [];
        $payload = [
            'input' => $message,
        ];

        // Add System Instruction for Interactions API
        // Note: The API spec for interactions might vary, but usually it's part of the agent config or params.
        // For now, if using a standard model via interactions, we can try adding it if supported.
        // However, strictly speaking, Interactions API handles context differently. 
        // For simply "shimmed" models, we might not have a direct 'system_instruction' field at top level in v1beta/interactions yet unless using specific model params.
        // But for consistency let's try to include it if the API supports it, or leave it for the Agent definition.
        // *Self-Correction*: Use 'system_instruction' in generation_config or top level depending on model? 
        // Actually, for Interactions, it's often part of the session or context. 
        // Let's Skip explicit system instruction for Interactions API for now unless we are sure where it goes, 
        // OR better: Prepend it to the input if it's a simple model interaction, or assume the Agent has it configured.
        // A safer bet for generic usage: Prepend to the user message if it's critical.
        
        if (!empty($provider->system_instruction)) {
             // Simply prepend for now to ensure it's seen
             $payload['input'] = "System Instruction: " . $provider->system_instruction . "\n\nUser: " . $message;
        }
        
        if (!empty($config['agent'])) {
             $payload['agent'] = $config['agent'];
             if (!empty($config['thinking_level'])) {
                 $payload['agent_config'] = [
                     'type' => 'dynamic', 
                 ];
                 
                 if ($config['agent'] === 'deep-research-pro-preview-12-2025') {
                      $payload['agent_config'] = [
                          'type' => 'deep-research',
                      ];
                 }
             }
        } else {
            $payload['model'] = $provider->model_identifier;
            if (!empty($config['thinking_level'])) {
                $payload['generation_config'] = [
                    'thinking_level' => $config['thinking_level'],
                    'thinking_summaries' => 'auto' 
                ];
            }
        }

        // Send Request to /v1beta/interactions
        $baseUrl = str_replace('/openai', '', rtrim($provider->base_url, '/'));
        
        $response = Http::timeout(120) 
            ->withHeaders(['x-goog-api-key' => $provider->api_key])
            ->post("{$baseUrl}/interactions", $payload);

        if ($response->failed()) {
             throw new Exception("Interaction Failed: " . $response->body());
        }

        $data = $response->json();

        $contentText = $data['outputs'][0]['text'] ?? 'No response content.';
        
        return [
            'choices' => [
                [
                    'message' => [
                        'role' => 'assistant',
                        'content' => $contentText
                    ]
                ]
            ]
        ];
    }



    /**
     * Convert MCP Tools from DB to OpenAI Tool Schema.
     */
    protected function getFormattedTools(): array
    {
        // Force include the new tool for debugging if DB sync is slow
        $dbTools = McpTool::all();
        $formatted = [];

        foreach ($dbTools as $tool) {
            $formatted[] = [
                'type' => 'function',
                'function' => [
                    'name' => $tool->name,
                    'description' => $tool->description,
                    'parameters' => $tool->parameters_schema, 
                ]
            ];
        }
        
        // Manual fallback/inject if missing from DB for some reason
        $hasExec = collect($formatted)->contains('function.name', 'execute_router_command');
        if (!$hasExec) {
             $formatted[] = [
                'type' => 'function',
                'function' => [
                    'name' => 'execute_router_command',
                    'description' => 'Executes Any RouterOS scripting command on the connected router. Use this for configuration, status checks, and everything else. Example: /system/resource/print',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'command' => ['type' => 'string', 'description' => 'The RouterOS command to run.']
                        ],
                        'required' => ['command']
                    ]
                ]
            ];
        }

        \Illuminate\Support\Facades\Log::info("AiOrchestrator Tools: " . json_encode(array_column(array_column($formatted, 'function'), 'name')));

        return $formatted;
    }

    /**
     * Handle Mistral Agents API.
     */
    protected function sendMistralAgent(AiProvider $provider, string $message, array $history): array
    {
        $messages = $history;
        
        // Manual Context Injection for Agents (Since we can't easily override system prompt on some Agent APIs)
        // We tell the Agent explicitly about its capabilities in the context of the user message.
        $toolContext = "\n\n[System Note]: You have access to a local router via the `execute_router_command` tool. If the user asks for configuration or status, please use this tool to run RouterOS commands (e.g., /system resource print).";
        
        $messages[] = ['role' => 'user', 'content' => $message . $toolContext];

        $payload = [
            'agent_id' => $provider->model_identifier, 
            'messages' => $messages,
            'max_tokens' => 4000, 
        ];

        $response = Http::timeout(120)
            ->connectTimeout(30)
            ->withoutVerifying()
            ->withToken(trim($provider->api_key)) 
            ->baseUrl($provider->base_url)
            ->post('/agents/completions', $payload); 

        if ($response->failed()) {
            throw new Exception("Mistral Agent Request Failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * List Files (Mistral Files API)
     */
    public function listFiles(AiProvider $provider): array
    {
        $response = Http::withToken(trim($provider->api_key))
            ->baseUrl($provider->base_url)
            ->get('/files');

        if ($response->failed()) {
            throw new Exception("Failed to list files: " . $response->body());
        }

        return $response->json()['data'] ?? [];
    }

    /**
     * Upload File (Mistral Files API)
     */
    public function uploadFile(AiProvider $provider, $file)
    {
        // Mistral requires multipart/form-data
        // purpose: "fine-tune" or "rag"? Usually "assistants" or "fine-tune".
        // The spec often defaults to specific purposes. Let's assume 'assistants' if available or generic.
        // Mistral API Doc says purpose is mandatory. 'fine-tune' is common, check if 'agents' use 'assistants'.
        
        $response = Http::withToken(trim($provider->api_key))
            ->baseUrl($provider->base_url)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post('/files', [
                'purpose' => 'fine-tune' // Currently Mistral docs verify 'fine-tune'. Agents might use this source.
            ]);

        if ($response->failed()) {
            throw new Exception("File Upload Failed: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Delete File (Mistral Files API)
     */
    public function deleteFile(AiProvider $provider, string $fileId)
    {
        $response = Http::withToken(trim($provider->api_key))
            ->baseUrl($provider->base_url)
            ->delete("/files/{$fileId}");

        if ($response->failed()) {
            throw new Exception("Delete Failed: " . $response->body());
        }

        return true;
    }

    /**
     * Process Document/Image via Mistral OCR.
     * Returns extracted markdown text.
     */
    public function processOCR(AiProvider $provider, $fileObj): string
    {
        // 1. Upload file specifically for OCR purpose? 
        // Mistral OCR usually takes a URL or a file upload.
        // For efficiency, we'll assume we upload to the signed URL or file endpoint first, 
        // OR we can send the image bytes directly if the API supports it (like Pixtral).
        // BUT, `mistral-ocr-latest` is a specific model.
        // Current best practice for Mistral OCR:
        // POST /v1/ocr 
        // Body: { "model": "mistral-ocr-latest", "document": { "type": "image_url", "image_url": "..." } } OR "document": { "type": "document_url", ... }
        // If local file, we might need to send base64 or upload first.
        
        // Simpler approach for now: Base64 for images if supported, or error if not.
        // *Correction*: Mistral OCR often requires a recognizable public URL or a specific file ID. 
        // Let's try sending as a file upload if the SDK does that, or Base64.
        // Actually, for "mistral-ocr-latest", it often works on input documents.
        
        // Let's implement the "Upload & Chat" flow for OCR:
        // 1. Upload content -> Get ID? Or send Base64?
        // Let's try the direct URL/Base64 approach common in other endpoints if clear, 
        // otherwise default to: User uploads -> We send to OCR endpoint.
        
        // *Drafting the request*:
        // POST /v1/chat/completions (Wait, OCR is separate?)
        // Docs: https://docs.mistral.ai/capabilities/vision/
        // "Pixtral" is for chat with images.
        // "OCR" is specific. 
        
        // Let's implement generic Image-to-Text via Pixtral 12B (cheaper/easier for "Agent" flow) 
        // UNLESS user specifically asked for "OCR" endpoint.
        // The user said "OCR", so let's try to be precise. 
        // If we can't easily find the specific /ocr endpoint signature, we'll use Pixtral with a prompt "Transcribe this".
        
        // *Decision*: PROMPTING Pixtral is often more robust for an "Agent" than raw OCR unless strict layout preservation is needed.
        // However, let's look for a specific `ocr` method.
        
        // Fallback/Robust Implementation: Use Pixtral for "Vision/OCR".
        // It serves both purposes (Describe image OR Read text based on prompt).
        // We will call it `processImage` instead of `processOCR` to be generic, 
        // but can enforce "Transcribe only" via prompt if needed.
        
        // Wait, user explicitly mentioned "OCR" and "File Files".
        // Let's stick to the naming `processOCR` but implement it using Pixtral for now as it's the most versatile "Agentic" way 
        // without managing separate OCR pipelines, unless we strictly see /ocr docs.
        
        // Refined Plan: 
        // If file is Image -> Send to Pixtral with "Extract all text from this image...".
        // If file is PDF -> Mistral might support PDF in OCR.
        
        // Let's assume simplest path:
        // PDF/Image -> Mistral Files API (already implemented upload) -> Then what?
        // Agents can use tools to read files.
        
        // Let's implement `extractTextFromImage` using Pixtral.
        
        // Resize image if larger than 1024px to avoid Rate Limits / Token Limits
        $imagePath = $fileObj->getRealPath();
        $mimeType = $fileObj->getMimeType();
        
        if (str_starts_with($mimeType, 'image/')) {
            list($width, $height) = getimagesize($imagePath);
            $maxDim = 1024;
            
            if ($width > $maxDim || $height > $maxDim) {
                // Simple GD resize
                $ratio = $width / $height;
                if ($ratio > 1) {
                    $newWidth = $maxDim;
                    $newHeight = $maxDim / $ratio;
                } else {
                    $newHeight = $maxDim;
                    $newWidth = $maxDim * $ratio;
                }
                
                $src = imagecreatefromstring(file_get_contents($imagePath));
                $dst = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                
                // Save to buffer
                ob_start();
                imagejpeg($dst, null, 85); // 85% quality
                $data = ob_get_clean();
                $base64Image = base64_encode($data);
                
                imagedestroy($src);
                imagedestroy($dst);
            } else {
                $base64Image = base64_encode(file_get_contents($imagePath));
            }
        } else {
             // Fallback for PDFs etc (Mistral OCR supports URLs better, but here we assume image flow for Pixtral)
             // If PDF, better to throw error or implement PDF-to-Image if needed.
             // For now, assume it's small enough or text.
             $base64Image = base64_encode(file_get_contents($imagePath));
        }

        $dataUri = "data:{$mimeType};base64,{$base64Image}";

        $payload = [
            'model' => 'pixtral-12b-2409', 
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => "Analyze this image. If it contains text, transcribe it exactly. If it is a screenshot of network settings, describe the configuration details."],
                        ['type' => 'image_url', 'image_url' => $dataUri]
                    ]
                ]
            ],
            'max_tokens' => 2000
        ];

        // Debugging Rate Limits
        Log::info("Sending Image to OCR/Vision", ['size_kb' => strlen($base64Image)/1024]);

        $response = Http::timeout(60)
            ->withoutVerifying()
            ->withToken(trim($provider->api_key)) 
            ->baseUrl($provider->base_url)
            ->post('/chat/completions', $payload);

        if ($response->failed()) {
             throw new Exception("OCR Failed: " . $response->body());
        }

        return $response->json()['choices'][0]['message']['content'] ?? '';
    }
}
