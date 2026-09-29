<!DOCTYPE html>
<html lang="es" class="h-full bg-[#040507]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión') — Discoteca Tío Chu</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts y Estilos Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-100 selection:bg-amber-400 selection:text-zinc-950 overflow-x-hidden">

<div class="min-h-screen bg-[#040507] text-slate-100 relative overflow-hidden flex font-sans antialiased">
    <!-- Luces ambientales de fondo (Obligatorias para el Liquid Glass) -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-500/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/2 -right-32 w-96 h-96 bg-blue-600/10 rounded-full blur-[150px] pointer-events-none"></div>
    <div class="fixed -top-24 -left-24 w-[480px] h-[480px] bg-amber-500/15 rounded-full blur-[130px] pointer-events-none z-0"></div>
    <div class="fixed top-1/3 -right-24 w-[540px] h-[540px] bg-blue-600/15 rounded-full blur-[150px] pointer-events-none z-0"></div>
    <div class="fixed -bottom-24 left-1/4 w-[450px] h-[450px] bg-emerald-500/10 rounded-full blur-[130px] pointer-events-none z-0"></div>

    <!-- Sidebar de Tío Chu -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 relative z-10 overflow-y-auto p-6 lg:p-8">
        <!-- Alertas Flash -->
        @if(session('success'))
            <div class="mb-6 p-4 backdrop-blur-2xl bg-emerald-500/10 border border-emerald-500/30 text-xs text-emerald-300 rounded-2xl shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 backdrop-blur-2xl bg-rose-500/10 border border-rose-500/30 text-xs text-rose-300 rounded-2xl shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>
