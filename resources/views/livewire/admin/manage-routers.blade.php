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
            <a href="/admin/routers" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/10 text-white border-white/5 shadow-inner">
                 <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg> Routers
            </a>
            <a href="/admin/providers" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                  <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg> AI Providers
            </a>
            <a href="/" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all mt-8 border-t border-white/5 pt-4">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg> Back to Chat
            </a>
        </nav>
    </aside>
    
    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/80 z-10 md:hidden backdrop-blur-sm" style="display: none;"></div>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto p-8 relative z-10">
        <!-- Mobile Toggle -->
        <div class="md:hidden flex items-center mb-6">
            <button @click="sidebarOpen = true" class="text-white bg-white/10 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg></button>
            <span class="ml-4 font-bold text-lg">Manage Routers</span>
        </div>

        <div class="flex justify-between items-center mb-8">
             <div>
                <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">Network Devices</h2>
                <p class="text-gray-400 text-sm mt-1">Manage physical router connections</p>
            </div>
            <button wire:click="create" class="px-5 py-2.5 bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl font-semibold text-sm shadow-lg shadow-brand-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Connect Router
            </button>
        </div>

        @if (session()->has('message'))
            <div class="glass border-l-4 border-green-500 text-green-400 p-4 rounded-r mb-6 flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($routers as $router)
                <div class="glass p-6 rounded-2xl border border-white/5 relative group hover:border-brand-500/30 transition-all duration-300">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-600/5 to-transparent opacity-0 group-hover:opacity-100 transition duration-500 rounded-2xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-gray-800/50 p-3 rounded-xl border border-white/5 group-hover:bg-brand-500/10 group-hover:border-brand-500/20 transition-colors">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-brand-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg>
                            </div>
                            <div class="flex items-center gap-2 px-2 py-1 rounded-full bg-gray-900 border border-white/5">
                                <div class="w-2 h-2 rounded-full {{ $router->status === 'online' ? 'bg-green-500 animate-pulse' : 'bg-gray-500' }}"></div>
                                <span class="text-[10px] font-mono text-gray-400 uppercase">{{ $router->status }}</span>
                            </div>
                        </div>

                        <h3 class="font-bold text-lg text-white mb-1">{{ $router->name }}</h3>
                        <div class="font-mono text-xs text-brand-300 mb-4">{{ $router->host }}:{{ $router->port }}</div>
                        
                        <div class="flex items-center gap-2 text-xs text-gray-500 bg-gray-900/50 rounded-lg p-2 mb-4 border border-white/5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            User: <span class="text-gray-300">{{ $router->username }}</span>
                        </div>

                        <div class="flex justify-end gap-2 mt-2 pt-4 border-t border-white/5 opacity-50 group-hover:opacity-100 transition-opacity">
                            <button wire:click="edit({{ $router->id }})" class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit
                            </button>
                            <button wire:click="delete({{ $router->id }})" class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1 transition-colors">
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
            <div class="glass p-8 rounded-2xl w-full max-w-md shadow-2xl border border-white/10 bg-gray-900/90">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">{{ $isEditing ? 'Edit Router Connection' : 'Connect New Router' }}</h3>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-white transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">Device Name</label>
                        <input type="text" wire:model="name" placeholder="e.g. Main Gateway" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                        @error('name') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs uppercase font-bold text-gray-400 mb-2">IP Address / Domain</label>
                            <input type="text" wire:model="host" placeholder="192.168.88.1 or router.domain.com" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-mono text-sm">
                            @error('host') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs uppercase font-bold text-gray-400 mb-2">Port</label>
                            <input type="number" wire:model="port" placeholder="8728" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-mono text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">API Username</label>
                        <input type="text" wire:model="username" placeholder="admin" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                        @error('username') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs uppercase font-bold text-gray-400 mb-2">API Password {{ $isEditing ? '(Leave blank to keep)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                        @error('password') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-white/5">
                    <button wire:click="closeModal" class="px-5 py-2.5 text-gray-400 hover:text-white text-sm font-medium transition-colors">Cancel</button>
                    <button wire:click="store" class="px-5 py-2.5 bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 rounded-xl font-bold text-sm shadow-lg shadow-brand-500/20 transition-all transform hover:scale-[1.02]">{{ $isEditing ? 'Update Connection' : 'Save Connection' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
