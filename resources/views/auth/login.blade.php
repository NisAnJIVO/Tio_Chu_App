<!DOCTYPE html>
<html lang="es" class="h-full" style="background-color: #000000;">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tío Chu</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Respaldo CDN de GSAP para que funcione inmediatamente en cualquier máquina -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

    <style>
        /* Optimización de aceleración por hardware GPU para 60/120 FPS */
        #splash-screen, #left-panel, #right-panel, #right-panel-img {
            will-change: transform, opacity;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
    </style>
</head>

<body class="h-full text-zinc-100 font-sans antialiased selection:bg-[#F5B81C] selection:text-black overflow-x-hidden" style="background-color: #000000;">

    <!-- PANTALLA DE CARGA / SPLASH INICIAL (Logo Central de Tío Chu) -->
    <div id="splash-screen" class="fixed inset-0 z-50 flex items-center justify-center" style="background-color: #000000;">
        <div class="text-center px-4">
            <img id="splash-logo" 
                 src="{{ asset('images/LogoTioChu.png') }}" 
                 alt="Tío Chu" 
                 class="w-48 h-48 sm:w-60 sm:h-60 object-contain mx-auto select-none opacity-0">
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL: PUERTAS Y ESCENARIO CENTRAL -->
    <div class="min-h-screen w-full relative overflow-hidden" style="background-color: #000000;">
        
        <!-- ETAPA CENTRAL AL INICIAR SESIÓN: LOGO Y BARRITA DE CARGA AMARILLA (z-10, DETRÁS DE LAS PUERTAS) -->
        <div id="submit-loading-stage" class="absolute inset-0 z-10 flex flex-col items-center justify-center pointer-events-none" style="background-color: #000000;">
            <div class="text-center px-4 flex flex-col items-center">
                <img id="submit-logo" 
                     src="{{ asset('images/LogoTioChu.png') }}" 
                     alt="Tío Chu" 
                     class="w-44 h-44 sm:w-56 sm:h-56 object-contain mx-auto select-none opacity-0">
                
                <!-- Barrita de carga minimalista en amarillo oficial -->
                <div id="loading-bar-container" class="w-56 sm:w-64 h-1.5 bg-zinc-900 border border-zinc-800 rounded-full overflow-hidden mt-6 opacity-0">
                    <div id="loading-bar-fill" class="h-full rounded-full w-0" style="background-color: #F5B81C;"></div>
                </div>
            </div>
        </div>

        <!-- LAS DOS PUERTAS DESLIZANTES (z-20, CUBREN EL CENTRO HASTA QUE SE ABREN) -->
        <div id="doors-wrapper" class="min-h-screen w-full flex flex-col lg:flex-row relative z-20 pointer-events-auto">
            
            <!-- HOJA IZQUIERDA DE LA PUERTA: Formulario Estilo iOS Minimalista -->
            <div id="left-panel" class="w-full lg:w-1/2 min-h-screen flex flex-col justify-center items-center px-6 sm:px-12 py-10" style="background-color: #000000; will-change: transform;" inert>
                
                <div class="w-full max-w-sm flex flex-col items-center">
                    
                    <!-- Logo Oficial -->
                    <div id="form-logo" class="text-center mb-6 -mt-8 sm:-mt-12">
                        <img src="{{ asset('images/LogoTioChu.png') }}" 
                             alt="Logo Tío Chu" 
                             class="w-40 h-40 sm:w-48 sm:h-48 md:w-52 md:h-52 object-contain mx-auto select-none">
                    </div>

                    @if($errors->any())
                        <div class="w-full mb-4 text-center">
                            <p class="text-xs text-rose-400 font-medium">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('login') }}" class="w-full space-y-4" id="login-form" autocomplete="off">
                        @csrf

                        <!-- Campo Correo -->
                        <div class="space-y-1.5 form-field">
                            <label for="email" class="block text-xs font-medium text-zinc-400">
                                Correo electrónico
                            </label>
                            <input type="text" 
                                   name="email" 
                                   id="email" 
                                   required 
                                   autocomplete="username"
                                   value="{{ old('email', 'DonLudo@gmail.com') }}"
                                   placeholder="correo@ejemplo.com"
                                   class="w-full px-4 py-3 rounded-xl bg-black border border-zinc-800 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-[#F5B81C] focus:ring-1 focus:ring-[#F5B81C] transition-colors">
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="space-y-1.5 form-field">
                            <label for="password" class="block text-xs font-medium text-zinc-400">
                                Contraseña
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   required 
                                   autocomplete="current-password"
                                   value="tiochu123"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 rounded-xl bg-black border border-zinc-800 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-[#F5B81C] focus:ring-1 focus:ring-[#F5B81C] transition-colors">
                        </div>

                        <!-- Recordar sesión -->
                        <div class="flex items-center justify-between pt-1 form-field">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" 
                                       name="remember" 
                                       checked
                                       class="w-4 h-4 rounded border-zinc-700 bg-black text-[#F5B81C] focus:ring-0 focus:ring-offset-0 accent-[#F5B81C]">
                                <span class="text-xs text-zinc-400">Recordar sesión</span>
                            </label>
                        </div>

                        <!-- Botón de Ingreso iOS Minimalista Dorado -->
                        <div class="pt-2 form-field">
                            <button type="submit" 
                                    id="submit-btn"
                                    class="w-full py-3.5 px-4 bg-[#F5B81C] hover:bg-[#e6ab17] active:bg-[#d69d0f] text-black font-semibold text-sm rounded-xl transition-all cursor-pointer text-center shadow-lg shadow-[#F5B81C]/10">
                                Iniciar sesión
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- HOJA DERECHA DE LA PUERTA: Imagen fondoTioChuLogo.png -->
            <div id="right-panel" class="hidden lg:block lg:w-1/2 min-h-screen relative overflow-hidden" style="background-color: #000000; will-change: transform;">
                <img id="right-panel-img"
                     src="{{ asset('images/fondoTioChuLogo.png') }}" 
                     alt="Tío Chu Fondo" 
                     class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-black via-transparent to-transparent w-24"></div>
            </div>

        </div>

    </div>

    <!-- Toast de Notificación Sutil Minimalista -->
    @if(session('success'))
        <div id="toast-notification" 
             class="fixed bottom-6 left-1/2 -translate-x-1/2 px-4 py-2 rounded-full bg-zinc-950/90 border border-white/10 text-zinc-400 text-xs shadow-2xl backdrop-blur-md pointer-events-none z-50 transition-opacity duration-300">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                const toast = document.getElementById('toast-notification');
                if (toast) {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 1800);
        </script>
    @endif

    <script>
        // Mantener sesión viva para evitar token CSRF vencido
        setInterval(function() {
            fetch('{{ route("login") }}', { method: 'HEAD' })
                .catch(function(err) { console.log('Keep-alive error:', err); });
        }, 10 * 60 * 1000);

        // Core de Animación de Puerta Corrediza Doble (GSAP GPU Accelerated)
        window.addEventListener('load', () => {
            const gsap = window.gsap;

            if (!gsap) {
                const splash = document.getElementById('splash-screen');
                if (splash) splash.style.display = 'none';
                const leftPanel = document.getElementById('left-panel');
                if (leftPanel) leftPanel.removeAttribute('inert');
                return;
            }

            // Preparar las puertas fuera de la pantalla antes de la animación:
            gsap.set('#left-panel', { xPercent: -100 });
            gsap.set('#right-panel', { xPercent: 100 });

            // 1. TIMELINE DE ENTRADA (Cierre de Puertas desde los extremos)
            const tl = gsap.timeline({
                onComplete: () => {
                    const leftPanel = document.getElementById('left-panel');
                    if (leftPanel) leftPanel.removeAttribute('inert');
                }
            });

            // Entrada del logo splash central
            tl.to('#splash-logo', {
                opacity: 1,
                scale: 1,
                duration: 0.6,
                ease: 'power2.out'
            });

            // El logo central se difumina
            tl.to('#splash-logo', {
                opacity: 0,
                scale: 1.15,
                filter: 'blur(10px)',
                duration: 0.45,
                ease: 'power2.inOut',
                delay: 0.35
            });

            // Desaparece el splash
            tl.to('#splash-screen', {
                opacity: 0,
                duration: 0.3,
                ease: 'power1.out',
                onComplete: () => {
                    const splash = document.getElementById('splash-screen');
                    if (splash) splash.style.display = 'none';
                }
            }, "-=0.2");

            // EFECTO PUERTA: La izquierda entra desde la IZQUIERDA (-100% -> 0%)
            // y la derecha entra desde la DERECHA (100% -> 0%), uniéndose en el centro
            tl.to('#left-panel', {
                xPercent: 0,
                duration: 0.85,
                ease: 'power3.out'
            }, "-=0.15");

            tl.to('#right-panel', {
                xPercent: 0,
                duration: 0.85,
                ease: 'power3.out'
            }, "<");

            // 2. ANIMACIÓN DE SALIDA (Reversa: Apertura de Puertas, Logo en el medio y Barrita de Carga Amarilla)
            const form = document.getElementById('login-form');
            const submitBtn = document.getElementById('submit-btn');
            let isSubmitting = false;

            if (form) {
                form.addEventListener('submit', (e) => {
                    if (isSubmitting) return;

                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    e.preventDefault();
                    isSubmitting = true;

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-80', 'cursor-wait');
                    }

                    // Asegurar estado inicial de la barrita y logo central
                    gsap.set('#submit-logo', { opacity: 0, scale: 0.85 });
                    gsap.set('#loading-bar-container', { opacity: 0 });
                    gsap.set('#loading-bar-fill', { width: '0%' });

                    const exitTl = gsap.timeline({
                        onComplete: () => {
                            form.submit();
                        }
                    });

                    // 1. Las dos puertas se abren hacia los lados
                    exitTl.to('#left-panel', {
                        xPercent: -100,
                        duration: 0.7,
                        ease: 'power3.inOut'
                    });

                    exitTl.to('#right-panel', {
                        xPercent: 100,
                        duration: 0.7,
                        ease: 'power3.inOut'
                    }, "<");

                    // 2. Al abrirse, el logo de Tío Chu aparece en el centro exacto
                    exitTl.to('#submit-logo', {
                        opacity: 1,
                        scale: 1,
                        duration: 0.5,
                        ease: 'power2.out'
                    }, "-=0.4");

                    // 3. Aparece el contenedor de la barrita
                    exitTl.to('#loading-bar-container', {
                        opacity: 1,
                        duration: 0.3,
                        ease: 'power1.out'
                    }, "-=0.2");

                    // 4. La barrita amarilla se llena suavemente de 0% a 100%
                    exitTl.to('#loading-bar-fill', {
                        width: '100%',
                        duration: 1.1,
                        ease: 'power1.inOut'
                    });

                    // 5. Breve pausa para contemplar la carga al 100% antes de redirigir
                    exitTl.to({}, { duration: 0.15 });
                });
            }
        });
    </script>
</body>

</html>