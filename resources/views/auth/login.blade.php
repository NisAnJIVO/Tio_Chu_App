<!DOCTYPE html>
<html lang="es" class="h-full bg-zinc-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | Discoteca Tío Chu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-sans antialiased text-zinc-900 selection:bg-zinc-800 selection:text-white">
    <div class="w-full max-w-sm">

        <!-- Notificación de Éxito si viene de logout -->
        @if(session('success'))
            <div class="mb-4 p-3 rounded border border-zinc-200 bg-white text-xs text-zinc-800 text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-zinc-200 rounded p-6 shadow-none">
            <!-- Encabezado -->
            <div class="mb-6 pb-4 border-b border-zinc-200 text-center">
                <h1 class="text-base font-bold tracking-tight text-zinc-900">DISCOTECA TÍO CHU</h1>
                <p class="text-xs text-zinc-600 mt-1">Control Administrativo</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-700 mb-1">Correo Electrónico</label>
                    <input type="text" name="email" id="email" required autofocus value="{{ old('email') }}"
                           placeholder="DonLudo@gmail.com"
                           class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-700 mb-1">Contraseña</label>
                    <input type="password" name="password" id="password" required
                           placeholder="••••••••"
                           class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-zinc-600">
                        <input type="checkbox" name="remember" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                        <span>Recordar sesión</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                        Ingresar al Sistema
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-4 text-center">
            <p class="text-[11px] text-zinc-600">Acceso exclusivo para administración</p>
        </div>
    </div>
</body>
</html>
