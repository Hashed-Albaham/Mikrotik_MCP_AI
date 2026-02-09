<div class="flex h-screen bg-gray-900 text-white overflow-hidden bg-[url('https://tailwindcss.com/_next/static/media/hero-dark.93c1130d.png')] bg-cover bg-center" x-data="{ sidebarOpen: false }">
  <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm z-0"></div>

  <!-- Admin Sidebar -->
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
            
            <a href="/admin/dashboard" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent {{ request()->is('admin/dashboard') ? 'bg-white/10 text-white border-white/5 shadow-inner' : 'text-gray-400 hover:text-white hover:bg-white/5 transition-all' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/dashboard') ? 'text-brand-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Overview
            </a>

            <a href="/admin/routers" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent {{ request()->is('admin/routers') ? 'bg-white/10 text-white border-white/5 shadow-inner' : 'text-gray-400 hover:text-white hover:bg-white/5 transition-all' }}">
                 <svg class="w-5 h-5 {{ request()->is('admin/routers') ? 'text-brand-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 01-2 2v4a2 2 0 012 2h14a2 2 0 012-2v-4a2 2 0 01-2-2m-2-4h.01M17 16h.01"></path></svg>
                Routers
            </a>

            <a href="/admin/providers" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent {{ request()->is('admin/providers') ? 'bg-white/10 text-white border-white/5 shadow-inner' : 'text-gray-400 hover:text-white hover:bg-white/5 transition-all' }}">
                  <svg class="w-5 h-5 {{ request()->is('admin/providers') ? 'text-brand-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                AI Providers
            </a>

            <a href="/admin/knowledge-base" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl border border-transparent {{ request()->is('admin/knowledge-base') ? 'bg-white/10 text-white border-white/5 shadow-inner' : 'text-gray-400 hover:text-white hover:bg-white/5 transition-all' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/knowledge-base') ? 'text-purple-400' : 'text-gray-500 group-hover:text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Knowledge Base (Mistral)
            </a>

            <a href="/" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all mt-8 border-t border-white/5 pt-4">
                <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                Back to Chat
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
            <span class="ml-4 font-bold text-lg">Admin Panel</span>
        </div>

        <h2 class="text-3xl font-bold mb-8 bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-500">System Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat Card 1 -->
            <div class="glass p-6 rounded-2xl border border-white/5 relative group overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-brand-600/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">Total Routers</div>
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-white">{{ $totalRouters }}</div>
                    <div class="mt-2 text-xs text-gray-500">Managed Devices</div>
                </div>
            </div>

             <!-- Stat Card 2 -->
             <div class="glass p-6 rounded-2xl border border-white/5 relative group overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-green-600/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">Active Brains</div>
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="text-4xl font-bold text-white">{{ $activeProviders }}</div>
                         <div class="text-lg text-gray-500 font-medium mb-1">/ {{ $totalProviders }}</div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">Connected AI Models</div>
                </div>
            </div>

             <!-- Stat Card 3 -->
             <div class="glass p-6 rounded-2xl border border-white/5 relative group overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                <div class="relative z-10">
                   <div class="flex items-center justify-between mb-4">
                        <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">Vouchers</div>
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        </div>
                    </div>
                    <div class="text-4xl font-bold text-white">{{ $totalVouchers }}</div>
                     <div class="mt-2 text-xs text-gray-500">Lifetime Generated</div>
                </div>
            </div>
        </div>
    </main>
</div>
