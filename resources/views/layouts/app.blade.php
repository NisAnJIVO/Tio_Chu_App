<!DOCTYPE html>
<html lang="es" class="h-full bg-zinc-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión') | Discoteca Tío Chu</title>

    <!-- Scripts y Estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-zinc-900 selection:bg-zinc-800 selection:text-white">
    <div class="min-h-full flex">
        
        <!-- Barra Lateral (Sidebar) -->
        <aside class="w-64 bg-white border-r border-zinc-200 flex flex-col fixed inset-y-0 z-30">
            <!-- Logo / Cabecera -->
            <div class="h-16 flex items-center px-6 border-b border-zinc-200 justify-between">
                <div>
                    <h1 class="text-base font-bold tracking-tight text-zinc-900">DISCOTECA TÍO CHU</h1>
                    <p class="text-xs text-zinc-600 font-medium">Panel Administrativo</p>
                </div>
            </div>

            <!-- Navegación de Módulos -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <!-- 0. Resumen General -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Resumen General
                </a>

                <div class="pt-3 pb-1">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-600">Catálogos</p>
                </div>

                <!-- 1. Inventario Bebidas (Módulo 2) -->
                <a href="{{ route('products.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('products.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Inventario Bebidas
                </a>

                <!-- 2. Personal (Módulo 3) -->
                <a href="{{ route('staff.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('staff.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Personal y Turnos
                </a>

                <div class="pt-3 pb-1">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-600">Operación Nocturna</p>
                </div>

                <!-- 3. Ventas por Barra (Módulo 6) -->
                <a href="{{ route('sales.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('sales.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ventas por Barra
                </a>

                <!-- 4. Facturas & POS (Módulo 1) -->
                <a href="{{ route('invoices.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('invoices.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                    Facturas & Tarjetero
                </a>

                <!-- 5. Pagos QR (Módulo 4) -->
                <a href="{{ route('qrs.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('qrs.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Cobros QR (Yasta / Yape)
                </a>

                <!-- 6. Pagos a Personal (Módulo Independiente) -->
                <a href="{{ route('staffPayments.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('staffPayments.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Pagos a Personal
                </a>

                <!-- 7. Cierre de Caja & Gastos (Módulo 5) -->
                <a href="{{ route('closing.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('closing.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Cierre de Caja & Gastos
                </a>

                <div class="pt-3 pb-1">
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-zinc-600">Configuración</p>
                </div>

                <!-- 7. Sesiones / Noches -->
                <a href="{{ route('sessions.index') }}" 
                   class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('sessions.*') ? 'bg-zinc-100 text-zinc-900 font-semibold' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Historial de Noches
                </a>
            </nav>

            <!-- Pie del Sidebar -->
            <div class="p-4 border-t border-zinc-200">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="text-xs font-semibold text-zinc-900">{{ Auth::user()->name ?? 'Don Ludo' }}</p>
                        <p class="text-[10px] text-zinc-600 truncate max-w-[130px]">{{ Auth::user()->email ?? 'DonLudo@gmail.com' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Cerrar sesión" class="text-xs text-zinc-600 hover:text-zinc-900 font-medium p-1 hover:bg-zinc-100 rounded">
                            Salir
                        </button>
                    </form>
                </div>
                <div class="flex items-center justify-between text-[10px] text-zinc-600 pt-2 border-t border-zinc-100">
                    <span>Versión 1.0</span>
                    <span class="inline-flex items-center px-1 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700">SQLite</span>
                </div>
            </div>
        </aside>

        <!-- Contenedor Principal con margen para el Sidebar fijo -->
        <div class="pl-64 flex-1 flex flex-col">
            
            <!-- Barra Superior (Topbar) -->
            <header class="h-16 bg-white border-b border-zinc-200 flex items-center justify-between px-8 sticky top-0 z-20">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-zinc-600">Noche en Operación:</span>
                    @if(isset($session) && $session)
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-zinc-900">
                                {{ $session->day_name }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                            </span>
                            @if($session->isOpen())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Abierta
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-100 text-zinc-700 border border-zinc-300">
                                    Cerrada
                                </span>
                            @endif
                        </div>
                    @else
                        <span class="text-sm text-zinc-600 italic">No hay noche activa seleccionada</span>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('sessions.create') }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-zinc-300 text-xs font-medium rounded text-zinc-700 bg-white hover:bg-zinc-50 focus:outline-none focus:ring-1 focus:ring-zinc-900 transition-colors">
                        + Nueva Noche
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="text-xs text-zinc-600 hover:text-zinc-900 font-medium px-2.5 py-1.5 border border-transparent hover:border-zinc-200 rounded">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </header>

            <!-- Contenido Dinámico de la Página -->
            <main class="flex-1 p-8">
                
                <!-- Notificaciones Flash -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded border border-zinc-300 bg-white text-sm text-zinc-900 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded border border-red-200 bg-red-50 text-sm text-red-800">
                        <p class="font-medium mb-1">Por favor verifica los siguientes errores:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-red-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
