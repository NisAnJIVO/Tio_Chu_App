<!DOCTYPE html>
<html lang="es" class="h-screen overflow-hidden bg-black" style="background-color: #000000;">
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
<body class="h-screen overflow-hidden font-sans antialiased text-zinc-100 selection:bg-[#F5B81C] selection:text-black" style="background-color: #000000;">

<div class="h-screen w-full flex overflow-hidden relative" style="background-color: #000000;">
    <!-- Iluminación sutil de fondo -->
    <div class="fixed -top-24 -left-24 w-[480px] h-[480px] bg-amber-500/5 rounded-full blur-[140px] pointer-events-none z-0"></div>
    <div class="fixed top-1/3 -right-24 w-[540px] h-[540px] bg-blue-600/5 rounded-full blur-[160px] pointer-events-none z-0"></div>

    <!-- Sidebar de Tío Chu (Estático y Fijo) -->
    @include('layouts.sidebar')

    <!-- Main Content Area (Única área que hace scroll) -->
    <main class="flex-1 h-screen overflow-y-auto relative z-10 p-6 lg:p-8" style="background-color: #090a0f;">
        <!-- Alertas Flash -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-xs text-emerald-300 rounded-2xl flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-xs text-rose-300 rounded-2xl">
                <div class="flex items-center gap-2 mb-1.5 font-bold text-rose-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Por favor corrige los siguientes errores:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
// Refrescar token CSRF antes de cualquier submit de formulario
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', async function(e) {
            const method = (form.querySelector('[name="_method"]')?.value || form.method || 'GET').toUpperCase();
            if (method === 'GET') return;

            if (form.dataset.submitting === 'true') return;

            e.preventDefault();
            form.dataset.submitting = 'true';

            try {
                const res = await fetch('/csrf-refresh', { credentials: 'same-origin' });
                const data = await res.json();

                form.querySelectorAll('input[name="_token"]').forEach(function(inp) {
                    inp.value = data.token;
                });

                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) metaTag.setAttribute('content', data.token);
            } catch (err) {
                console.warn('No se pudo refrescar el CSRF token:', err);
            }

            HTMLFormElement.prototype.submit.call(form);
        });
    });
});
</script>
</body>
</html>
