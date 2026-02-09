<div class="flex h-screen bg-gray-900 text-white overflow-hidden bg-[url('https://tailwindcss.com/_next/static/media/hero-dark.93c1130d.png')] bg-cover bg-center" x-data="{ sidebarOpen: false }">
    <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm z-0"></div>

    <!-- Admin Sidebar (Same as dashboard) -->
    <aside class="w-72 glass border-r border-white/5 flex flex-col fixed md:relative z-20 h-full transition-transform duration-300 md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
         <div class="p-6 border-b border-white/5 flex items-center justify-between gap-3">
             <div class="flex items-center gap-3">
                 <div class="w-8 h-8 rounded bg-gradient-to-br from-brand-400 to-purple-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                     <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                Admin Panel
            </h1>
        </div>
        <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider px-2 mb-2">Controls</div>
            <a href="/admin/dashboard" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg> Overview
            </a>
            <a href="/admin/routers" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                 <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg> Routers
            </a>
            <a href="/admin/providers" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/10 text-white border-white/5 shadow-inner">
                  <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg> AI Providers
            </a>
            <a href="/admin/knowledge-base" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> Knowledge Base
            </a>
            <a href="/" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all mt-8 border-t border-white/5 pt-4">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> Back to Chat
            </a>
        </nav>
    </aside>

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/80 z-10 md:hidden backdrop-blur-sm" style="display: none;"></div>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto p-8 relative z-10 glass-content">
        <!-- Mobile Toggle -->
        <div class="md:hidden flex items-center mb-6">
            <button @click="sidebarOpen = true" class="text-white bg-white/10 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg></button>
            <span class="ml-4 font-bold text-lg">Manage Providers</span>
        </div>

        <div class="flex justify-between items-center mb-6">
            <div>
                 <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">AI Models</h2>
                 <p class="text-gray-400 text-sm mt-1">Configure your AI providers (LLMs)</p>
            </div>
            <button wire:click="create" class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 rounded-xl font-semibold text-sm shadow-lg shadow-green-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Connect New Brain
            </button>
        </div>

        @if (session()->has('message'))
             <div class="glass border-l-4 border-green-500 text-green-400 p-4 rounded-r mb-6 flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($providers as $provider)
                <div class="glass p-6 rounded-2xl border border-white/5 relative group hover:border-green-500/30 transition-all duration-300">
                     <div class="absolute inset-0 bg-gradient-to-br from-green-600/5 to-transparent opacity-0 group-hover:opacity-100 transition duration-500 rounded-2xl"></div>

                     <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-gray-800/50 p-3 rounded-xl border border-white/5 group-hover:bg-green-500/10 group-hover:border-green-500/20 transition-colors">
                                 <svg class="w-6 h-6 text-gray-400 group-hover:text-green-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div class="w-2 h-2 rounded-full {{ $provider->is_active ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></div>
                        </div>
                        
                        <h3 class="font-bold text-lg text-white mb-1">{{ $provider->name }}</h3>
                        <div class="text-xs text-green-300 bg-green-500/10 inline-block px-2 py-1 rounded mb-4 border border-green-500/20">
                            {{ $provider->model_identifier }}
                        </div>
                        
                        <div class="text-xs text-gray-400 bg-gray-900/50 rounded-lg p-3 mb-4 break-all font-mono border border-white/5">
                            {{ $provider->base_url }}
                        </div>

                        <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-white/5 opacity-50 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $provider->id }})" class="text-gray-400 hover:text-white text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit
                            </button>
                            <button wire:click="delete({{ $provider->id }})" class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 animate-fade-in-up">
            <div class="glass p-8 rounded-2xl w-full max-w-md shadow-2xl border border-white/10 bg-gray-900/90 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">{{ $isEditing ? 'Edit Brain' : 'Connect New Brain' }}</h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-white transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="space-y-5">
                    <!-- Presets -->
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">Quick Presets</label>
                        <div class="flex gap-2">
                             <button wire:click="applyPreset('gemini')" class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30 hover:bg-blue-500/40 transition">Google Gemini</button>
                             <button wire:click="applyPreset('openai')" class="px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30 hover:bg-green-500/40 transition">OpenAI</button>
                             <button wire:click="applyPreset('mistral')" class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-400 border border-purple-500/30 hover:bg-purple-500/40 transition">Mistral AI</button>
                             <button wire:click="applyPreset('ollama')" class="px-3 py-1 rounded-full text-xs font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30 hover:bg-orange-500/40 transition">Ollama</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">Display Name</label>
                        <input type="text" wire:model="name" placeholder="e.g. GPT-4 Turbo" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all">
                        @error('name') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Provider Type Selection -->
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">Provider Type</label>
                        <select wire:model.live="type" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all text-sm">
                            <option value="standard">Standard (Chat Completion)</option>
                            <option value="mistral_agent">Mistral Agent (Specialized)</option>
                            <option value="gemini_interaction">Google Gemini Interactions (Advanced)</option>
                        </select>
                    </div>

                    @if($type === 'mistral_agent')
                        <div class="p-4 bg-purple-900/20 border border-purple-500/30 rounded-xl space-y-4">
                            <h4 class="text-purple-400 font-bold text-xs uppercase tracking-wider mb-2">Mistral Agent Config</h4>
                            <p class="text-xs text-gray-400">Using endpoint: <code>/agents/completions</code>. The Model ID will be the <strong>Agent ID</strong>.</p>
                        </div>
                    @endif

                    @if($type === 'gemini_interaction')
                        <div class="p-4 bg-blue-900/20 border border-blue-500/30 rounded-xl space-y-4">
                            <h4 class="text-blue-400 font-bold text-xs uppercase tracking-wider mb-2">Agent Configuration</h4>
                            
                            <div>
                                <label class="block text-xs text-gray-400 mb-2">Select Agent</label>
                                <select wire:model="selectedAgent" class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-white text-sm">
                                    <option value="deep-research-pro-preview-12-2025">Deep Research Agent</option>
                                    <option value="gemini-2.5-flash-thinking">Thinking Model Agent</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-400 mb-2">Thinking Level</label>
                                <select wire:model="thinkingLevel" class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-white text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">Base URL</label>
                        <input type="text" wire:model="base_url" placeholder="https://api.openai.com/v1" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all text-sm font-mono">
                        @error('base_url') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">API Key</label>
                        <div class="flex gap-2">
                            <input type="password" wire:model="api_key" placeholder="sk-..." class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all font-mono">
                             <button wire:click="fetchModels" wire:loading.attr="disabled" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl text-white text-xs font-bold whitespace-nowrap transition-colors flex items-center gap-2">
                                <span wire:loading.remove wire:target="fetchModels">Fetch Models</span>
                                <span wire:loading wire:target="fetchModels">Fetching...</span>
                            </button>
                        </div>
                         @if (session()->has('error'))
                             <span class="text-red-500 text-xs block mt-1">{{ session('error') }}</span>
                         @endif
                         @if (session()->has('success'))
                             <span class="text-green-500 text-xs block mt-1">{{ session('success') }}</span>
                         @endif
                    </div>

                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">
                            {{ $type === 'mistral_agent' ? 'Agent ID' : ($type === 'gemini_interaction' ? 'Agent Name / Model' : 'Model ID') }}
                        </label>
                        
                        @if(!empty($fetchedModels))
                            <select wire:model="model_identifier" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all text-sm font-mono appearance-none">
                                <option value="">Select a Model</option>
                                @foreach($fetchedModels as $model)
                                    <option value="{{ $model }}">{{ $model }}</option>
                                @endforeach
                            </select>
                             <div class="text-xs text-gray-500 mt-1">Select from fetched models or type manually below if missing.</div>
                        @else
                             <input type="text" wire:model="model_identifier" placeholder="{{ $type === 'mistral_agent' ? 'ag:...' : 'gpt-4-turbo' }}" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all text-sm font-mono">
                        @endif
                        
                        @error('model_identifier') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- System Instruction -->
                    <div>
                         <label class="block text-xs uppercase font-bold text-gray-400 mb-2">System Instruction (Optional)</label>
                         <textarea wire:model="system_instruction" rows="3" placeholder="You are a helpful assistant..." class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-all text-sm"></textarea>
                         <div class="text-xs text-gray-500 mt-1">Define how the AI should behave (e.g. 'You are a MikroTik expert').</div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-white/5">
                    <button wire:click="closeModal" class="px-5 py-2.5 text-gray-400 hover:text-white text-sm font-medium transition-colors">Cancel</button>
                    <button wire:click="store" class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 rounded-xl font-bold text-sm shadow-lg shadow-green-500/20 transition-all transform hover:scale-[1.02]">Save Connection</button>
                </div>
            </div>
        </div>
    @endif
</div>
