<!DOCTYPE html>
<html lang="es" class="h-full bg-[#08090d]">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso Ejecutivo — Discoteca Tío Chu</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full flex items-center justify-center p-4 antialiased bg-[#08090d] text-zinc-200 selection:bg-amber-400 selection:text-zinc-950 font-sans relative overflow-hidden">

    <!-- Iluminación Ambiental de Fondo (Liquid Glass Mesh Orbs) -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[550px] h-[550px] bg-amber-500/10 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-32 -left-20 w-[450px] h-[450px] bg-blue-600/10 rounded-full blur-[130px]"></div>
        <div class="absolute top-1/3 -right-20 w-[400px] h-[400px] bg-emerald-500/5 rounded-full blur-[130px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        <!-- Mensajes de Estado / Alertas -->
        @if(session('success'))
            <div class="mb-5 p-3.5 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 text-xs text-emerald-300 text-center backdrop-blur-xl shadow-lg flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-2xl border border-rose-500/30 bg-rose-500/10 text-xs text-rose-300 backdrop-blur-xl shadow-lg space-y-1">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Card Principal con Efecto Liquid Glass -->
        <div class="glass-panel rounded-3xl p-8 sm:p-9 border border-white/10 shadow-2xl backdrop-blur-2xl bg-white/[0.03]">
            
            <!-- Cabecera con Logotipo y Branding Oficial Tío Chu -->
            <div class="mb-7 pb-6 border-b border-white/10 text-center">
                <div class="w-14 h-14 mx-auto mb-3.5 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-zinc-950 font-black text-lg flex items-center justify-center tracking-tight shadow-xl shadow-amber-500/25 border border-amber-300/40">
                    TC
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[10px] font-bold tracking-widest uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-2">
                    Acceso Ejecutivo
                </span>
                <h1 class="text-xl font-black tracking-tight text-white uppercase">DISCOTECA TÍO CHU</h1>
                <p class="text-xs text-zinc-400 mt-1">Control de Inventario, Barras & Arqueo de Caja</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider mb-2">
                        Credencial de Acceso (Correo o Usuario)
                    </label>
                    <input type="text" name="email" id="email" required autofocus 
                        value="{{ old('email', 'DonLudo@gmail.com') }}"
                        placeholder="DonLudo@gmail.com"
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold text-zinc-300 uppercase tracking-wider">
                            Contraseña
                        </label>
                        <span class="text-[10px] font-mono text-zinc-500">Clave de Seguridad</span>
                    </div>
                    <input type="password" name="password" id="password" required 
                        placeholder="••••••••"
                        value="tiochu123"
                        class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all font-mono">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer text-zinc-300 hover:text-white transition-colors">
                        <input type="checkbox" name="remember" checked
                            class="w-4 h-4 rounded border-white/20 bg-zinc-900 text-amber-500 focus:ring-amber-500/50 focus:ring-offset-0">
                        <span class="text-xs font-medium">Mantener sesión activa en este equipo</span>
                    </label>
                </div>

                <div class="pt-3">
                    <button type="submit" id="submit-btn"
                        class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-extrabold tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/25 active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-zinc-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Ingresar al Sistema</span>
                    </button>
                </div>
            </form>

            <!-- Acceso Rápido para Administración -->
            <div class="mt-6 pt-5 border-t border-white/10">
                <div class="p-3 rounded-xl bg-amber-500/[0.06] border border-amber-500/20 text-xs flex items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-bold text-amber-300 uppercase tracking-wider">Acceso Rápido Administrador</p>
                        <p class="text-[11px] text-zinc-400 font-mono">DonLudo@gmail.com / tiochu123</p>
                    </div>
                    <button type="button" onclick="setDemoCredentials()" 
                            class="px-2.5 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-[11px] font-bold tracking-wider transition-all border border-amber-500/30">
                        Autocompletar
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-[11px] text-zinc-500 font-mono tracking-wider">SISTEMA PRIVADO DE ALTA GAMA &bull; DISCOTECA TÍO CHU</p>
        </div>
    </div>

    <!-- Script de auto-mantenimiento de sesión para evitar 419 -->
    <script>
        function setDemoCredentials() {
            document.getElementById('email').value = 'DonLudo@gmail.com';
            document.getElementById('password').value = 'tiochu123';
        }

        // Mantener la sesión y el token CSRF siempre vivos si la pestaña permanece abierta
        setInterval(function() {
            fetch('{{ route("login") }}', { method: 'HEAD' })
                .catch(function(err) { console.log('Keep-alive error:', err); });
        }, 10 * 60 * 1000); // Cada 10 minutos
    </script>
</body>

</html>