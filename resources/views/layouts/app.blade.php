<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI-Net Orchestrator</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS (bundled with Livewire usually, but explicit request for dynamic UI) -->
    
    @livewireStyles
</head>
<body class="font-sans antialiased text-white h-screen overflow-hidden selection:bg-brand-500 selection:text-white bg-gray-900">
    <!-- Animated Background Mesh -->
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-blue-600/20 blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-purple-600/20 blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    {{ $slot }}

    @livewireScripts
</body>
</html>
