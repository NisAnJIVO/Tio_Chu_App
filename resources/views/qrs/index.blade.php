@extends('layouts.app')

@section('title', 'Cobros por QR')

@section('content')

<style>
    /* Ocultar flechas de números nativas */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
        appearance: textfield !important;
    }
</style>

<div class="space-y-4 max-w-7xl mx-auto w-full pb-8">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: ESTADO DE NOCHE & SELECTOR
         ======================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3.5 rounded-2xl notoria-card shadow-sm">
        
        <!-- Izquierda: Título y Selector de Noche -->
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <h1 class="text-lg sm:text-xl font-black tracking-tight theme-text-primary font-sans">
                    Cobros por QR
                </h1>
            </div>

            @if($session)
                <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl notoria-subtile">
                    <span class="w-2 h-2 rounded-full {{ $session->isOpen() ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-500' }}"></span>
                    <span class="text-xs font-black uppercase tracking-wider {{ $session->isOpen() ? 'text-emerald-500' : 'theme-text-muted' }}">
                        {{ $session->isOpen() ? 'Noche Abierta' : 'Noche Cerrada' }}
                    </span>
                </div>
            @endif

            <!-- Selector de Noche en Píldora -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('qrs.index') }}" class="flex items-center">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl notoria-subtile text-xs hover:border-[#F5B81C] transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-bold theme-text-primary focus:outline-none cursor-pointer pr-1">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'En Vivo' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif
        </div>

        <!-- Derecha: Estado o Acceso a Cierre -->
        <div class="flex items-center gap-2 shrink-0">
            @if($session && !$session->isOpen())
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl notoria-subtile text-xs theme-text-muted">
                    <svg class="w-3.5 h-3.5 theme-text-muted shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Modo lectura</span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="text-[#F5B81C] hover:underline ml-1 font-bold">Cierre &rarr;</a>
                </div>
            @endif
        </div>

    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-2xl notoria-card flex flex-col items-center justify-center shadow-sm">
            <div class="w-12 h-12 rounded-2xl notoria-subtile flex items-center justify-center theme-text-muted mb-3">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect width="6" height="6" x="3" y="3" rx="1"/>
                    <rect width="6" height="6" x="15" y="3" rx="1"/>
                    <rect width="6" height="6" x="3" y="15" rx="1"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h3m3 0h-3v3m0 3v-3m3 3v.01M10 7v3a2 2 0 01-2 2H5"/>
                </svg>
            </div>
            <h3 class="text-sm font-black uppercase tracking-wider theme-text-primary">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs theme-text-secondary mt-1 mb-5">Apertura una noche para registrar cobros por QR.</p>
            <a href="{{ route('sessions.create') }}" class="px-5 py-2.5 rounded-xl bg-[#F5B81C] text-black font-black text-xs hover:bg-[#e5ac18] transition-all active:scale-95 shadow-sm">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. 4 TARJETAS KPI RESUMEN: CARDS NOTORIAS
             ======================================================== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
            
            <!-- TARJETA 1: Total General QR -->
            <div class="p-3 sm:p-4 rounded-xl notoria-card flex items-center justify-between shadow-sm">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block truncate">
                        Total QR
                    </span>
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalGeneral, 2) }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] theme-text-muted font-mono block mt-0.5 truncate">
                        Yasta: {{ number_format($totalYasta, 0) }} • Yape: {{ number_format($totalYape, 0) }}
                    </span>
                </div>
                <div class="hidden sm:flex w-10 h-10 rounded-xl notoria-subtile items-center justify-center text-[#F5B81C] shrink-0 ml-3 shadow-sm">
                    <svg class="w-5 h-5 text-[#F5B81C]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="6" height="6" x="3" y="3" rx="1"/>
                        <rect width="6" height="6" x="15" y="3" rx="1"/>
                        <rect width="6" height="6" x="3" y="15" rx="1"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h3m3 0h-3v3m0 3v-3m3 3v.01M10 7v3a2 2 0 01-2 2H5"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 2: Barra Principal -->
            <div class="p-3 sm:p-4 rounded-xl notoria-card flex items-center justify-between shadow-sm">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block truncate">
                        Barra Principal
                    </span>
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalsByPos['Barra Principal']['total'], 2) }}
                    </div>
                    <span class="text-[10px] theme-text-muted font-mono block mt-0.5 truncate">
                        Yasta: Bs. {{ number_format($totalsByPos['Barra Principal']['yasta'], 0) }} • Yape: Bs. {{ number_format($totalsByPos['Barra Principal']['yape'], 0) }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl notoria-subtile flex items-center justify-center theme-text-primary shrink-0 ml-3 shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 22h8M12 15v7M19 3l-7 8-7-8h14z"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 3: Tienda (Entrada) -->
            <div class="p-3 sm:p-4 rounded-xl notoria-card flex items-center justify-between shadow-sm">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block truncate">
                        Tienda
                    </span>
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalsByPos['Tienda']['total'], 2) }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] theme-text-muted font-mono block mt-0.5 truncate">
                        Yasta: {{ number_format($totalsByPos['Tienda']['yasta'], 0) }} • Yape: {{ number_format($totalsByPos['Tienda']['yape'], 0) }}
                    </span>
                </div>
                <div class="hidden sm:flex w-10 h-10 rounded-xl notoria-subtile items-center justify-center theme-text-primary shrink-0 ml-3 shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 4: Barra Subterráneo -->
            <div class="p-3 sm:p-4 rounded-xl notoria-card flex items-center justify-between shadow-sm">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block truncate">
                        Barra Subterráneo
                    </span>
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalsByPos['Subte']['total'], 2) }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] theme-text-muted font-mono block mt-0.5 truncate">
                        Yasta: {{ number_format($totalsByPos['Subte']['yasta'], 0) }} • Yape: {{ number_format($totalsByPos['Subte']['yape'], 0) }}
                    </span>
                </div>
                <div class="hidden sm:flex w-10 h-10 rounded-xl notoria-subtile flex items-center justify-center theme-text-primary shrink-0 ml-3 shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>

        </div>

        <!-- ========================================================
             3. FORMULARIO MINIMALISTA CON SWITCHES MODO IPHONE & LOGOS
             ======================================================== -->
        <div class="p-4 sm:p-5 notoria-card shadow-sm">
            <div class="border-b notoria-border pb-3.5 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#F5B81C]/15 border border-[#F5B81C]/50 flex items-center justify-center text-[#F5B81C] shadow-sm shrink-0">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="6" height="6" x="3" y="3" rx="1"/>
                            <rect width="6" height="6" x="15" y="3" rx="1"/>
                            <rect width="6" height="6" x="3" y="15" rx="1"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h3m3 0h-3v3m0 3v-3m3 3v.01M10 7v3a2 2 0 01-2 2H5"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black theme-text-primary uppercase tracking-wider font-sans">
                            Registrar Nuevo Cobro QR
                        </h2>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('qrs.store') }}" 
                  class="space-y-4 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                @csrf
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                    
                    <!-- 1. PUNTO DE VENTA (SWITCHES MODO IPHONE) -->
                    <div class="sm:col-span-1 lg:col-span-3 space-y-1.5">
                        <label class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                            Punto de Venta
                        </label>
                        <div class="grid grid-cols-3 p-1 rounded-xl notoria-subtile gap-1 h-[48px] items-center">
                            <label class="cursor-pointer h-full">
                                <input type="radio" name="point_of_sale" value="Barra Principal" class="peer sr-only" checked>
                                <div class="h-full flex items-center justify-center py-2 text-center rounded-lg text-sm sm:text-base font-bold theme-text-secondary transition-all peer-checked:bg-[#F5B81C] peer-checked:text-black peer-checked:font-black peer-checked:shadow-sm">
                                    Principal
                                </div>
                            </label>
                            <label class="cursor-pointer h-full">
                                <input type="radio" name="point_of_sale" value="Tienda" class="peer sr-only">
                                <div class="h-full flex items-center justify-center py-2 text-center rounded-lg text-sm sm:text-base font-bold theme-text-secondary transition-all peer-checked:bg-[#F5B81C] peer-checked:text-black peer-checked:font-black peer-checked:shadow-sm">
                                    Tienda
                                </div>
                            </label>
                            <label class="cursor-pointer h-full">
                                <input type="radio" name="point_of_sale" value="Subte" class="peer sr-only">
                                <div class="h-full flex items-center justify-center py-2 text-center rounded-lg text-sm sm:text-base font-bold theme-text-secondary transition-all peer-checked:bg-[#F5B81C] peer-checked:text-black peer-checked:font-black peer-checked:shadow-sm">
                                    Subte
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. APLICACIÓN BANCARIA (SWITCHES MODO IPHONE CON LOGOS) -->
                    <div class="sm:col-span-1 lg:col-span-3 space-y-1.5">
                        <label class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                            Aplicación
                        </label>
                        <div class="grid grid-cols-2 p-1 rounded-xl notoria-subtile gap-1.5 h-[48px] items-center">
                            <!-- YASTA -->
                            <label class="cursor-pointer h-full">
                                <input type="radio" name="bank_app" value="YASTA" class="peer sr-only" checked>
                                <div class="app-switch-yasta h-full flex items-center justify-center gap-2 px-2.5 rounded-lg text-sm sm:text-base font-bold theme-text-secondary transition-all cursor-pointer">
                                    <img src="{{ asset('images/LogosQR/yasta.png') }}" alt="Yasta" class="h-6 sm:h-7 w-auto object-contain rounded shrink-0">
                                    <span class="tracking-wide">YASTA</span>
                                </div>
                            </label>
                            <!-- YAPE -->
                            <label class="cursor-pointer h-full">
                                <input type="radio" name="bank_app" value="YAPE" class="peer sr-only">
                                <div class="app-switch-yape h-full flex items-center justify-center gap-2 px-2.5 rounded-lg text-sm sm:text-base font-bold theme-text-secondary transition-all cursor-pointer">
                                    <img src="{{ asset('images/LogosQR/yape.png') }}" alt="Yape" class="h-6 sm:h-7 w-auto object-contain rounded shrink-0">
                                    <span class="tracking-wide">YAPE</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 3. COBRANTE (COMBOBOX DIRECTO ULTRA USABLE) -->
                    <div class="sm:col-span-1 lg:col-span-3 space-y-1.5 relative" id="cobrante-container">
                        <label for="cobrante-search-input" class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                            Cobrante
                        </label>
                        <input type="hidden" name="cobrante_name" id="cobrante_name_input" value="" required>

                        <div class="relative flex items-center h-[48px]">
                            <!-- Indicador / Avatar -->
                            <div id="cobrante-avatar" class="absolute left-3 w-6 h-6 rounded-lg notoria-subtile flex items-center justify-center text-xs font-black theme-text-muted pointer-events-none transition-colors shrink-0">
                                <svg class="w-3.5 h-3.5 theme-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>

                            <!-- Input directo con autocompletado y búsqueda -->
                            <input type="text" 
                                   id="cobrante-search-input" 
                                   autocomplete="off" 
                                   placeholder="Escribe o elige cobrante..."
                                   class="w-full h-full text-sm font-bold notoria-input theme-text-primary rounded-xl pl-11 pr-8 focus:outline-none focus:border-[#F5B81C] transition-all cursor-pointer">

                            <!-- Botón Limpiar Selección -->
                            <button type="button" 
                                    id="cobrante-clear-btn" 
                                    onclick="clearCobrante()"
                                    class="hidden absolute right-2.5 w-5 h-5 rounded-full notoria-subtile flex items-center justify-center text-xs theme-text-muted hover:theme-text-primary transition-colors cursor-pointer"
                                    title="Limpiar cobrante">
                                ✕
                            </button>
                        </div>

                        <!-- Menú Desplegable con Staff Agrupado -->
                        <div id="cobrante-menu" 
                             class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 rounded-2xl notoria-card shadow-2xl p-2 max-h-72 overflow-y-auto space-y-2 border notoria-border">
                            
                            @php
                                $bartenders = $cobrantesStaff->filter(fn($s) => str_contains(mb_strtolower($s->role), 'bartender') || str_contains(mb_strtolower($s->role), 'barra'));
                                $refuerzos  = $cobrantesStaff->filter(fn($s) => str_contains(mb_strtolower($s->role), 'refuerzo'));
                                $meseros    = $cobrantesStaff->reject(fn($s) =>
                                    str_contains(mb_strtolower($s->role), 'bartender') ||
                                    str_contains(mb_strtolower($s->role), 'barra') ||
                                    str_contains(mb_strtolower($s->role), 'refuerzo')
                                );
                            @endphp

                            <!-- Grupo Bartenders -->
                            @if($bartenders->isNotEmpty())
                                <div class="cobrante-group" data-group="bartenders">
                                    <div class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-[#F5B81C]">
                                        BARTENDERS
                                    </div>
                                    <div class="space-y-0.5 mt-0.5">
                                        @foreach($bartenders as $b)
                                            @php
                                                $bRole = str_ireplace('mozo', 'MESERO', $b->role);
                                            @endphp
                                            <div onclick="selectCobrante('{{ $b->name }}', '{{ strtoupper($bRole) }}')" 
                                                 class="cobrante-item flex items-center justify-between px-2.5 py-2 rounded-xl hover:bg-[#F5B81C]/15 cursor-pointer transition-colors group"
                                                 data-name="{{ $b->name }}"
                                                 data-search="{{ strtolower($b->name . ' ' . $bRole) }}">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-6 h-6 rounded-lg notoria-subtile flex items-center justify-center text-xs font-black theme-text-primary group-hover:border-[#F5B81C] shrink-0">
                                                        {{ substr($b->name, 0, 1) }}
                                                    </div>
                                                    <div class="min-w-0 truncate">
                                                        <span class="text-xs font-bold theme-text-primary block truncate">{{ $b->name }}</span>
                                                        <span class="text-[10px] theme-text-secondary font-medium block truncate">{{ strtoupper($bRole) }}</span>
                                                    </div>
                                                </div>
                                                <span class="cobrante-check hidden text-[#F5B81C] text-xs font-black shrink-0 ml-2">✓</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Grupo Meseros -->
                            @if($meseros->isNotEmpty())
                                <div class="cobrante-group" data-group="meseros">
                                    <div class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-[#F5B81C]">
                                        MESEROS
                                    </div>
                                    <div class="space-y-0.5 mt-0.5">
                                        @foreach($meseros as $m)
                                            @php
                                                $cleanRole = str_ireplace(['mozo', 'mozos'], ['MESERO', 'MESEROS'], $m->role);
                                                if (empty($cleanRole) || strtolower($cleanRole) === 'staff') {
                                                     $cleanRole = 'MESERO';
                                                }
                                            @endphp
                                            <div onclick="selectCobrante('{{ $m->name }}', '{{ strtoupper($cleanRole) }}')" 
                                                 class="cobrante-item flex items-center justify-between px-2.5 py-2 rounded-xl hover:bg-[#F5B81C]/15 cursor-pointer transition-colors group"
                                                 data-name="{{ $m->name }}"
                                                 data-search="{{ strtolower($m->name . ' ' . $cleanRole) }}">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-6 h-6 rounded-lg notoria-subtile flex items-center justify-center text-xs font-black theme-text-primary group-hover:border-[#F5B81C] shrink-0">
                                                        {{ substr($m->name, 0, 1) }}
                                                    </div>
                                                    <div class="min-w-0 truncate">
                                                        <span class="text-xs font-bold theme-text-primary block truncate">{{ $m->name }}</span>
                                                        <span class="text-[10px] theme-text-secondary font-medium block truncate">{{ strtoupper($cleanRole) }}</span>
                                                    </div>
                                                </div>
                                                <span class="cobrante-check hidden text-[#F5B81C] text-xs font-black shrink-0 ml-2">✓</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Grupo Refuerzos -->
                            @if($refuerzos->isNotEmpty())
                                <div class="cobrante-group" data-group="refuerzos">
                                    <div class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-[#F5B81C]">
                                        REFUERZOS
                                    </div>
                                    <div class="space-y-0.5 mt-0.5">
                                        @foreach($refuerzos as $r)
                                            @php
                                                $rRole = str_ireplace('mozo', 'MESERO', $r->role);
                                            @endphp
                                            <div onclick="selectCobrante('{{ $r->name }}', '{{ strtoupper($rRole) }}')" 
                                                 class="cobrante-item flex items-center justify-between px-2.5 py-2 rounded-xl hover:bg-[#F5B81C]/15 cursor-pointer transition-colors group"
                                                 data-name="{{ $r->name }}"
                                                 data-search="{{ strtolower($r->name . ' ' . $rRole) }}">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <div class="w-6 h-6 rounded-lg notoria-subtile flex items-center justify-center text-xs font-black theme-text-primary group-hover:border-[#F5B81C] shrink-0">
                                                        {{ substr($r->name, 0, 1) }}
                                                    </div>
                                                    <div class="min-w-0 truncate">
                                                        <span class="text-xs font-bold theme-text-primary block truncate">{{ $r->name }}</span>
                                                        <span class="text-[10px] theme-text-secondary font-medium block truncate">{{ strtoupper($rRole) }}</span>
                                                    </div>
                                                </div>
                                                <span class="cobrante-check hidden text-[#F5B81C] text-xs font-black shrink-0 ml-2">✓</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    <!-- 4. MONTO COBRADO & BOTÓN AGREGAR -->
                    <div class="sm:col-span-1 lg:col-span-3 space-y-1.5">
                        <label for="amount" class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                            Monto
                        </label>
                        <div class="flex items-center gap-2 h-[48px]">
                            <div class="relative flex-1 h-full">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-sans font-bold text-xs text-[#F5B81C]">Bs.</span>
                                <input type="number" step="any" min="0.01" inputmode="decimal" name="amount" id="amount" required placeholder="0.00"
                                       class="w-full h-full text-base sm:text-lg font-mono font-black rounded-xl pl-9 pr-2 notoria-input theme-text-primary focus:outline-none focus:border-[#F5B81C] placeholder:text-zinc-400">
                            </div>
                            <button type="submit" 
                                    class="h-full px-4 bg-[#F5B81C] text-black font-black text-sm rounded-xl hover:bg-[#e5ac18] active:scale-95 transition-all shadow-sm shrink-0 flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                <span>Cobrar</span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        <!-- ========================================================
             4. 3 COLUMNAS POR PUNTO DE VENTA (AUDITORÍA LIMPIA)
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            @foreach($pointsOfSale as $posName)
                @php
                    $list = $paymentsByPos[$posName];
                    $subtotals = $totalsByPos[$posName];
                    $displayPosName = match($posName) {
                        'Barra Principal' => 'Barra Principal',
                        'Subte' => 'Barra Subterráneo',
                        default => $posName,
                    };
                @endphp
                <div class="notoria-card flex flex-col h-full overflow-hidden shadow-sm">
                    
                    <!-- Encabezado de Columna -->
                    <div class="notoria-card-header px-4 py-3.5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black text-white uppercase tracking-wider font-sans">
                                {{ $displayPosName }}
                            </h3>
                            <span class="text-sm font-mono font-black text-white">
                                <span class="text-xs text-[#F5B81C] font-sans mr-0.5">Bs.</span>{{ number_format($subtotals['total'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-zinc-300 mt-1 font-mono">
                            <span>Yasta: Bs. {{ number_format($subtotals['yasta'], 0) }} • Yape: Bs. {{ number_format($subtotals['yape'], 0) }}</span>
                            <span class="font-sans text-zinc-300">({{ $list->count() }} cobros)</span>
                        </div>
                    </div>

                    <!-- Tabla de Transacciones -->
                    <div class="flex-1 overflow-y-auto max-h-[460px]">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead class="bg-slate-950 text-white font-mono uppercase text-[10px] tracking-wider sticky top-0 font-bold border-b notoria-border">
                                <tr>
                                    <th class="px-3.5 py-2.5">Cobrante</th>
                                    <th class="px-2 py-2.5 text-center">App</th>
                                    <th class="px-3.5 py-2.5 text-right">Monto</th>
                                    <th class="px-2 py-2.5 text-right w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y notoria-border font-sans">
                                @forelse($list as $qr)
                                    <tr id="qr-row-{{ $qr->id }}" class="hover:bg-slate-500/10 transition-colors">
                                        <td class="px-3.5 py-2.5 font-bold theme-text-primary uppercase text-xs truncate max-w-[120px]">
                                            {{ $qr->operator_name }}
                                        </td>
                                        <td class="px-2 py-2.5 text-center">
                                            @if($qr->bank_app === 'YASTA')
                                                <span class="badge-qr-yasta inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-black tracking-wider uppercase shadow-xs">
                                                    <img src="{{ asset('images/LogosQR/yasta.png') }}" alt="Yasta" class="h-3.5 w-auto object-contain rounded">
                                                    <span>YASTA</span>
                                                </span>
                                            @else
                                                <span class="badge-qr-yape inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-black tracking-wider uppercase shadow-xs">
                                                    <img src="{{ asset('images/LogosQR/yape.png') }}" alt="Yape" class="h-3.5 w-auto object-contain rounded">
                                                    <span>YAPE</span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3.5 py-2.5 text-right font-black font-mono theme-text-primary text-xs">
                                            Bs. {{ number_format($qr->amount, 2) }}
                                        </td>
                                        <td class="px-2 py-2.5 text-right">
                                            <form method="POST" action="{{ route('qrs.destroy', $qr) }}" 
                                                  class="delete-qr-form {{ !$session->isOpen() ? 'pointer-events-none opacity-40' : '' }}"
                                                  data-qr-id="{{ $qr->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        title="Eliminar cobro"
                                                        class="text-rose-500 hover:text-rose-400 p-1 rounded-md transition-colors cursor-pointer active:scale-95">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center theme-text-muted text-xs font-sans">
                                            Sin cobros QR en {{ $displayPosName }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pie de la Columna -->
                    <div class="px-4 py-3 notoria-subtile border-t notoria-border rounded-none flex items-center justify-between font-sans">
                        <span class="text-[11px] theme-text-secondary font-bold uppercase tracking-wider">Subtotal:</span>
                        <span class="text-xs font-black font-mono text-[#F5B81C]">Bs. {{ number_format($subtotals['total'], 2) }}</span>
                    </div>

                </div>
            @endforeach

        </div>

    @endif

</div>

<!-- Script para interacción rápida y eliminación asíncrona -->
<script>
// Control del Selector Personalizado de Cobrante (Directo & Usable)
function openCobranteMenu() {
    const menu = document.getElementById('cobrante-menu');
    if (!menu) return;
    menu.classList.remove('hidden');
}

function closeCobranteMenu() {
    const menu = document.getElementById('cobrante-menu');
    if (!menu) return;
    menu.classList.add('hidden');
}

function selectCobrante(name, role) {
    const hiddenInput = document.getElementById('cobrante_name_input');
    const searchInput = document.getElementById('cobrante-search-input');
    const avatar = document.getElementById('cobrante-avatar');
    const clearBtn = document.getElementById('cobrante-clear-btn');

    if (hiddenInput) hiddenInput.value = name;
    if (searchInput) {
        searchInput.value = name;
        searchInput.classList.remove('border-rose-500');
        searchInput.classList.add('border-[#F5B81C]');
    }
    if (avatar) {
        avatar.innerHTML = `<span class="text-xs font-black">${name.charAt(0).toUpperCase()}</span>`;
        avatar.className = 'absolute left-3 w-6 h-6 rounded-lg bg-[#F5B81C] text-black flex items-center justify-center text-xs font-black shadow-sm shrink-0 pointer-events-none';
    }
    if (clearBtn) {
        clearBtn.classList.remove('hidden');
    }

    // Actualizar checks
    document.querySelectorAll('.cobrante-item').forEach(item => {
        const check = item.querySelector('.cobrante-check');
        if (item.getAttribute('data-name') === name) {
            item.classList.add('bg-zinc-800/80');
            if (check) check.classList.remove('hidden');
        } else {
            item.classList.remove('bg-zinc-800/80');
            if (check) check.classList.add('hidden');
        }
    });

    closeCobranteMenu();

    const amountInput = document.getElementById('amount');
    if (amountInput) amountInput.focus();
}

function clearCobrante() {
    const hiddenInput = document.getElementById('cobrante_name_input');
    const searchInput = document.getElementById('cobrante-search-input');
    const avatar = document.getElementById('cobrante-avatar');
    const clearBtn = document.getElementById('cobrante-clear-btn');

    if (hiddenInput) hiddenInput.value = '';
    if (searchInput) {
        searchInput.value = '';
        searchInput.classList.remove('border-rose-500', 'border-[#F5B81C]');
        searchInput.focus();
    }
    if (avatar) {
        avatar.innerHTML = `<svg class="w-3.5 h-3.5 theme-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`;
        avatar.className = 'absolute left-3 w-6 h-6 rounded-lg notoria-subtile flex items-center justify-center text-xs font-black theme-text-muted pointer-events-none transition-colors shrink-0';
    }
    if (clearBtn) {
        clearBtn.classList.add('hidden');
    }

    document.querySelectorAll('.cobrante-item').forEach(item => {
        item.classList.remove('bg-zinc-800/80');
        const check = item.querySelector('.cobrante-check');
        if (check) check.classList.add('hidden');
    });

    filterCobrantes('');
    openCobranteMenu();
}

function filterCobrantes(query) {
    const q = (query || '').toLowerCase().trim();
    document.querySelectorAll('.cobrante-item').forEach(item => {
        const searchData = item.getAttribute('data-search') || '';
        if (!q || searchData.includes(q)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });

    // Ocultar grupos vacíos
    document.querySelectorAll('.cobrante-group').forEach(group => {
        const visibleItems = Array.from(group.querySelectorAll('.cobrante-item')).filter(i => i.style.display !== 'none');
        group.style.display = visibleItems.length ? 'block' : 'none';
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('cobrante-search-input');
    const container = document.getElementById('cobrante-container');

    if (searchInput) {
        searchInput.addEventListener('focus', () => {
            filterCobrantes(searchInput.value);
            openCobranteMenu();
        });

        searchInput.addEventListener('input', () => {
            openCobranteMenu();
            filterCobrantes(searchInput.value);
            const hidden = document.getElementById('cobrante_name_input');
            if (hidden) hidden.value = searchInput.value.trim();
            const clearBtn = document.getElementById('cobrante-clear-btn');
            if (clearBtn) {
                if (searchInput.value.trim()) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                const firstVisible = Array.from(document.querySelectorAll('.cobrante-item')).find(i => i.style.display !== 'none');
                if (firstVisible) {
                    e.preventDefault();
                    firstVisible.click();
                }
            } else if (e.key === 'Escape') {
                closeCobranteMenu();
            }
        });
    }

    // Cerrar al hacer clic afuera
    document.addEventListener('click', (e) => {
        if (container && !container.contains(e.target)) {
            closeCobranteMenu();
        }
    });

    // Validación al enviar el formulario
    const qrForm = document.querySelector('form[action="{{ route('qrs.store') }}"]');
    if (qrForm) {
        qrForm.addEventListener('submit', function(e) {
            const cobranteVal = (document.getElementById('cobrante_name_input')?.value || '').trim();
            if (!cobranteVal) {
                e.preventDefault();
                const input = document.getElementById('cobrante-search-input');
                if (input) {
                    input.classList.add('border-rose-500');
                    input.focus();
                }
                openCobranteMenu();
            }
        });
    }

    document.querySelectorAll('.delete-qr-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!confirm('¿Eliminar este cobro QR?')) return;

            const qrId = this.getAttribute('data-qr-id');
            const row = document.getElementById('qr-row-' + qrId);
            const actionUrl = this.getAttribute('action');
            const token = this.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json'
                    },
                    body: new URLSearchParams({
                        '_method': 'DELETE',
                        '_token': token
                    })
                });

                if (response.ok) {
                    if (row) {
                        row.classList.add('transition-all', 'duration-300', 'opacity-0', 'scale-95');
                        setTimeout(() => {
                            row.remove();
                            window.location.reload();
                        }, 200);
                    } else {
                        window.location.reload();
                    }
                } else {
                    this.submit();
                }
            } catch (err) {
                this.submit();
            }
        });
    });
});
</script>
@endsection
