<!DOCTYPE html>
<html lang="es" class="h-screen overflow-hidden dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión') — Discoteca Tío Chu</title>

    <!-- Modo Oscuro Oficial Tío Chu -->
    <script>
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
        try { localStorage.removeItem('tiochu_theme'); } catch(e) {}
    </script>

    <!-- Favicon Oficial Tío Chu -->
    <link rel="icon" type="image/png" href="{{ asset('images/LogoTioChu.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/LogoTioChu.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans (Única Fuente Oficial) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts y Estilos Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden font-sans antialiased theme-text-primary selection:bg-[#F5B81C] selection:text-black theme-app-bg transition-colors duration-200">

<div class="h-screen w-full max-w-full flex overflow-hidden overflow-x-hidden relative theme-app-bg">
    <!-- Sidebar de Tío Chu (Estático y Fijo) -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 w-full max-w-full h-screen overflow-y-auto overflow-x-hidden p-2.5 sm:p-4 lg:p-6 theme-main-bg transition-colors duration-200 flex flex-col">
        <!-- Alerta Discreta Estilo iOS (Bottom-Right Toast sin bloqueo visual) -->
        <!-- Alertas Efímeras Estilo iOS (Bottom-Center Toasts Minimalistas) -->
        @if(session('success'))
            <div id="flash-success-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2 bg-black/95 border border-zinc-800 text-xs text-zinc-200 rounded-full shadow-2xl backdrop-blur-md flex items-center gap-2.5 transition-all duration-300 pointer-events-auto">
                <span class="w-2 h-2 rounded-full bg-[#F5B81C] shrink-0"></span>
                <span class="font-medium truncate max-w-sm sm:max-w-md">{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('flash-success-toast')?.remove()" class="text-zinc-500 hover:text-white text-sm ml-1 cursor-pointer">&times;</button>
            </div>
            <script>
                setTimeout(function() {
                    const toast = document.getElementById('flash-success-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translate(-50%, 8px)';
                        setTimeout(() => toast.remove(), 250);
                    }
                }, 1800);
            </script>
        @endif
        @if(session('error') || $errors->any())
            <div id="flash-error-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2 bg-black/95 border border-rose-900/60 text-xs text-rose-300 rounded-full shadow-2xl backdrop-blur-md flex items-center gap-2.5 transition-all duration-300 pointer-events-auto">
                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                <span class="font-medium truncate max-w-sm sm:max-w-md">
                    {{ session('error') ?? $errors->first() }}
                </span>
                <button type="button" onclick="document.getElementById('flash-error-toast')?.remove()" class="text-zinc-500 hover:text-white text-sm ml-1 cursor-pointer">&times;</button>
            </div>
            <script>
                setTimeout(function() {
                    const toast = document.getElementById('flash-error-toast');
                    if (toast) {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translate(-50%, 8px)';
                        setTimeout(() => toast.remove(), 250);
                    }
                }, 2200);
            </script>
        @endif

        @yield('content')
    </main>
</div>

<script>
// Refrescar token CSRF antes de cualquier submit de formulario
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', async function(e) {
            if (form.dataset.ajax === 'true' || form.getAttribute('data-ajax') === 'true') return;

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
