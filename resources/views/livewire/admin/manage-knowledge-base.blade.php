<div class="flex h-screen bg-gray-900 text-white overflow-hidden bg-[url('https://tailwindcss.com/_next/static/media/hero-dark.93c1130d.png')] bg-cover bg-center" x-data="{ sidebarOpen: false }">
    <div class="absolute inset-0 bg-gray-900/90 backdrop-blur-sm z-0"></div>

    <!-- Admin Sidebar -->
    <aside class="w-72 glass border-r border-white/5 flex flex-col fixed md:relative z-20 h-full transition-transform duration-300 md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="p-6 border-b border-white/5 flex items-center justify-between gap-3">
             <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-purple-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                    Mcp Knowledge
                </h1>
            </div>
             <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <x-admin-nav />
    </aside>
    
    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/80 z-10 md:hidden backdrop-blur-sm" style="display: none;"></div>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto p-8 relative z-10 glass-content">
         <!-- Mobile Toggle -->
        <div class="md:hidden flex items-center mb-6">
            <button @click="sidebarOpen = true" class="text-white bg-white/10 p-2 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg></button>
            <span class="ml-4 font-bold text-lg">Knowledge Base</span>
        </div>

        <div class="flex justify-between items-center mb-6">
            <div>
                 <h2 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">Knowledge Base</h2>
                 <p class="text-gray-400 text-sm mt-1">Manage files for RAG and Fine-tuning on Mistral AI.</p>
            </div>
            
            <div class="w-64">
                <select wire:model.live="selectedProviderId" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-white text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none transition-all">
                    @foreach($providers as $provider)
                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if (session()->has('message'))
             <div class="glass border-l-4 border-green-500 text-green-400 p-4 rounded-r mb-6 flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif
        
        @if($errorMessage)
             <div class="glass border-l-4 border-red-500 text-red-400 p-4 rounded-r mb-6 flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ $errorMessage }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upload Area -->
            <div class="lg:col-span-1">
                <div class="glass p-6 rounded-2xl border border-white/5">
                    <h3 class="font-bold text-lg text-white mb-4">Upload New File</h3>
                    
                    <div 
                        x-data="{ isDropping: false, progress: 0 }"
                        x-on:livewire-upload-start="isDropping = true; progress = 0"
                        x-on:livewire-upload-finish="isDropping = false; progress = 100"
                        x-on:livewire-upload-error="isDropping = false; progress = 0"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                        class="border-2 border-dashed border-white/10 rounded-xl p-8 text-center transition-colors relative"
                        :class="isDropping ? 'border-purple-500 bg-purple-500/10' : 'hover:border-purple-500/50 hover:bg-white/5'"
                    >
                        <input type="file" wire:model="upload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        <div class="pointer-events-none">
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="text-sm text-gray-300 font-medium">Click or Drag file here</p>
                            <p class="text-xs text-gray-500 mt-1">TXT, PDF, MD (Max 10MB)</p>
                        </div>

                        <!-- Progress Bar -->
                        <div x-show="isDropping" class="mt-4 transition-all duration-300">
                             <div class="w-full bg-gray-700 rounded-full h-1.5">
                                <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                            </div>
                            <div class="text-xs text-purple-400 mt-1">Uploading...</div>
                        </div>
                    </div>

                    @if($upload)
                        <div class="mt-4 flex items-center justify-between bg-white/5 p-3 rounded-lg">
                            <span class="text-sm text-gray-300 truncate max-w-[150px]">{{ $upload->getClientOriginalName() }}</span>
                            <button wire:click="saveFile" class="text-xs bg-purple-600 hover:bg-purple-500 text-white px-3 py-1.5 rounded-lg transition-colors">
                                Confirm Upload
                            </button>
                        </div>
                    @endif
                    
                    @error('upload') <span class="text-red-500 text-xs block mt-2">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- File List -->
            <div class="lg:col-span-2">
                <div class="glass rounded-2xl border border-white/5 overflow-hidden">
                    <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center bg-white/5">
                        <h3 class="font-bold text-white">Cloud Files</h3>
                        <button wire:click="loadFiles" class="text-gray-400 hover:text-white transition spin-on-hover">
                            <svg class="w-4 h-4 {{ $isLoading ? 'animate-spin text-purple-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-400">
                            <thead class="bg-black/20 text-gray-200 uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-3">File Name</th>
                                    <th class="px-6 py-3">ID</th>
                                    <th class="px-6 py-3">Size</th>
                                    <th class="px-6 py-3">Created</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($files as $file)
                                    <tr class="hover:bg-white/5 transition-colors group">
                                        <td class="px-6 py-4 font-medium text-white flex items-center gap-2">
                                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            {{ $file['filename'] ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4 font-mono text-xs select-all">{{ $file['id'] }}</td>
                                        <td class="px-6 py-4">{{ number_format(($file['bytes'] ?? 0) / 1024, 2) }} KB</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::createFromTimestamp($file['created_at'] ?? 0)->diffForHumans() }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <button wire:click="deleteFile('{{ $file['id'] }}')" wire:confirm="Are you sure you want to delete this file?" class="text-red-400 hover:text-red-300 transition-colors opacity-0 group-hover:opacity-100 flex items-center gap-1 ml-auto">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                            No files found in the cloud.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
