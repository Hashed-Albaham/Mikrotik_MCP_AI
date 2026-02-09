<div class="flex h-screen overflow-hidden backdrop-blur-sm bg-black/10" 
     x-data="{ 
        sidebarOpen: false, 
        pendingMessage: '',
        autoPilot: true, 
        watchdogStatus: 'idle', // idle, executing, verifying
        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('chat-container');
                if(container) container.scrollTop = container.scrollHeight;
            });
        }
     }"
     x-init="scrollToBottom()"
     x-on:trigger-auto-tool.window="
        if(autoPilot) {
            watchdogStatus = 'executing';
            $wire.handleAutoToolExecution($event.detail.tool, $event.detail.id, $event.detail.args)
                .then(() => watchdogStatus = 'idle');
        }
     "
     x-on:generate-ai-response.window="$wire.generateResponse($event.detail.messageId)"
     @message-sent.window="pendingMessage = ''; scrollToBottom()"
>
    <!-- Sidebar -->
    <aside class="w-72 glass border-r border-white/5 flex flex-col fixed md:relative z-20 h-full transition-transform duration-300 md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="p-6 border-b border-white/5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <span class="font-bold text-lg tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">NetOrchestrator</span>
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold">AI Powered</p>
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Brain Selector -->
            <div class="px-2 mb-6">
                <!-- AI Provider -->
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Active Brain</div>
                <div class="relative mb-4">
                    <select wire:model.live="activeProviderId" class="w-full appearance-none bg-gray-800/50 border border-brand-500/30 text-white text-sm rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-1 focus:ring-brand-500 hover:bg-gray-800 transition-colors cursor-pointer">
                        @foreach($availableProviders as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-2.5 text-brand-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                </div>

                <!-- Router Selection -->
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex justify-between items-center">
                    <span>Target Router</span>
                    <a href="/admin/routers" class="text-[10px] text-brand-400 hover:text-brand-300">Edit</a>
                </div>
                <div class="relative">
                    <select wire:model.live="activeRouterId" class="w-full appearance-none bg-gray-800/50 border border-blue-500/30 text-white text-sm rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 hover:bg-gray-800 transition-colors cursor-pointer">
                        @foreach($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute left-3 top-2.5 text-blue-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <!-- Connection Check -->
                <button wire:click="checkConnection" wire:loading.attr="disabled" class="mt-2 w-full flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-white/5 hover:bg-white/10 border border-white/5 transition-all text-gray-400 hover:text-white">
                    <span wire:loading.remove wire:target="checkConnection">Test Connection</span>
                    <div wire:loading wire:target="checkConnection" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>

                <!-- Connection Status -->
                 @if($connectionStatus)
                    <div class="mt-2 text-xs p-2 rounded-lg border {{ $connectionStatus === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-red-500/10 border-red-500/20 text-red-400' }}">
                        {{ $connectionMessage }}
                    </div>
                @endif
            </div>

            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2">Workspace</div>
            
            <button wire:click="newSession" class="w-full text-left p-3 rounded-xl bg-gradient-to-r from-brand-900/50 to-transparent border border-brand-500/20 hover:border-brand-500/50 transition-all duration-200 group relative overflow-hidden">
                <div class="absolute inset-0 bg-brand-500/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <span class="text-brand-400 group-hover:text-brand-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </span>
                    <span class="font-medium text-sm text-gray-300 group-hover:text-white">New Chat Session</span>
                </div>
            </button>

            <!-- Navigation Links -->
            <div class="mt-8">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2 mb-2">Management</div>
                <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                <a href="/admin/routers" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg>
                    Routers
                </a>
            </div>
        </div>

        <div class="p-4 border-t border-white/5 z-20 glass-panel mt-auto mx-2 mb-2 rounded-xl">
             <div class="flex items-center gap-2">
                <div class="relative">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <div class="w-2 h-2 rounded-full bg-green-500 absolute inset-0 animate-ping opacity-75"></div>
                </div>
                <span class="text-xs font-mono text-green-400">System Operational</span>
            </div>
        </div>
    </aside>
    
    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/80 z-10 md:hidden backdrop-blur-sm" style="display: none;"></div>

    <!-- Main Chat Area -->
    <main class="flex-1 flex flex-col relative z-0">
        <!-- Top Bar Mobile -->
        <div class="md:hidden glass p-4 flex items-center justify-between border-b border-white/10">
            <span class="font-bold">NetOrchestrator</span>
            <button @click="sidebarOpen = true" class="p-2 text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-8 scrollbar-hide" id="chat-container">
            @if(empty($messages))
                <div class="h-full flex flex-col items-center justify-center text-center">
                    <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-gray-800 to-gray-700 flex items-center justify-center mb-6 shadow-2xl animate-fade-in-up">
                        <div class="text-5xl">🧬</div>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-2 animate-fade-in-up" style="animation-delay: 0.1s;">AI Operator Ready</h2>
                    <p class="text-gray-400 max-w-md animate-fade-in-up" style="animation-delay: 0.2s;">
                        Connect to your routers, manage hotspots, and troubleshoot network issues using natural language.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-3 mt-8 animate-fade-in-up" style="animation-delay: 0.3s;">
                        <button wire:click="$set('input', 'Generate 50 hotspot vouchers')" class="p-3 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl text-sm text-left transition text-gray-300 hover:text-white">
                            🎫 Generate Vouchers
                        </button>
                         <button wire:click="$set('input', 'Check router CPU load')" class="p-3 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl text-sm text-left transition text-gray-300 hover:text-white">
                            📊 Check CPU Status
                        </button>
                    </div>
                </div>
            @else
                @foreach($messages as $msg)
                    <div class="flex gap-6 {{ $msg['role'] === 'user' ? 'flex-row-reverse' : '' }} animate-fade-in-up">
                        <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center shadow-lg {{ $msg['role'] === 'user' ? 'bg-gradient-to-br from-indigo-500 to-blue-600' : 'bg-gradient-to-br from-gray-700 to-gray-800 border border-white/10' }}">
                            @if($msg['role'] === 'user')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            @else
                                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            @endif
                        </div>
                        
                        <div class="flex flex-col gap-2 max-w-[80%] md:max-w-[70%]">
                            <div class="px-6 py-4 rounded-2xl shadow-sm {{ $msg['role'] === 'user' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-tr-none' : 'glass rounded-tl-none text-gray-200' }}">
                                <div class="prose prose-invert prose-sm" 
                                     @if($msg['role'] === 'assistant')
                                        wire:stream="msg-{{ $msg['id'] }}-content"
                                        id="msg-{{ $msg['id'] }}-content"
                                     @endif
                                >
                                    @if(empty($msg['content']) && $msg['role'] === 'assistant')
                                        <div class="flex space-x-1 h-5 items-center">
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-75"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-150"></div>
                                        </div>
                                    @else
                                        {{ $msg['content'] }}
                                    @endif
                                </div>
                            </div>

                            <!-- DYNAMIC WIDGET RENDERER -->
                            @if(isset($msg['ui_widget']) && $msg['ui_widget'])
                                <div class="mt-4 p-5 rounded-2xl border border-white/10 bg-gray-800/80 shadow-xl w-full backdrop-blur-md">
                                    @php $widget = $msg['ui_widget']; @endphp
                                    
                                    <!-- FORM WIDGET -->
                                    @if($widget['type'] === 'form')
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-2 h-2 rounded-full bg-brand-400"></div>
                                            <span class="text-sm font-bold text-gray-200 uppercase tracking-widest">Action Required</span>
                                        </div>
                                        <form x-on:submit.prevent="$wire.handleWidgetAction('{{ $widget['tool'] }}', Object.fromEntries(new FormData($event.target)))" class="space-y-4">
                                            @foreach($widget['fields'] as $field)
                                                <div>
                                                    <label class="block text-xs uppercase font-bold text-gray-400 mb-2 pl-1">{{ $field['label'] }}</label>
                                                    <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ $field['default'] ?? '' }}" 
                                                           class="w-full bg-gray-900/50 border border-white/10 rounded-lg px-4 py-3 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all placeholder-gray-600 text-white">
                                                </div>
                                            @endforeach
                                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-lg text-sm font-bold shadow-lg shadow-brand-500/30 transition-all transform hover:scale-[1.02]">
                                                Execute Action
                                            </button>
                                        </form>
                                    
                                    <!-- SUCCESS WIDGET -->
                                    @elseif($widget['type'] === 'success')
                                        <div class="flex items-center gap-4 text-green-400 p-2">
                                            <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <span class="font-medium text-lg">{{ $widget['message'] }}</span>
                                        </div>
                                        @if(isset($widget['download_url']))
                                            <a href="{{ $widget['download_url'] }}" target="_blank" class="mt-4 block text-center w-full py-3 border border-green-500/30 text-green-400 rounded-lg hover:bg-green-500/10 transition text-sm font-medium uppercase tracking-wide">
                                                Download Output
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
            
            <!-- Optimistic Pending Message -->
            <div x-show="pendingMessage" class="flex gap-6 flex-row-reverse animate-fade-in-up">
                <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center shadow-lg bg-gradient-to-br from-indigo-500 to-blue-600">
                     <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div class="flex flex-col gap-2 max-w-[80%] md:max-w-[70%]">
                    <div class="px-6 py-4 rounded-2xl shadow-sm bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-tr-none">
                        <div class="prose prose-invert prose-sm" x-text="pendingMessage"></div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
             <div wire:loading wire:target="sendMessage" class="flex gap-4 animate-pulse">
                <div class="w-10 h-10 rounded-full bg-gray-800 border border-white/5 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                </div>
                <div class="flex items-center gap-1.5 h-10 px-4 bg-gray-800/50 rounded-2xl rounded-tl-none border border-white/5">
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce delay-75"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce delay-150"></div>
                </div>
            </div>
        </div>

        <!-- FIXED WATCHDOG OVERLAY (Moved outside container for z-index safety) -->
        <div x-show="watchdogStatus !== 'idle'" x-transition 
             class="fixed top-20 left-1/2 transform -translate-x-1/2 z-[100] flex flex-col items-center pointer-events-none">
            <div class="glass px-6 py-3 rounded-full flex items-center gap-4 border border-brand-500/50 shadow-2xl shadow-brand-500/30 backdrop-blur-xl bg-black/80 pointer-events-auto">
                <!-- Radar Animation -->
                <div class="relative w-3 h-3 flex-shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-brand-500"></span>
                </div>
                
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold font-mono tracking-widest text-brand-300 uppercase glow-text" 
                          x-text="watchdogStatus === 'executing' ? 'AUTONOMOUS AGENT ACTIVE' : 'VERIFYING COMPLETION...'">
                    </span>
                    <!-- Dynamic Command Display -->
                    <span class="text-[9px] text-gray-400 font-mono mt-0.5" x-text="$wire.activeToolName ? 'Cmd: ' + $wire.activeToolName : 'Processing...'"></span>
                </div>
                
                <!-- Stop Button -->
                <button type="button" @click="autoPilot = false; watchdogStatus = 'idle'" class="ml-2 p-1.5 hover:bg-red-500/20 rounded-full text-gray-400 hover:text-red-400 transition group border border-transparent hover:border-red-500/30" title="Emergency Stop">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-6 md:p-8 pt-0">
            <div class="max-w-4xl mx-auto relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-brand-500 to-purple-600 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
                <form 
                    wire:submit.prevent="submitMessage"
                    class="relative"
                >
                    <input 
                        wire:model.live="input" 
                        type="text" 
                        placeholder="اكتب رسالتك هنا..."
                        class="w-full bg-gray-900 text-white placeholder-gray-500 border border-white/10 rounded-xl pl-12 pr-14 py-4 focus:outline-none focus:ring-0 shadow-2xl transition-all"
                        autofocus
                    >
                    
                    <!-- Attachment Button -->
                    <div class="absolute left-2 top-2 p-2">
                        <label class="cursor-pointer text-gray-400 hover:text-white transition-colors">
                            <input type="file" wire:model="upload" class="hidden" accept="image/*,application/pdf,.txt">
                             <svg wire:loading.remove wire:target="upload" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                             <!-- Spinner during upload -->
                             <div wire:loading wire:target="upload" class="w-6 h-6 border-2 border-brand-500 border-t-transparent rounded-full animate-spin"></div>
                        </label>
                    </div>

                    <!-- Upload Errors -->
                    @error('upload') <span class="absolute bottom-full left-0 mb-2 text-xs text-red-500 bg-black/80 px-2 py-1 rounded">{{ $message }}</span> @enderror

                    <!-- Upload Preview -->
                    @if($upload)
                        <div class="absolute bottom-full left-0 mb-2 bg-gray-800 rounded-lg p-2 border border-white/10 flex items-center gap-2">
                            <span class="text-xs text-gray-300 truncate max-w-[150px]">{{ $upload->getClientOriginalName() }}</span>
                            <button type="button" wire:click="$set('upload', null)" class="text-red-400 hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endif
                    <button type="submit" class="absolute right-2 top-2 p-2 bg-brand-600 hover:bg-brand-500 rounded-lg transition-all text-white disabled:opacity-50 disabled:cursor-not-allowed shadow-lg" wire:loading.attr="disabled">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
                <div class="text-center mt-3 text-[10px] text-gray-500 uppercase tracking-widest font-mono">
                    AI-Net Orchestrator v1.0 • Secure Connection
                </div>
            </div>
        </div>
    </main>
</div>
