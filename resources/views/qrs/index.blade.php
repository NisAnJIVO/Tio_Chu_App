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
         1. CABECERA PRINCIPAL: TÍTULO & SELECTOR DE NOCHE
         ======================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3.5 rounded-2xl notoria-card shadow-sm">
        
        <!-- Izquierda: Título Principal -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                Cobros por QR
            </h1>
        </div>

        <!-- Derecha: Selector de Noche & Modo Lectura -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0 sm:ml-auto">
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('qrs.index') }}" class="flex items-center">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl notoria-subtile text-xs hover:border-[#F5B81C] transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-semibold theme-text-primary focus:outline-none cursor-pointer pr-1">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'En Vivo' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

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
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="3" height="3"/>
                    <path d="M21 14v3h-3"/>
                    <path d="M14 21v-3"/>
                    <path d="M21 21v.01"/>
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
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate" id="kpi-qr-global-total">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalGeneral, 2) }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] theme-text-muted font-mono block mt-0.5 truncate" id="kpi-qr-global-sub">
                        Yasta: {{ number_format($totalYasta, 0) }} • Yape: {{ number_format($totalYape, 0) }}
                    </span>
                </div>
                <div class="flex w-11 h-11 sm:w-12 sm:h-12 rounded-xl notoria-subtile border notoria-border items-center justify-center shrink-0 ml-3 shadow-xs">
                    <svg class="w-6 h-6 theme-text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="3" height="3"/>
                        <path d="M21 14v3h-3"/>
                        <path d="M14 21v-3"/>
                        <path d="M21 21v.01"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 2: Barra Principal -->
            <div class="p-3 sm:p-4 rounded-xl notoria-card flex items-center justify-between shadow-sm">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block truncate">
                        Barra Principal
                    </span>
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate" id="kpi-qr-pos-Barra-Principal-total">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalsByPos['Barra Principal']['total'], 2) }}
                    </div>
                    <span class="text-[10px] theme-text-muted font-mono block mt-0.5 truncate" id="kpi-qr-pos-Barra-Principal-sub">
                        Yasta: Bs. {{ number_format($totalsByPos['Barra Principal']['yasta'], 0) }} • Yape: Bs. {{ number_format($totalsByPos['Barra Principal']['yape'], 0) }}
                    </span>
                </div>
                <div class="flex w-11 h-11 sm:w-12 sm:h-12 rounded-xl notoria-subtile border notoria-border items-center justify-center theme-text-primary shrink-0 ml-3 shadow-xs">
                    <svg class="w-6 h-6 theme-text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 22h8M12 15v7M19 3l-7 8-7-8h14z"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 3: Tienda (Entrada) -->
            <div class="p-3 sm:p-4 rounded-xl notoria-card flex items-center justify-between shadow-sm">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block truncate">
                        Tienda
                    </span>
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate" id="kpi-qr-pos-Tienda-total">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalsByPos['Tienda']['total'], 2) }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] theme-text-muted font-mono block mt-0.5 truncate" id="kpi-qr-pos-Tienda-sub">
                        Yasta: {{ number_format($totalsByPos['Tienda']['yasta'], 0) }} • Yape: {{ number_format($totalsByPos['Tienda']['yape'], 0) }}
                    </span>
                </div>
                <div class="flex w-11 h-11 sm:w-12 sm:h-12 rounded-xl notoria-subtile border notoria-border items-center justify-center theme-text-primary shrink-0 ml-3 shadow-xs">
                    <svg class="w-6 h-6 theme-text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 4: Barra Subterráneo -->
            <div class="p-3 sm:p-4 rounded-xl notoria-card flex items-center justify-between shadow-sm">
                <div class="min-w-0">
                    <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block truncate">
                        Barra Subterráneo
                    </span>
                    <div class="text-lg sm:text-2xl font-black theme-text-primary font-mono tracking-tight mt-0.5 sm:mt-1 truncate" id="kpi-qr-pos-Subte-total">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($totalsByPos['Subte']['total'], 2) }}
                    </div>
                    <span class="text-[9px] sm:text-[10px] theme-text-muted font-mono block mt-0.5 truncate" id="kpi-qr-pos-Subte-sub">
                        Yasta: {{ number_format($totalsByPos['Subte']['yasta'], 0) }} • Yape: {{ number_format($totalsByPos['Subte']['yape'], 0) }}
                    </span>
                </div>
                <div class="flex w-11 h-11 sm:w-12 sm:h-12 rounded-xl notoria-subtile border notoria-border items-center justify-center theme-text-primary shrink-0 ml-3 shadow-xs">
                    <svg class="w-6 h-6 theme-text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>

        </div>

        <!-- ========================================================
             3. FORMULARIO EN 2 NIVELES CON AIRE & LOGOS CORPORATIVOS
             ======================================================== -->
        <div class="p-6 sm:p-7 notoria-card shadow-sm rounded-2xl">
            <div class="border-b notoria-border pb-4 mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-slate-800 dark:text-slate-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="3" height="3"/>
                            <path d="M21 14v3h-3"/>
                            <path d="M14 21v-3"/>
                            <path d="M21 21v.01"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-black theme-text-primary uppercase tracking-wider font-sans">
                        Registrar Nuevo Cobro QR
                    </h2>
                </div>
            </div>
            
            <form method="POST" action="{{ route('qrs.store') }}" id="create-qr-form" onsubmit="event.preventDefault(); handleCreateQrSubmit(event); return false;"
                  class="space-y-5 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                @csrf
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                <!-- NIVEL 1: CONTEXTO / FILTROS (Punto de Venta + Aplicación) -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start pb-5 border-b notoria-border">
                    
                    <!-- PUNTO DE VENTA (Selector de Pestañas Compacto) -->
                    <div class="md:col-span-7 flex flex-col gap-1.5">
                        <label class="block text-xs font-black uppercase tracking-wider theme-text-secondary h-4 flex items-center">
                            Punto de Venta
                        </label>
                        <div class="grid grid-cols-3 p-1 rounded-xl notoria-subtile gap-1">
                            <label class="cursor-pointer">
                                <input type="radio" name="point_of_sale" value="Barra Principal" class="peer sr-only" checked>
                                <div class="h-10 flex items-center justify-center px-2 text-center rounded-lg text-xs sm:text-sm font-bold theme-text-secondary transition-all peer-checked:bg-[#F5B81C] peer-checked:text-slate-900 peer-checked:font-black peer-checked:shadow-sm">
                                    Principal
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="point_of_sale" value="Tienda" class="peer sr-only">
                                <div class="h-10 flex items-center justify-center px-2 text-center rounded-lg text-xs sm:text-sm font-bold theme-text-secondary transition-all peer-checked:bg-[#F5B81C] peer-checked:text-slate-900 peer-checked:font-black peer-checked:shadow-sm">
                                    Tienda
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="point_of_sale" value="Subte" class="peer sr-only">
                                <div class="h-10 flex items-center justify-center px-2 text-center rounded-lg text-xs sm:text-sm font-bold theme-text-secondary transition-all peer-checked:bg-[#F5B81C] peer-checked:text-slate-900 peer-checked:font-black peer-checked:shadow-sm">
                                    Subte
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- APLICACIÓN (Sin hover, Selección Ámbar Limpia) -->
                    <div class="md:col-span-5 flex flex-col gap-1.5">
                        <label class="block text-xs font-black uppercase tracking-wider theme-text-secondary h-4 flex items-center">
                            Aplicación
                        </label>
                        <div class="grid grid-cols-2 p-1 rounded-xl notoria-subtile gap-1">
                            <!-- YASTA -->
                            <label class="cursor-pointer block">
                                <input type="radio" name="bank_app" value="YASTA" class="peer sr-only" checked>
                                <div class="h-10 flex items-center justify-center px-3 rounded-lg transition-all border border-transparent peer-checked:bg-[#F5B81C] peer-checked:border-[#F5B81C] peer-checked:shadow-sm active:scale-95">
                                    <img src="{{ asset('images/LogosQR/yasta.png') }}" alt="Yasta" class="h-6 sm:h-7 w-auto object-contain">
                                </div>
                            </label>
                            <!-- YAPE -->
                            <label class="cursor-pointer block">
                                <input type="radio" name="bank_app" value="YAPE" class="peer sr-only">
                                <div class="h-10 flex items-center justify-center px-3 rounded-lg transition-all border border-transparent peer-checked:bg-[#F5B81C] peer-checked:border-[#F5B81C] peer-checked:shadow-sm active:scale-95">
                                    <img src="{{ asset('images/LogosQR/yape.png') }}" alt="Yape" class="h-6 sm:h-7 w-auto object-contain">
                                </div>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- NIVEL 2: DATOS DE TRANSACCIÓN (Cobrante + Monto + Botón Cobrar) -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                    
                    <!-- COBRANTE (Selector de Turno: Meseros y Bartenders de esa noche) -->
                    <div class="md:col-span-5 flex flex-col gap-1.5">
                        <label for="cobrante_select" class="block text-xs font-black uppercase tracking-wider theme-text-secondary h-4 flex items-center">
                            Cobrante de Turno
                        </label>
                        <div class="relative">
                            <select name="cobrante_select" id="cobrante_select" required onchange="handleCobranteChange(this)"
                                    class="w-full h-10 sm:h-11 rounded-xl notoria-subtile pl-3.5 pr-9 border notoria-border font-bold text-sm theme-text-primary focus:outline-none focus:border-[#F5B81C] focus:ring-2 focus:ring-[#F5B81C]/20 transition-all cursor-pointer appearance-none">
                                <option value="" disabled selected>-- Seleccionar Cobrante de Turno --</option>
                                @if(isset($bartenders) && $bartenders->isNotEmpty())
                                    <optgroup label="🍹 BARTENDERS / BARRA">
                                        @foreach($bartenders as $b)
                                            <option value="{{ $b->name }}" class="bg-slate-900 text-white py-1">
                                                {{ $b->name }} ({{ strtoupper($b->role ?? 'BARTENDER') }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if(isset($meseros) && $meseros->isNotEmpty())
                                    <optgroup label="🤵 MESEROS / MOZOS">
                                        @foreach($meseros as $m)
                                            @php
                                                $cleanRole = str_ireplace(['mozo', 'mozos'], ['MESERO', 'MESEROS'], $m->role ?? 'MESERO');
                                            @endphp
                                            <option value="{{ $m->name }}" class="bg-slate-900 text-white py-1">
                                                {{ $m->name }} ({{ strtoupper($cleanRole) }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if((!isset($bartenders) || $bartenders->isEmpty()) && (!isset($meseros) || $meseros->isEmpty()) && isset($cobrantesStaff) && $cobrantesStaff->isNotEmpty())
                                    <optgroup label="PERSONAL ACTIVO">
                                        @foreach($cobrantesStaff as $cs)
                                            <option value="{{ $cs->name }}" class="bg-slate-900 text-white py-1">
                                                {{ $cs->name }} ({{ strtoupper($cs->role ?? 'STAFF') }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                <option value="__custom__" class="bg-slate-900 text-[#F5B81C] font-black py-1">
                                    ➕ Otro cobrante (escribir nombre)...
                                </option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 theme-text-muted">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Input adicional sólo si elige 'Otro' -->
                        <div id="custom-cobrante-box" class="hidden mt-1.5">
                            <input type="text" id="custom_cobrante_input" placeholder="Escribe el nombre del cobrante..."
                                   class="w-full h-10 rounded-xl notoria-subtile px-3.5 border border-[#F5B81C] font-bold text-sm theme-text-primary placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-[#F5B81C]/20 transition-all">
                        </div>
                    </div>

                    <!-- MONTO (BS.): col-span-4 (label arriba, input con h-10 sm:h-11) -->
                    <div class="md:col-span-4 flex flex-col gap-1.5">
                        <label for="amount" class="block text-xs font-black uppercase tracking-wider theme-text-secondary h-4 flex items-center">
                            Monto (Bs.)
                        </label>
                        <div class="flex items-center rounded-xl notoria-subtile px-3.5 h-10 sm:h-11 border notoria-border focus-within:border-[#F5B81C] focus-within:ring-2 focus-within:ring-[#F5B81C]/20 transition-all">
                            <span class="text-sm font-mono font-bold text-[#F5B81C] mr-2 shrink-0">Bs.</span>
                            <input type="number" 
                                   step="any" 
                                   min="0.01" 
                                   inputmode="decimal" 
                                   name="amount" 
                                   id="amount" 
                                   required 
                                   placeholder="0.00" 
                                   class="w-full text-base sm:text-lg font-mono font-black bg-transparent border-0 theme-text-primary placeholder-zinc-400 focus:outline-none p-0">
                        </div>
                    </div>

                    <!-- BOTÓN + COBRAR: col-span-3 (label invisible alineador, altura exacta h-10 sm:h-11) -->
                    <div class="md:col-span-3 flex flex-col gap-1.5">
                        <span class="block text-xs font-black uppercase tracking-wider invisible select-none h-4 flex items-center" aria-hidden="true">
                            Acción
                        </span>
                        <button type="submit" id="btn-submit-qr"
                                class="h-10 sm:h-11 w-full flex items-center justify-center gap-1.5 font-bold bg-[#F5B81C] hover:bg-[#e5ac18] disabled:opacity-50 disabled:cursor-not-allowed text-slate-900 rounded-xl shadow-sm active:scale-95 transition-all cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span id="btn-submit-text">COBRAR</span>
                        </button>
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
                    $posSlug = Str::slug($posName);
                @endphp
                <div class="notoria-card flex flex-col h-full overflow-hidden shadow-sm" data-pos="{{ $posName }}" data-pos-slug="{{ $posSlug }}">
                    
                    <!-- Encabezado de Columna -->
                    <div class="notoria-card-header px-4 py-3.5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black text-white uppercase tracking-wider font-sans">
                                {{ $displayPosName }}
                            </h3>
                            <span class="text-sm font-mono font-black text-white" id="header-col-total-{{ $posSlug }}">
                                <span class="text-xs text-[#F5B81C] font-sans mr-0.5">Bs.</span>{{ number_format($subtotals['total'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-zinc-300 mt-1 font-mono" id="header-col-sub-{{ $posSlug }}">
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
                            <tbody class="divide-y notoria-border font-sans" id="qr-tbody-{{ $posSlug }}">
                                @forelse($list as $qr)
                                    <tr id="qr-row-{{ $qr->id }}" data-id="{{ $qr->id }}" data-pos="{{ $posName }}" data-pos-slug="{{ $posSlug }}" data-app="{{ $qr->bank_app }}" data-amount="{{ $qr->amount }}" class="hover:bg-slate-500/10 transition-colors">
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
                                            <button type="button" 
                                                    onclick="handleDeleteQr({{ $qr->id }}, '{{ route('qrs.destroy', $qr) }}')"
                                                    title="Eliminar cobro"
                                                    class="text-rose-500 hover:text-rose-400 p-1 rounded-md transition-colors cursor-pointer active:scale-95 {{ !$session->isOpen() ? 'pointer-events-none opacity-40' : '' }}">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-qr-row-{{ $posSlug }}">
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
                        <span class="text-xs font-black font-mono text-[#F5B81C]" id="footer-col-subtotal-{{ $posSlug }}">Bs. {{ number_format($subtotals['total'], 2) }}</span>
                    </div>

                </div>
            @endforeach

        </div>

    @endif

</div>

<!-- Toast flotante estilo iOS -->
<div id="qr-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2 bg-black/95 border border-zinc-800 text-xs text-zinc-200 rounded-full shadow-2xl backdrop-blur-md flex items-center gap-2.5 transition-all duration-300 opacity-0 pointer-events-none">
    <span id="qr-toast-dot" class="w-2 h-2 rounded-full bg-[#F5B81C]"></span>
    <span id="qr-toast-text" class="font-bold font-sans"></span>
</div>

<!-- Script para interacción rápida y eliminación asíncrona -->
<script>
const YASTA_LOGO_URL = "{{ asset('images/LogosQR/yasta.png') }}";
const YAPE_LOGO_URL = "{{ asset('images/LogosQR/yape.png') }}";

function showQrToast(text, type = 'success') {
    const toast = document.getElementById('qr-toast');
    const toastText = document.getElementById('qr-toast-text');
    const toastDot = document.getElementById('qr-toast-dot');
    if (!toast || !toastText) return;

    toastText.innerHTML = text;
    if (toastDot) {
        toastDot.className = type === 'error' ? 'w-2 h-2 rounded-full bg-rose-500' : 'w-2 h-2 rounded-full bg-[#F5B81C]';
    }
    toast.classList.remove('opacity-0', 'pointer-events-none');
    toast.classList.add('opacity-100');
    clearTimeout(toast._t);
    toast._t = setTimeout(() => {
        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0', 'pointer-events-none');
    }, 2800);
}

function getPosSlug(posName) {
    if (posName === 'Barra Principal') return 'barra-principal';
    if (posName === 'Tienda') return 'tienda';
    if (posName === 'Subte') return 'subte';
    return (posName || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}

// Control del Cobrante (Select de Turno / Manual)
function handleCobranteChange(select) {
    const customBox = document.getElementById('custom-cobrante-box');
    const customInput = document.getElementById('custom_cobrante_input');
    if (select.value === '__custom__') {
        if (customBox) customBox.classList.remove('hidden');
        if (customInput) customInput.focus();
    } else {
        if (customBox) customBox.classList.add('hidden');
        if (customInput) customInput.value = '';
    }
}

/* --- RECALCULAR TODOS LOS TOTALES Y KPIS DE QR EN VIVO --- */
function recalculateQrKPIs() {
    let globalTotal = 0, globalYasta = 0, globalYape = 0;
    const posTotals = {
        'Barra Principal': { total: 0, yasta: 0, yape: 0, count: 0, slug: 'barra-principal' },
        'Tienda': { total: 0, yasta: 0, yape: 0, count: 0, slug: 'tienda' },
        'Subte': { total: 0, yasta: 0, yape: 0, count: 0, slug: 'subte' }
    };

    document.querySelectorAll('tr[id^="qr-row-"]').forEach(row => {
        const pos = row.dataset.pos || 'Barra Principal';
        const app = row.dataset.app || 'YASTA';
        const amount = parseFloat(row.dataset.amount) || 0;

        globalTotal += amount;
        if (app === 'YASTA') globalYasta += amount;
        else globalYape += amount;

        if (posTotals[pos]) {
            posTotals[pos].total += amount;
            posTotals[pos].count += 1;
            if (app === 'YASTA') posTotals[pos].yasta += amount;
            else posTotals[pos].yape += amount;
        }
    });

    // 1. Tarjetas KPI Globales Superiores
    const globTotalEl = document.getElementById('kpi-qr-global-total');
    const globSubEl = document.getElementById('kpi-qr-global-sub');
    if (globTotalEl) globTotalEl.innerHTML = `<span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>${globalTotal.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    if (globSubEl) globSubEl.textContent = `Yasta: ${Math.round(globalYasta).toLocaleString('es-BO')} • Yape: ${Math.round(globalYape).toLocaleString('es-BO')}`;

    // 2. Tarjetas KPI por Barra
    for (const [pos, data] of Object.entries(posTotals)) {
        const idKey = pos === 'Barra Principal' ? 'Barra-Principal' : pos;
        const cardTotal = document.getElementById(`kpi-qr-pos-${idKey}-total`);
        const cardSub = document.getElementById(`kpi-qr-pos-${idKey}-sub`);

        if (cardTotal) cardTotal.innerHTML = `<span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>${data.total.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        if (cardSub) {
            const prefix = pos === 'Barra Principal' ? 'Yasta: Bs. ' : 'Yasta: ';
            const yapePrefix = pos === 'Barra Principal' ? ' • Yape: Bs. ' : ' • Yape: ';
            cardSub.textContent = `${prefix}${Math.round(data.yasta).toLocaleString('es-BO')}${yapePrefix}${Math.round(data.yape).toLocaleString('es-BO')}`;
        }

        // 3. Encabezados y Pies de Columna
        const headerTotal = document.getElementById(`header-col-total-${data.slug}`);
        const headerSub = document.getElementById(`header-col-sub-${data.slug}`);
        const footerSub = document.getElementById(`footer-col-subtotal-${data.slug}`);

        if (headerTotal) headerTotal.innerHTML = `<span class="text-xs text-[#F5B81C] font-sans mr-0.5">Bs.</span>${data.total.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        if (headerSub) headerSub.innerHTML = `<span>Yasta: Bs. ${Math.round(data.yasta).toLocaleString('es-BO')} • Yape: Bs. ${Math.round(data.yape).toLocaleString('es-BO')}</span><span class="font-sans text-zinc-300">(${data.count} cobros)</span>`;
        if (footerSub) footerSub.textContent = `Bs. ${data.total.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
}

/* --- ENVÍO ASÍNCRONO DE COBRO QR (SIN RECARGA & SIN DUPLICADOS) --- */
let isQrSubmitting = false;

window.handleCreateQrSubmit = async function(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    if (isQrSubmitting) return false;

    const form = document.getElementById('create-qr-form');
    if (!form) return false;

    const selectEl = document.getElementById('cobrante_select');
    const customInput = document.getElementById('custom_cobrante_input');
    let cobranteVal = '';

    if (selectEl) {
        if (selectEl.value === '__custom__') {
            cobranteVal = (customInput?.value || '').trim();
        } else {
            cobranteVal = (selectEl.value || '').trim();
        }
    }

    if (!cobranteVal) {
        showQrToast('Selecciona o indica el cobrante', 'error');
        if (selectEl && selectEl.value === '__custom__') {
            customInput?.focus();
        } else if (selectEl) {
            selectEl.focus();
        }
        return false;
    }

    const amountInput = document.getElementById('amount');
    const amountVal = parseFloat(amountInput?.value);
    if (!amountVal || isNaN(amountVal) || amountVal <= 0) {
        showQrToast('Ingresa un monto válido mayor a 0', 'error');
        amountInput?.focus();
        return false;
    }

    const submitBtn = document.getElementById('btn-submit-qr');
    const submitText = document.getElementById('btn-submit-text');
    const originalText = submitText ? submitText.textContent : 'COBRAR';

    // Bloquear inmediatamente para prevenir doble envío
    isQrSubmitting = true;
    if (submitBtn) {
        submitBtn.disabled = true;
        if (submitText) submitText.textContent = 'Cobrando...';
    }

    try {
        const formData = new FormData(form);
        formData.append('_ajax', '1');
        formData.set('cobrante_name', cobranteVal);
        formData.set('operator_name', cobranteVal);

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                ...(token ? { 'X-CSRF-TOKEN': token } : {})
            },
            body: formData
        });

        const data = await res.json();

        if (res.ok && data.success) {
            const qr = data.qr;
            const slug = getPosSlug(qr.point_of_sale);
            const emptyRow = document.getElementById(`empty-qr-row-${slug}`);
            if (emptyRow) emptyRow.remove();

            const tbody = document.getElementById(`qr-tbody-${slug}`);
            if (tbody) {
                const tr = document.createElement('tr');
                tr.id = `qr-row-${qr.id}`;
                tr.dataset.id = qr.id;
                tr.dataset.pos = qr.point_of_sale;
                tr.dataset.posSlug = slug;
                tr.dataset.app = qr.bank_app;
                tr.dataset.amount = qr.amount;
                tr.className = 'hover:bg-slate-500/10 transition-colors animate-fade-in';

                const appBadge = qr.bank_app === 'YASTA'
                    ? `<span class="badge-qr-yasta inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-black tracking-wider uppercase shadow-xs">
                           <img src="${YASTA_LOGO_URL}" alt="Yasta" class="h-3.5 w-auto object-contain rounded">
                           <span>YASTA</span>
                       </span>`
                    : `<span class="badge-qr-yape inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-black tracking-wider uppercase shadow-xs">
                           <img src="${YAPE_LOGO_URL}" alt="Yape" class="h-3.5 w-auto object-contain rounded">
                           <span>YAPE</span>
                       </span>`;

                tr.innerHTML = `
                    <td class="px-3.5 py-2.5 font-bold theme-text-primary uppercase text-xs truncate max-w-[120px]">
                        ${qr.operator_name}
                    </td>
                    <td class="px-2 py-2.5 text-center">
                        ${appBadge}
                    </td>
                    <td class="px-3.5 py-2.5 text-right font-black font-mono theme-text-primary text-xs">
                        Bs. ${qr.amount.toFixed(2)}
                    </td>
                    <td class="px-2 py-2.5 text-right">
                        <button type="button" 
                                onclick="handleDeleteQr(${qr.id}, '${qr.destroy_url}')"
                                title="Eliminar cobro"
                                class="text-rose-500 hover:text-rose-400 p-1 rounded-md transition-colors cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                        </button>
                    </td>
                `;

                tbody.prepend(tr);
            }

            // Recalcular KPIs y totales inmediatamente sin recarga
            recalculateQrKPIs();

            // Resetear campo monto y reenfocar
            if (amountInput) {
                amountInput.value = '';
                amountInput.focus();
            }

            showQrToast(data.message || 'Pago QR registrado');
        } else {
            showQrToast(data.message || 'Error al registrar cobro QR', 'error');
        }
    } catch(err) {
        console.error('Error al registrar QR:', err);
        showQrToast('Error de conexión al registrar cobro QR', 'error');
    } finally {
        isQrSubmitting = false;
        if (submitBtn) {
            submitBtn.disabled = false;
            if (submitText) submitText.textContent = originalText;
        }
    }

    return false;
};

/* --- ELIMINACIÓN ASINCRÓNICA DE COBRO QR (SIN RECARGA) --- */
window.handleDeleteQr = async function(id, actionUrl) {
    if (!confirm('¿Eliminar este cobro QR definitivamente?')) return;
    const row = document.getElementById('qr-row-' + id);
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
        const response = await fetch(actionUrl, {
            method: 'POST',
            headers: {
                ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                'X-HTTP-Method-Override': 'DELETE',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                '_method': 'DELETE',
                ...(token ? { '_token': token } : {}),
                '_ajax': '1'
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            if (row) {
                const slug = row.dataset.posSlug;
                row.classList.add('transition-all', 'duration-300', 'opacity-0', 'scale-95');
                setTimeout(() => {
                    row.remove();
                    recalculateQrKPIs();

                    const tbody = document.getElementById(`qr-tbody-${slug}`);
                    if (tbody && !tbody.querySelector('tr[id^="qr-row-"]')) {
                        tbody.innerHTML = `
                            <tr id="empty-qr-row-${slug}">
                                <td colspan="4" class="px-4 py-8 text-center theme-text-muted text-xs font-sans">
                                    Sin cobros QR en esta barra.
                                </td>
                            </tr>
                        `;
                    }
                }, 200);
            }
            showQrToast(data.message || 'Pago QR eliminado');
        } else {
            showQrToast(data.message || 'No se pudo eliminar el cobro QR', 'error');
        }
    } catch(err) {
        console.error('Error al eliminar QR:', err);
        showQrToast('Error de conexión al eliminar el cobro QR', 'error');
    }
};
</script>
@endsection
