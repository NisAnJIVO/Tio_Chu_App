@extends('layouts.app')

@section('title', 'Ventas por Barra')

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

    /* En modo tabla tradicional: ocultar labels de tarjetas */
    .card-label {
        display: none !important;
    }

    /* ========================================================
       MODO RESPONSIVE AUTOMÁTICO
       - Celular (< 768px): Se adapta automáticamente como tarjetas táctiles
       - PC (>= 768px): Tabla Excel tradicional con columnas congeladas
       ======================================================== */
    @media (max-width: 767px) {
        #sales-form .sales-table-wrapper {
            overflow-x: visible !important;
        }
        #sales-form .excel-table {
            display: block !important;
            width: 100% !important;
            border: none !important;
        }
        #sales-form .excel-table thead {
            display: none !important;
        }
        #sales-form .excel-table tbody {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.75rem !important;
            width: 100% !important;
            padding: 0.75rem !important;
            background: transparent !important;
        }

        /* Fila de Licor convertida en tarjeta táctil */
        #sales-form .liquor-row {
            display: grid !important;
            grid-template-columns: repeat(6, 1fr) !important;
            gap: 0.625rem 0.5rem !important;
            background: #09090b !important;
            border: 1px solid #27272a !important;
            border-radius: 1.25rem !important;
            padding: 1rem !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.6) !important;
            position: relative !important;
        }
        #sales-form .liquor-row:focus-within {
            border-color: rgba(245, 184, 28, 0.6) !important;
            box-shadow: 0 0 20px -2px rgba(245, 184, 28, 0.15) !important;
            background: #0e0c06 !important;
        }

        /* Fila de Mixer convertida en tarjeta táctil */
        #sales-form .mixer-row {
            display: grid !important;
            grid-template-columns: repeat(6, 1fr) !important;
            gap: 0.625rem 0.5rem !important;
            background: #09090b !important;
            border: 1px solid #27272a !important;
            border-radius: 1.25rem !important;
            padding: 1rem !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.6) !important;
            position: relative !important;
        }
        #sales-form .mixer-row:focus-within {
            border-color: rgba(245, 184, 28, 0.6) !important;
            box-shadow: 0 0 20px -2px rgba(245, 184, 28, 0.15) !important;
            background: #0e0c06 !important;
        }

        /* Mostrar labels descriptivos en tarjetas móviles */
        #sales-form .card-label {
            display: block !important;
        }

        /* Inputs en modo tarjeta: 100% de ancho de celda */
        #sales-form input.excel-input {
            width: 100% !important;
            height: 2.6rem !important;
            font-size: 0.95rem !important;
            border-radius: 0.75rem !important;
        }

        /* Asignaciones de Grid para Licor */
        #sales-form .col-num { display: none !important; }
        #sales-form .col-product { 
            grid-column: 1 / 5 !important; 
            border: none !important; 
            padding: 0 !important; 
        }
        #sales-form .col-subtotal { 
            grid-column: 5 / 7 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-end !important;
            justify-content: flex-start !important;
            text-align: right !important;
        }
        #sales-form .liquor-row .col-subtotal::before {
            content: "Subtotal Combo";
            display: block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #71717a;
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }
        #sales-form .mixer-row .col-subtotal::before {
            content: "Total Extras";
            display: block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #71717a;
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }
        #sales-form .col-mixer { 
            grid-column: 1 / 4 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            align-items: center !important;
        }
        #sales-form .col-special { 
            grid-column: 4 / 7 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
        }
        #sales-form .col-pkg { 
            grid-column: 1 / 3 !important; 
            border: none !important; 
            padding: 0 !important; 
        }
        #sales-form .col-units { 
            grid-column: 3 / 5 !important; 
            border: none !important; 
            padding: 0 !important; 
        }
        #sales-form .col-totini { 
            grid-column: 5 / 7 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
        }
        #sales-form .col-saldo { 
            grid-column: 1 / 3 !important; 
            border: none !important; 
            padding: 0 !important; 
        }
        #sales-form .col-vendido { 
            grid-column: 3 / 5 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
        }
        #sales-form .col-price { 
            grid-column: 5 / 7 !important; 
            border: none !important; 
            padding: 0 !important; 
        }

        /* Asignaciones de Grid para Mixers */
        #sales-form .col-mixer-consumido { 
            grid-column: 3 / 5 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
        }
        #sales-form .col-mixer-included { 
            grid-column: 5 / 7 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
        }
        #sales-form .col-mixer-extras { 
            grid-column: 1 / 4 !important; 
            border: none !important; 
            padding: 0 !important; 
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
        }
        #sales-form .col-mixer-price { 
            grid-column: 4 / 7 !important; 
            border: none !important; 
            padding: 0 !important; 
        }

        /* Totales en Modo Tarjetas */
        #sales-form tfoot {
            display: block !important;
            background: #09090b !important;
            border-top: 1px solid #27272a !important;
            padding: 0.875rem !important;
        }
        #sales-form tfoot tr {
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 0.5rem !important;
            width: 100% !important;
            border: none !important;
        }
        #sales-form tfoot td {
            border: none !important;
            padding: 0 !important;
        }
    }

    /* En PC (>= 768px): Columna de producto congelada sticky */
    @media (min-width: 768px) {
        #sales-form .col-sticky-product {
            position: sticky !important;
            left: 0 !important;
            z-index: 20 !important;
            background-color: #09090b !important;
            box-shadow: 3px 0 8px -2px rgba(0,0,0,0.7) !important;
        }
    }
</style>

<div class="space-y-4 max-w-7xl mx-auto w-full pb-14 sm:pb-8">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: TÍTULO, NOCHE & ACCIÓN DE GUARDAR
         ======================================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-3.5 sm:px-5 sm:py-3.5 rounded-2xl theme-card border theme-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-sans">
                Ventas por Barra & Combos
            </h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                Liquidación de combos, consumo de botellas y recaudación por punto de venta
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2 w-full md:w-auto shrink-0">
            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('sales.index') }}" class="flex items-center flex-1 sm:flex-initial">
                    <input type="hidden" name="bar" value="{{ $selectedBar }}">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs hover:border-[#F5B81C] transition-colors w-full sm:w-auto">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-semibold text-white focus:outline-none cursor-pointer pr-1 w-full">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-zinc-950 text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'En Vivo' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

            <!-- Estado Noche Cerrada (Discreto) -->
            @if($session && !$session->isOpen())
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs text-zinc-400">
                    <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Modo lectura</span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="text-[#F5B81C] hover:underline ml-1 font-semibold">Cierre &rarr;</a>
                </div>
            @endif

            <!-- Botón Guardar en Cabecera -->
            @if($session && $session->isOpen() && $selectedBar !== 'Tienda')
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <span id="sales-autosave-status" class="text-[10px] font-mono text-zinc-500" aria-live="polite"></span>
                    <button type="submit" form="sales-form" class="px-4 py-2 sm:py-1.5 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all flex items-center justify-center gap-1.5 cursor-pointer dilemo-btn shadow-sm w-full sm:w-auto">
                        <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Guardar Ventas</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-8 sm:p-12 text-center rounded-2xl theme-card border theme-border">
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-4">Apertura una noche para registrar las ventas.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all dilemo-btn inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. SELECTOR DE PUNTOS DE VENTA (iOS SEGMENTED CONTROL)
             ======================================================== -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 p-2 rounded-2xl theme-card border theme-border">
            <div class="grid grid-cols-3 sm:inline-flex p-1 rounded-xl bg-zinc-950 border border-zinc-800 w-full sm:w-auto">
                @foreach($availableBars as $bar)
                    @php
                        $isSelected = $selectedBar === $bar;
                        $isPrincipal = str_contains($bar, 'Kelly') || str_contains($bar, 'Principal');
                        $isSubte = str_contains($bar, 'Ariel') || str_contains($bar, 'Subterráneo') || str_contains($bar, 'Subte');
                        $badgeLabel = $isPrincipal ? 'Piso Principal' : ($isSubte ? 'Subterráneo' : 'Unidades Sueltas');
                    @endphp
                    <a href="{{ route('sales.index', ['session_id' => $session->id, 'bar' => $bar]) }}" 
                       class="px-3 sm:px-4 py-2 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer text-center {{ $isSelected ? 'bg-[#F5B81C] text-black shadow-sm' : 'text-zinc-400 hover:text-white' }}">
                        <span>{{ $bar }}</span>
                        <span class="text-[10px] font-medium opacity-75 hidden md:inline">({{ $badgeLabel }})</span>
                    </a>
                @endforeach
            </div>

            @if($session->isOpen())
                <div class="flex items-center gap-2">
                    @if($selectedBar === 'Tienda')
                        <a href="#tienda-pos-terminal"
                           class="px-3.5 py-2 sm:py-1.5 rounded-xl bg-[#F5B81C] text-black text-xs font-bold hover:bg-[#e5ac18] transition-all flex items-center justify-center gap-1.5 cursor-pointer dilemo-btn w-full sm:w-auto shadow-sm">
                            <span class="text-base font-black leading-none">+</span>
                            <span>Ir al Terminal de Venta</span>
                        </a>
                    @else
                        <button type="button" onclick="document.getElementById('modal-add-special').classList.remove('hidden')"
                                class="px-3.5 py-2 sm:py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs font-semibold text-zinc-300 hover:text-white hover:border-zinc-700 transition-all flex items-center justify-center gap-1.5 cursor-pointer dilemo-btn w-full sm:w-auto">
                            <span class="text-[#F5B81C] font-bold">+</span>
                            <span>Nueva Soda en Catálogo</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- ======================================================== -->
        <!-- MÉTRICAS CLARAS DE LA BARRA / TIENDA (GRID 2x2 EN MÓVIL) -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
            
            <!-- Total Bebidas Liquidado / Total Tienda -->
            <div class="theme-card rounded-2xl p-3.5 sm:p-5 border theme-border flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs font-semibold text-zinc-400 uppercase tracking-wider">{{ $selectedBar === 'Tienda' ? 'Total Tienda' : 'Total Bebidas' }}</span>
                    <span class="text-[9px] sm:text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-md bg-zinc-950 border border-zinc-800 text-zinc-400">{{ $selectedBar }}</span>
                </div>
                <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-[#F5B81C] tracking-tight" id="header-grand-total">
                    <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($selectedBar === 'Tienda' && $totalStoreSales > 0 ? $totalStoreSales : $grandTotalBar, 2) }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-400 mt-1 font-sans">
                    @if($selectedBar === 'Tienda')
                        <span class="font-semibold text-white">{{ $storeSales->count() }} despachos</span> sueltos registrados
                    @else
                        <span id="header-total-combos" class="font-semibold text-white">{{ $totalLiquorCombos }} combos</span> + <span id="header-total-extras" class="font-semibold text-white">{{ $totalMixerExtras }} extras</span>
                    @endif
                </p>
            </div>

            <!-- Efectivo en Mano en Barra / Tienda -->
            <div class="theme-card rounded-2xl p-3.5 sm:p-5 border theme-border flex flex-col justify-between">
                <span class="text-[11px] sm:text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Efectivo {{ $selectedBar === 'Tienda' ? 'Tienda' : 'en Barra' }}</span>
                <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-emerald-400 tracking-tight">
                    <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span><span id="header-bar-cash">{{ number_format($selectedBar === 'Tienda' ? $storeSales->sum('cash_amount') : $barCashRemaining, 2) }}</span>
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1 font-sans">
                    Dinero físico en caja
                </p>
            </div>

            <!-- En QR (YASTA / YAPE) -->
            <div class="theme-card rounded-2xl p-3.5 sm:p-5 border theme-border flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs font-semibold text-zinc-400 uppercase tracking-wider">Cobrado QR</span>
                    <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" class="text-[10px] font-semibold text-blue-400 hover:underline">Ver QR &rarr;</a>
                </div>
                <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-blue-400 tracking-tight">
                    <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($selectedBar === 'Tienda' ? $storeSales->sum('qr_amount') : $barTotalQr, 2) }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1 font-mono truncate">
                    @if($selectedBar === 'Tienda')
                        Despachos cobrados con QR
                    @else
                        YASTA: {{ number_format($barTotalQrYasta, 0) }} • YAPE: {{ number_format($barTotalQrYape, 0) }}
                    @endif
                </p>
            </div>

            <!-- En Tarjetas POS -->
            <div class="theme-card rounded-2xl p-3.5 sm:p-5 border theme-border flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] sm:text-xs font-semibold text-zinc-400 uppercase tracking-wider">Tarjetas (POS)</span>
                    <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" class="text-[10px] font-semibold text-purple-400 hover:underline">Ver POS &rarr;</a>
                </div>
                <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-purple-400 tracking-tight">
                    <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($selectedBar === 'Tienda' ? $storeSales->sum('card_amount') : $barTotalCard, 2) }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1 font-mono truncate">
                    @if($selectedBar === 'Tienda')
                        Despachos cobrados con POS
                    @else
                        Neto: Bs. {{ number_format($barTotalCardNet, 2) }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Modal: Registrar Variante Especial en Catálogo -->
        <div id="modal-add-special" class="hidden fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-3 sm:p-4">
            <div class="theme-card p-4 sm:p-6 rounded-2xl border theme-border max-w-md w-full shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b theme-border pb-3 mb-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider font-sans">Nueva Soda en Catálogo</h3>
                    <button type="button" onclick="document.getElementById('modal-add-special').classList.add('hidden')" class="text-zinc-400 hover:text-white text-base font-bold cursor-pointer">&times;</button>
                </div>
                <p class="text-xs text-zinc-400 mb-4">Define una soda especial para una categoría de tragos (ej: Sprite para Rones o Fernet).</p>
                
                <form method="POST" action="{{ route('sales.specialMixers.store') }}" class="space-y-4 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Categoría del Trago</label>
                        <select name="category" required class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                            <option value="" disabled selected class="bg-zinc-950">-- Seleccionar Categoría --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" class="bg-zinc-950">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Nombre de la Variante / Soda</label>
                        <input type="text" name="mixer_name" placeholder="Ej: Sprite 2.0L, Aquarius Pera" required 
                               class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:border-[#F5B81C]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Producto Mixer para Descuento en Barra</label>
                        <select name="product_id" required class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                            <option value="" disabled selected class="bg-zinc-950">-- Seleccionar Soda / Mixer en Almacén --</option>
                            @foreach($allMixerProducts as $mix)
                                <option value="{{ $mix->id }}" class="bg-zinc-950">{{ $mix->name }} (Bs. {{ number_format($mix->sale_price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3">
                        <button type="button" onclick="document.getElementById('modal-add-special').classList.add('hidden')" 
                                class="px-4 py-2 rounded-xl text-xs text-zinc-400 font-semibold bg-zinc-950 border border-zinc-800 hover:text-white cursor-pointer w-full sm:w-auto">Cancelar</button>
                        <button type="submit" class="px-5 py-2 bg-[#F5B81C] text-black font-bold text-xs rounded-xl hover:bg-[#e5ac18] cursor-pointer dilemo-btn w-full sm:w-auto">Guardar en Catálogo</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Asignar Mixers Especiales a la Fila de Venta -->
        <div id="modal-assign-special" class="hidden fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-3 sm:p-4">
            <div class="theme-card p-4 sm:p-6 rounded-2xl border theme-border max-w-lg w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b theme-border pb-3">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-[#F5B81C] font-bold block">Acompañamiento Alternativo</span>
                        <h3 class="text-base font-bold text-white" id="modal-liquor-title">Licor</h3>
                    </div>
                    <button type="button" onclick="closeSpecialModal()" class="text-zinc-400 hover:text-white text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <div class="bg-zinc-950 rounded-xl p-3 border border-zinc-800 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-zinc-400">Total Combos Vendidos:</span>
                        <span class="font-bold text-white ml-1 text-sm font-mono" id="modal-total-combos">0</span>
                    </div>
                    <div>
                        <span class="text-zinc-400">Soda por Defecto:</span>
                        <span class="font-bold text-[#F5B81C] ml-1" id="modal-default-mixer-name">-</span>
                    </div>
                </div>

                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    <p class="text-[11px] text-zinc-400">
                        Asigna cuántos combos salieron con un mixer alternativo (se restarán automáticamente de la soda por defecto):
                    </p>
                    
                    <div class="space-y-2">
                        @foreach($allMixerProducts as $mixer)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-950 border border-zinc-800 hover:border-zinc-700 transition-all gap-2">
                                <span class="text-xs font-medium text-white flex-1 min-w-0 truncate">{{ $mixer->name }}</span>
                                <div class="flex items-center gap-2 shrink-0">
                                    <label class="text-[11px] text-zinc-400">Combos:</label>
                                    <input type="number" inputmode="numeric" min="0" value="0" 
                                           data-mixer-id="{{ $mixer->id }}" 
                                           data-mixer-name="{{ $mixer->name }}"
                                           class="modal-special-qty w-16 h-9 text-center rounded-lg px-2 py-1 text-xs font-mono font-bold bg-black border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C]">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-3 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-between text-xs">
                    <span class="text-zinc-300">Sodas por defecto restantes:</span>
                    <span class="font-bold text-[#F5B81C] text-sm font-mono" id="modal-remaining-default">0</span>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2 border-t theme-border">
                    <button type="button" onclick="closeSpecialModal()" class="px-4 py-2 rounded-xl text-xs text-zinc-400 font-semibold bg-zinc-950 border border-zinc-800 hover:text-white cursor-pointer w-full sm:w-auto">Cancelar</button>
                    <button type="button" onclick="saveSpecialModal()" class="px-5 py-2 bg-[#F5B81C] text-black font-bold text-xs rounded-xl hover:bg-[#e5ac18] cursor-pointer dilemo-btn w-full sm:w-auto">Listo / Aplicar</button>
                </div>
            </div>
        </div>

        @if($selectedBar === 'Tienda')
            <!-- Hidden input para que el recalculador JS incluya las ventas de tienda -->
            <input type="hidden" id="store-sales-total-val" value="{{ (float)$totalStoreSales }}">

            <!-- ========================================================
                 TERMINAL POS DIRECTO — TIENDA OFICIAL TÍO CHU
                 ======================================================== -->
            <div id="tienda-pos-terminal" class="space-y-6 mb-8">

                <!-- ENCABEZADO TERMINAL -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 sm:p-5 rounded-2xl theme-card border theme-border">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#F5B81C]/20 border border-[#F5B81C]/40 flex items-center justify-center text-[#F5B81C] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                                <line x1="8" y1="21" x2="16" y2="21"/>
                                <line x1="12" y1="17" x2="12" y2="21"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#F5B81C] text-black">Tienda Oficial</span>
                                <h2 class="text-base sm:text-lg font-bold text-white tracking-tight">Ventas Directas de Unidades Sueltas</h2>
                            </div>
                            <p class="text-xs text-zinc-400 mt-0.5">
                                Toca cualquier bebida para cargarla al ticket de venta. Consume directamente de Bodega Central.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-zinc-400 font-mono">
                            Total Despachado: <strong class="text-[#F5B81C] font-mono text-sm">Bs. {{ number_format($totalStoreSales, 2) }}</strong>
                        </span>
                    </div>
                </div>

                @if($session->isOpen())
                <!-- POS INTERACTIVO: 2 COLUMNAS (CATÁLOGO DE BOTONES + TICKET DE COBRO) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

                    <!-- COLUMNA IZQUIERDA: CATÁLOGO DE BEBIDAS CON BOTONES GRANDES (lg:col-span-7 xl:col-span-8) -->
                    <div class="lg:col-span-7 xl:col-span-8 space-y-4">

                        <!-- FILTROS RÁPIDOS Y BUSCADOR -->
                        <div class="p-3.5 rounded-2xl theme-card border theme-border space-y-3">
                            <!-- Buscador Rápido -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="M21 21l-4.35-4.35"/>
                                    </svg>
                                </div>
                                <input type="text" 
                                       id="tienda-search-input" 
                                       oninput="filterTiendaDrinks()"
                                       placeholder="Buscar bebida (ej: San Pedro, Casa Real, Fernet, Ginger, Johnnie Walker)..." 
                                       class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 text-xs sm:text-sm focus:outline-none focus:border-[#F5B81C] focus:ring-1 focus:ring-[#F5B81C] transition-all">
                                <button type="button" 
                                        onclick="document.getElementById('tienda-search-input').value = ''; filterTiendaDrinks();" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-500 hover:text-white cursor-pointer text-xs">
                                    &times;
                                </button>
                            </div>

                            <!-- Pastillas de Categorías -->
                            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs" id="tienda-category-pills">
                                <button type="button" 
                                        onclick="setTiendaCategory('todas', this)"
                                        class="tienda-cat-btn px-3 py-1.5 rounded-xl font-bold text-xs cursor-pointer transition-all bg-[#F5B81C] text-black shadow-sm shrink-0">
                                    Todas
                                </button>
                                <button type="button" 
                                        onclick="setTiendaCategory('singanis', this)"
                                        class="tienda-cat-btn px-3 py-1.5 rounded-xl font-semibold text-xs cursor-pointer transition-all bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 shrink-0">
                                    Singanis
                                </button>
                                <button type="button" 
                                        onclick="setTiendaCategory('fernet-rones', this)"
                                        class="tienda-cat-btn px-3 py-1.5 rounded-xl font-semibold text-xs cursor-pointer transition-all bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 shrink-0">
                                    Fernet & Rones
                                </button>
                                <button type="button" 
                                        onclick="setTiendaCategory('whiskys', this)"
                                        class="tienda-cat-btn px-3 py-1.5 rounded-xl font-semibold text-xs cursor-pointer transition-all bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 shrink-0">
                                    Whiskys
                                </button>
                                <button type="button" 
                                        onclick="setTiendaCategory('gins', this)"
                                        class="tienda-cat-btn px-3 py-1.5 rounded-xl font-semibold text-xs cursor-pointer transition-all bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 shrink-0">
                                    Gins
                                </button>
                                <button type="button" 
                                        onclick="setTiendaCategory('tequilas-otros', this)"
                                        class="tienda-cat-btn px-3 py-1.5 rounded-xl font-semibold text-xs cursor-pointer transition-all bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 shrink-0">
                                    Tequilas & Otros
                                </button>
                                <button type="button" 
                                        onclick="setTiendaCategory('mixers', this)"
                                        class="tienda-cat-btn px-3 py-1.5 rounded-xl font-semibold text-xs cursor-pointer transition-all bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 shrink-0">
                                    Mixers & Sodas
                                </button>
                            </div>
                        </div>

                        <!-- GRID DE BOTONES TÁCTILES DE BEBIDAS -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3" id="tienda-drinks-grid">
                            @foreach($allProducts as $prod)
                                @php
                                    $drinkType = $prod->drink_type;
                                    $isMixer = $prod->category === 'Mixers';
                                    $catKey = 'otros';
                                    if ($isMixer) {
                                        $catKey = 'mixers';
                                    } elseif (str_contains(strtolower($drinkType), 'singani')) {
                                        $catKey = 'singanis';
                                    } elseif (str_contains(strtolower($drinkType), 'ron') || str_contains(strtolower($drinkType), 'fernet')) {
                                        $catKey = 'fernet-rones';
                                    } elseif (str_contains(strtolower($drinkType), 'whisky')) {
                                        $catKey = 'whiskys';
                                    } elseif (str_contains(strtolower($drinkType), 'gin')) {
                                        $catKey = 'gins';
                                    } else {
                                        $catKey = 'tequilas-otros';
                                    }
                                @endphp
                                <button type="button" 
                                        onclick="addDrinkToTiendaCart({{ $prod->id }}, {{ json_encode($prod->name) }}, {{ (float)$prod->sale_price }}, {{ json_encode($prod->image_url) }}, {{ (int)$prod->stock_warehouse }})"
                                        data-category-key="{{ $catKey }}"
                                        data-name="{{ strtolower($prod->name) }}"
                                        class="tienda-drink-card p-3 rounded-2xl bg-zinc-900/90 border border-zinc-800 hover:border-[#F5B81C] hover:bg-zinc-850 active:scale-95 transition-all flex flex-col justify-between text-left group cursor-pointer shadow-sm relative min-h-[165px]">
                                    
                                    <!-- Badge de Stock y Categoría -->
                                    <div class="flex items-center justify-between gap-1 w-full">
                                        <span class="text-[9px] font-extrabold uppercase tracking-wider text-zinc-400 truncate max-w-[85px]">
                                            {{ $isMixer ? 'Mixer' : $drinkType }}
                                        </span>
                                        @if($prod->stock_warehouse > 0)
                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shrink-0">
                                                {{ $prod->stock_warehouse }} bot.
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-mono font-medium bg-zinc-950 text-zinc-500 border border-zinc-800 shrink-0">
                                                0 bot.
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Imagen de la Bebida -->
                                    <div class="h-16 sm:h-20 w-full flex items-center justify-center my-1.5 pointer-events-none">
                                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" 
                                             class="max-h-full max-w-full object-contain filter drop-shadow group-hover:scale-110 transition-transform duration-200">
                                    </div>

                                    <!-- Nombre y Precio -->
                                    <div class="w-full space-y-1">
                                        <div class="font-bold text-white text-xs leading-tight line-clamp-2 group-hover:text-[#F5B81C] transition-colors">
                                            {{ $prod->name }}
                                        </div>
                                        <div class="flex items-center justify-between pt-1 border-t border-zinc-800/60">
                                            <span class="font-mono font-black text-[#F5B81C] text-xs sm:text-sm">
                                                Bs. {{ number_format($prod->sale_price, 0) }}
                                            </span>
                                            <span class="text-[10px] font-bold text-zinc-400 group-hover:text-white flex items-center gap-0.5">
                                                <span>+ Añadir</span>
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Estado sin resultados en búsqueda -->
                        <div id="tienda-empty-search" class="hidden text-center py-12 px-4 rounded-2xl theme-card border theme-border">
                            <p class="text-xs text-zinc-400">No se encontraron bebidas con ese término.</p>
                        </div>
                    </div>

                    <!-- COLUMNA DERECHA: TICKET DE COBRO DIRECTO (lg:col-span-5 xl:col-span-4) -->
                    <div class="lg:col-span-5 xl:col-span-4 lg:sticky lg:top-4 self-start">
                        <div class="rounded-2xl theme-card border theme-border p-4 sm:p-5 shadow-2xl space-y-4">
                            
                            <!-- Cabecera del Ticket -->
                            <div class="flex items-center justify-between pb-3 border-b theme-border">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-zinc-950 border border-zinc-800 flex items-center justify-center text-[#F5B81C]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">Ticket de Venta</h3>
                                    <span id="tienda-cart-badge-count" class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-[#F5B81C]/20 text-[#F5B81C] border border-[#F5B81C]/40">
                                        0 items
                                    </span>
                                </div>
                                <button type="button" 
                                        onclick="clearTiendaCart()" 
                                        class="text-[11px] text-zinc-400 hover:text-rose-400 font-semibold transition-colors cursor-pointer">
                                    Vaciar
                                </button>
                            </div>

                            <!-- Formulario POS para envío -->
                            <form id="tienda-pos-form" method="POST" action="{{ route('sales.storeSales.store') }}" onsubmit="return validateAndSubmitTiendaPos(event)">
                                @csrf
                                <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                                
                                <!-- Contenedor de inputs dinámicos de los items -->
                                <div id="tienda-cart-hidden-inputs"></div>

                                <!-- Inputs de Cobro -->
                                <input type="hidden" name="cash_amount" id="tienda-input-cash" value="0">
                                <input type="hidden" name="qr_amount" id="tienda-input-qr" value="0">
                                <input type="hidden" name="card_amount" id="tienda-input-card" value="0">
                                <input type="hidden" name="bank_app" id="tienda-input-bank-app" value="YASTA">
                                <input type="hidden" name="sync_qr" value="1">

                                <!-- Lista de Productos en el Ticket -->
                                <div id="tienda-cart-list" class="max-h-64 overflow-y-auto space-y-2 pr-1 divide-y divide-zinc-850">
                                    <div id="tienda-cart-placeholder" class="text-center py-8 text-zinc-500">
                                        <svg class="w-8 h-8 mx-auto mb-2 opacity-40 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-xs font-medium text-zinc-400">Ninguna bebida seleccionada</p>
                                        <p class="text-[11px] text-zinc-500 mt-0.5">Toca los botones grandes de la izquierda para agregar.</p>
                                    </div>
                                </div>

                                <!-- Total a Cobrar -->
                                <div class="pt-3 border-t theme-border">
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-zinc-950 border border-zinc-800/80">
                                        <span class="text-xs font-bold text-zinc-300 uppercase tracking-wider">Total a Cobrar:</span>
                                        <span id="tienda-cart-total-display" class="text-xl sm:text-2xl font-black font-mono text-[#F5B81C]">
                                            Bs. 0.00
                                        </span>
                                    </div>
                                </div>

                                <!-- Métodos de Pago Rápidos (1 solo toque) -->
                                <div class="space-y-2 pt-2">
                                    <label class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block">
                                        Forma de Pago:
                                    </label>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <button type="button" onclick="setTiendaPaymentPreset('efectivo')" id="btn-pay-efectivo"
                                                class="tienda-pay-btn py-2 px-2.5 rounded-xl border font-bold flex items-center justify-center gap-1.5 transition-all bg-[#F5B81C] text-black border-[#F5B81C] shadow-sm">
                                            <span>💵 Efectivo 100%</span>
                                        </button>
                                        <button type="button" onclick="setTiendaPaymentPreset('qr')" id="btn-pay-qr"
                                                class="tienda-pay-btn py-2 px-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all bg-zinc-950 border-zinc-800 text-zinc-400 hover:text-white">
                                            <span>📱 QR 100%</span>
                                        </button>
                                        <button type="button" onclick="setTiendaPaymentPreset('tarjeta')" id="btn-pay-tarjeta"
                                                class="tienda-pay-btn py-2 px-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all bg-zinc-950 border-zinc-800 text-zinc-400 hover:text-white">
                                            <span>💳 Tarjeta 100%</span>
                                        </button>
                                        <button type="button" onclick="setTiendaPaymentPreset('mixto')" id="btn-pay-mixto"
                                                class="tienda-pay-btn py-2 px-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all bg-zinc-950 border-zinc-800 text-zinc-400 hover:text-white">
                                            <span>⚖️ Dividido / Mixto</span>
                                        </button>
                                    </div>

                                    <!-- Selector de App QR (si QR > 0) -->
                                    <div id="tienda-qr-app-picker" class="hidden p-2.5 rounded-xl bg-blue-500/10 border border-blue-500/20 space-y-1.5">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-bold text-blue-400">App para Cobro QR:</span>
                                            <span class="text-zinc-400 text-[10px]">Se guardará en la lista de QRs</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button" onclick="setTiendaBankApp('YASTA')" id="btn-app-yasta"
                                                    class="py-1.5 rounded-lg text-xs font-bold border transition-all bg-[#004b93] text-white border-blue-400">
                                                YASTA (Unión)
                                            </button>
                                            <button type="button" onclick="setTiendaBankApp('YAPE')" id="btn-app-yape"
                                                    class="py-1.5 rounded-lg text-xs font-bold border transition-all bg-zinc-900 text-zinc-400 border-zinc-800 hover:text-white">
                                                YAPE (BCP)
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Desglose Manual para Pago Mixto / Personalizado -->
                                    <div id="tienda-split-inputs" class="hidden p-3 rounded-xl bg-zinc-950 border border-zinc-800 space-y-2">
                                        <div class="text-[11px] text-zinc-400 font-semibold">Desglose de montos:</div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="text-[10px] text-emerald-400 block mb-1">Efectivo:</label>
                                                <input type="number" step="0.5" min="0" id="tienda-manual-cash" oninput="onTiendaManualPaymentChange()"
                                                       class="w-full h-8 text-center rounded-lg bg-black border border-zinc-800 font-mono text-xs font-bold text-white focus:outline-none focus:border-emerald-500">
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-blue-400 block mb-1">QR:</label>
                                                <input type="number" step="0.5" min="0" id="tienda-manual-qr" oninput="onTiendaManualPaymentChange()"
                                                       class="w-full h-8 text-center rounded-lg bg-black border border-zinc-800 font-mono text-xs font-bold text-white focus:outline-none focus:border-blue-500">
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-purple-400 block mb-1">Tarjeta:</label>
                                                <input type="number" step="0.5" min="0" id="tienda-manual-card" oninput="onTiendaManualPaymentChange()"
                                                       class="w-full h-8 text-center rounded-lg bg-black border border-zinc-800 font-mono text-xs font-bold text-white focus:outline-none focus:border-purple-500">
                                            </div>
                                        </div>
                                        <div id="tienda-split-diff-msg" class="text-[10px] text-zinc-400 font-mono text-right"></div>
                                    </div>
                                </div>

                                <!-- Mesero / Quién retira o cobra -->
                                <div class="space-y-1.5 pt-2">
                                    <label for="tienda-input-cobrante" class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider block">
                                        Mesero / Quién retira o cobra:
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-zinc-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="cobrante_name" 
                                               id="tienda-input-cobrante" 
                                               required 
                                               list="waiters-list"
                                               placeholder="Escribe el nombre del mesero..."
                                               autocomplete="off"
                                               class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 text-xs sm:text-sm font-semibold uppercase tracking-wide focus:outline-none focus:border-[#F5B81C] focus:ring-1 focus:ring-[#F5B81C] transition-all">
                                    </div>
                                    <datalist id="waiters-list">
                                        @foreach(\App\Models\Staff::where('is_active', true)->orderBy('name')->pluck('name') as $staffName)
                                            <option value="{{ $staffName }}">
                                        @endforeach
                                    </datalist>
                                </div>

                                <!-- Botón Gigante de Cobrar y Despachar -->
                                <div class="pt-4">
                                    <button type="submit" 
                                            id="tienda-btn-submit" 
                                            disabled
                                            class="w-full py-3.5 px-4 rounded-xl bg-zinc-800 text-zinc-500 font-black text-xs sm:text-sm uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-not-allowed shadow-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span id="tienda-btn-submit-text">SELECCIONA BEBIDAS PARA COBRAR</span>
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
                @else
                <div class="p-6 rounded-2xl theme-card border theme-border text-center text-zinc-400 text-xs">
                    Noche cerrada. Abre o desbloquea la noche para registrar ventas en tienda.
                </div>
                @endif

                <!-- ========================================================
                     TABLA DE VENTAS REGISTRADAS ESTA NOCHE EN TIENDA
                     ======================================================== -->
                <div class="rounded-2xl theme-card border theme-border overflow-hidden space-y-4 p-4 sm:p-6 mt-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b theme-border">
                        <div>
                            <h3 class="text-sm font-bold text-white tracking-tight">Historial de Despachos de Esta Noche</h3>
                            <p class="text-xs text-zinc-400 mt-0.5">Lista de todas las salidas y cobros registrados en mostrador de tienda.</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-[#F5B81C] bg-zinc-950 px-3 py-1.5 rounded-xl border border-zinc-800">
                            {{ $storeSales->count() }} Despachos • Bs. {{ number_format($totalStoreSales, 2) }}
                        </span>
                    </div>

                    @if($storeSales->isEmpty())
                        <div class="text-center py-10 px-4 bg-zinc-950/50 rounded-xl border border-zinc-900">
                            <h4 class="text-xs font-bold text-zinc-400">No hay ventas registradas esta noche en Tienda.</h4>
                            <p class="text-[11px] text-zinc-500 mt-1">Selecciona bebidas arriba y presiona "Registrar Cobro" para empezar.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border theme-border">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead class="bg-zinc-950 text-zinc-400 font-semibold uppercase text-[10px] tracking-wider border-b theme-border">
                                    <tr>
                                        <th class="px-4 py-3 w-12 text-center">N°</th>
                                        <th class="px-4 py-3">Hora</th>
                                        <th class="px-4 py-3">Producto Despachado</th>
                                        <th class="px-4 py-3 text-center">Cant. Suelta</th>
                                        <th class="px-4 py-3 text-right">P. Unitario</th>
                                        <th class="px-4 py-3 text-right">Total</th>
                                        <th class="px-4 py-3 text-center">Método Pago</th>
                                        <th class="px-4 py-3">Cobrante</th>
                                        @if($session->isOpen())
                                            <th class="px-4 py-3 text-center w-16">Acción</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-900 font-sans">
                                    @foreach($storeSales as $index => $ss)
                                        <tr class="hover:bg-zinc-900/40 transition-colors">
                                            <td class="px-4 py-3 text-center text-zinc-500 font-mono text-[11px]">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-zinc-400 font-mono text-[11px]">{{ $ss->created_at->format('H:i') }}</td>
                                            <td class="px-4 py-3 font-bold text-white">
                                                <span>{{ $ss->product->name ?? 'Producto Eliminado' }}</span>
                                                <span class="text-[10px] text-zinc-500 font-normal block">{{ $ss->product->category ?? '' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-zinc-900 border border-zinc-800 font-mono font-bold text-white text-xs">
                                                    {{ $ss->quantity }} unid.
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono text-zinc-300">
                                                Bs. {{ number_format($ss->unit_price, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-mono font-bold text-[#F5B81C]">
                                                Bs. {{ number_format($ss->total_price, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($ss->cash_amount > 0 && $ss->qr_amount == 0 && $ss->card_amount == 0)
                                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold">Efectivo</span>
                                                @elseif($ss->qr_amount > 0 && $ss->cash_amount == 0 && $ss->card_amount == 0)
                                                    <span class="px-2 py-0.5 rounded-md bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-bold">QR</span>
                                                @elseif($ss->card_amount > 0 && $ss->cash_amount == 0 && $ss->qr_amount == 0)
                                                    <span class="px-2 py-0.5 rounded-md bg-purple-500/10 border border-purple-500/20 text-purple-400 text-[10px] font-bold">Tarjeta</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[10px] font-bold">Mixto</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-zinc-300 font-medium">
                                                {{ $ss->cobrante_name }}
                                            </td>
                                            @if($session->isOpen())
                                                <td class="px-4 py-3 text-center">
                                                    <form method="POST" action="{{ route('sales.storeSales.destroy', $ss) }}" onsubmit="return confirm('¿Seguro que deseas anular esta venta de tienda? Devolverá el stock a Bodega Central.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Anular venta" class="p-1.5 rounded-lg text-rose-400 hover:text-white hover:bg-rose-500/20 transition-all cursor-pointer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-zinc-950 font-bold border-t theme-border text-xs">
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-right uppercase tracking-wider text-zinc-400">Total Despachos Tienda:</td>
                                        <td class="px-4 py-3 text-right font-mono text-[#F5B81C] text-sm">
                                            Bs. {{ number_format($totalStoreSales, 2) }}
                                        </td>
                                        <td colspan="{{ $session->isOpen() ? 3 : 2 }}" class="px-4 py-3 text-right text-zinc-500 text-[11px]">
                                            Efectivo: Bs. {{ number_format($storeSales->sum('cash_amount'), 2) }} • QR: Bs. {{ number_format($storeSales->sum('qr_amount'), 2) }} • POS: Bs. {{ number_format($storeSales->sum('card_amount'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        @endif

        <!-- Formulario Tabular de Inventario y Ventas para Barras (Principal y Subterráneo) -->
        @if($selectedBar !== 'Tienda')
        <form method="POST" action="{{ route('sales.updateBulk') }}" id="sales-form" data-ajax="true" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="night_session_id" value="{{ $session->id }}">
            <input type="hidden" name="bar_name" value="{{ $selectedBar }}">

            <div class="space-y-4">

                <!-- 1. TABLA DE LICORES (COMBOS) -->
                <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-zinc-950 border-b theme-border flex flex-wrap items-center justify-between gap-2.5">
                        <div>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">1. Licores & Combos</h3>
                            <p class="text-[11px] text-zinc-400 mt-0.5">
                                Stock sincronizado desde <a href="{{ route('barInventory.index', ['session_id' => $session->id, 'bar' => $selectedBar]) }}" class="text-[#F5B81C] hover:underline font-semibold">Inventario de Barras</a>.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono font-bold text-[#F5B81C]" id="badge-liquor-subtotal">
                                Subtotal: Bs. {{ number_format($subtotalLiquors, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto sales-table-wrapper">
                        <table class="w-full text-xs text-left border-collapse excel-table" id="table-liquors">
                            <thead class="bg-zinc-950 border-b theme-border text-zinc-400 font-semibold uppercase text-[11px] tracking-wider">
                                <tr>
                                    <th class="px-3 py-3.5 w-8 text-center col-num">N°</th>
                                    <th class="px-4 py-3.5 col-product col-sticky-product">Licor / Presentación</th>
                                    <th class="px-4 py-3.5 col-mixer">Acompañamiento</th>
                                    <th class="px-3 py-3.5 text-center col-special">Especial</th>
                                    <th class="px-2 py-3.5 w-16 text-center border-l theme-border bg-zinc-950/60 col-pkg">
                                        <div class="flex flex-col items-center">
                                            <span class="text-zinc-200">Cajas</span>
                                            <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                        </div>
                                    </th>
                                    <th class="px-2 py-3.5 w-16 text-center bg-zinc-950/60 col-units">
                                        <div class="flex flex-col items-center">
                                            <span class="text-zinc-200">Sueltas</span>
                                            <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                        </div>
                                    </th>
                                    <th class="px-3 py-3.5 w-20 text-center border-l theme-border col-totini">Total Inicial</th>
                                    <th class="px-3 py-3.5 w-24 text-center border-l theme-border bg-zinc-950/80 col-saldo">
                                        <div class="flex flex-col items-center text-[#F5B81C]">
                                            <span class="font-bold">Saldo Cierre</span>
                                            <span class="text-[9px] text-zinc-400 lowercase">conteo</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3.5 w-24 text-center border-l theme-border font-bold text-[#F5B81C] col-vendido">Combos Vendidos</th>
                                    <th class="px-4 py-3.5 w-28 text-right border-l theme-border col-price">Precio Combo</th>
                                    <th class="px-4 py-3.5 w-32 text-right border-l theme-border col-subtotal">Subtotal (Bs.)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y theme-border font-sans">
                                @forelse($liquorSales as $index => $sale)
                                    @php
                                        $mixerInfo = $mapping[$sale->product_id] ?? null;
                                        $mixerId = $mixerInfo['mixer_id'] ?? 0;
                                        $mixerRatio = $mixerInfo['ratio'] ?? 1;
                                        $mixerName = 'Solo';
                                        if ($mixerId == 23) $mixerName = '1 Ginger Ale 2L';
                                        elseif ($mixerId == 24) $mixerName = '1 Coca Cola 2L';
                                        elseif ($mixerId == 25) $mixerName = '1 Agua Vital 2L';
                                        elseif ($mixerId == 26) $mixerName = '2 Aguas Tónicas 1L';

                                        $specialsJson = is_array($sale->selected_special_mixer) ? json_encode($sale->selected_special_mixer) : ($sale->selected_special_mixer ?? '{}');
                                        $specialsDecoded = [];
                                        if (!empty($sale->selected_special_mixer)) {
                                            $decoded = is_string($sale->selected_special_mixer) ? json_decode($sale->selected_special_mixer, true) : $sale->selected_special_mixer;
                                            if (is_array($decoded)) $specialsDecoded = $decoded;
                                        }

                                        $badgeParts = [];
                                        foreach ($specialsDecoded as $mId => $q) {
                                            if ($q > 0) {
                                                $mObj = $allMixerProducts->firstWhere('id', $mId);
                                                $shortName = $mObj ? explode(' ', $mObj->name)[0] : 'Mixer';
                                                $badgeParts[] = "$shortName ($q)";
                                            }
                                        }
                                        $badgeText = count($badgeParts) > 0 ? implode(', ', $badgeParts) : '+ Especial';
                                        $hasSpecials = count($badgeParts) > 0;
                                    @endphp
                                    <tr class="hover:bg-zinc-900/40 transition-colors liquor-row" 
                                        data-row-id="{{ $sale->id }}" 
                                        data-product-id="{{ $sale->product_id }}"
                                        data-product-name="{{ $sale->product->name }}"
                                        data-units-per-pkg="{{ $sale->product->units_per_package ?? 1 }}"
                                        data-mixer-id="{{ $mixerId }}"
                                        data-mixer-name="{{ $mixerName }}"
                                        data-mixer-ratio="{{ $mixerRatio }}">
                                        
                                        <!-- N° -->
                                        <td class="px-3 py-2.5 text-center text-zinc-500 font-mono col-num">{{ $index + 1 }}</td>
                                        
                                        <!-- Nombre y Presentación -->
                                        <td class="px-4 py-2.5 col-product col-sticky-product">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <span class="font-bold text-white block text-sm sm:text-xs">{{ $sale->product->name }}</span>
                                                    <span class="text-[11px] text-zinc-400 font-normal">{{ $sale->product->unit }} • {{ $sale->product->units_per_package ?? 1 }} unid/caja</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Acompañamiento -->
                                        <td class="px-4 py-2.5 text-zinc-300 text-xs col-mixer">
                                            <span class="inline-flex px-2 py-0.5 rounded-lg bg-zinc-950 border border-zinc-800 text-[11px] font-medium">
                                                + {{ $mixerName }}
                                            </span>
                                        </td>
                                        
                                        <!-- Botón de Mixer Especial -->
                                        <td class="px-3 py-2 text-center col-special">
                                            <input type="hidden" 
                                                   name="sales[{{ $sale->id }}][selected_special_mixer]" 
                                                   id="input-specials-{{ $sale->id }}" 
                                                   value="{{ $specialsJson }}" 
                                                   class="input-special-mixers">
                                            <button type="button" 
                                                    onclick="openSpecialModal({{ $sale->id }})" 
                                                    id="btn-special-{{ $sale->id }}"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 cursor-pointer border {{ $hasSpecials ? 'bg-zinc-900 text-[#F5B81C] border-[#F5B81C]/50' : 'bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white' }}">
                                                <span id="badge-special-{{ $sale->id }}">{{ $badgeText }}</span>
                                            </button>
                                        </td>

                                        <!-- Cajas (Stock) -->
                                        <td class="px-2 py-2 text-center border-l theme-border col-pkg">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Cajas (Stock)</span>
                                            <input type="number" 
                                                   inputmode="numeric"
                                                   data-excel-col="0"
                                                   name="sales[{{ $sale->id }}][packages]" 
                                                   value="{{ $sale->packages }}" 
                                                   min="0"
                                                   class="excel-input input-packages w-14 sm:w-16 h-10 sm:h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                        </td>

                                        <!-- Sueltas (Stock) -->
                                        <td class="px-2 py-2 text-center col-units">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Sueltas (Stock)</span>
                                            <input type="number" 
                                                   inputmode="numeric"
                                                   data-excel-col="1"
                                                   name="sales[{{ $sale->id }}][units]" 
                                                   value="{{ $sale->units }}" 
                                                   min="0"
                                                   class="excel-input input-units w-14 sm:w-16 h-10 sm:h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                        </td>

                                        <!-- Total Inicial -->
                                        <td class="px-3 py-2.5 text-center border-l theme-border col-totini">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Total Inicial</span>
                                            <div class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-800 font-bold font-mono text-zinc-300 text-xs h-10 sm:h-auto w-full sm:w-auto cell-total-initial">
                                                {{ $sale->total_initial }}
                                            </div>
                                        </td>

                                        <!-- Saldo Cierre -->
                                        <td class="px-3 py-2 text-center border-l theme-border bg-zinc-950/40 col-saldo">
                                            <span class="card-label text-[10px] font-bold text-[#F5B81C] mb-1">Saldo Cierre</span>
                                            <input type="number" 
                                                   inputmode="numeric"
                                                   data-excel-col="2"
                                                   name="sales[{{ $sale->id }}][saldo]" 
                                                   value="{{ $sale->saldo }}" 
                                                   min="0"
                                                   class="excel-input input-saldo w-16 sm:w-20 h-10 sm:h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border-2 border-zinc-700/80 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all shadow-inner">
                                        </td>

                                        <!-- Combos Vendidos -->
                                        <td class="px-4 py-2.5 text-center border-l theme-border col-vendido">
                                            <span class="card-label text-[10px] font-bold text-[#F5B81C] mb-1">Combos Vendidos</span>
                                            <div class="inline-flex items-center justify-center px-3 py-1 rounded-xl bg-zinc-950 border border-zinc-800 font-black font-mono text-[#F5B81C] text-sm sm:text-base h-10 sm:h-auto w-full sm:w-auto cell-vendido">
                                                {{ $sale->vendido }}
                                            </div>
                                        </td>

                                        <!-- Precio Combo -->
                                        <td class="px-4 py-2 text-right border-l theme-border col-price">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Precio Combo</span>
                                            <input type="number" 
                                                   step="0.5" 
                                                   inputmode="decimal"
                                                   data-excel-col="3"
                                                   name="sales[{{ $sale->id }}][unit_price]" 
                                                   value="{{ $sale->unit_price }}" 
                                                   min="0"
                                                   class="excel-input input-price w-20 sm:w-24 h-10 sm:h-9 text-right rounded-xl px-2 font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-zinc-200 focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="px-4 py-2.5 text-right font-bold font-mono text-white text-xs sm:text-sm border-l theme-border cell-subtotal col-subtotal">
                                            Bs. {{ number_format($sale->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay licores registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-zinc-950 border-t theme-border font-bold text-xs text-white">
                                <tr>
                                    <td colspan="8" class="px-4 py-3 text-right uppercase tracking-wider font-sans">
                                        <span>Subtotal Combos:</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-black text-[#F5B81C] text-base" id="tfoot-liquor-vendido">
                                        {{ $totalLiquorCombos }}
                                    </td>
                                    <td class="hidden sm:table-cell"></td>
                                    <td class="px-4 py-3 text-right font-mono font-black text-[#F5B81C] text-sm" id="tfoot-liquor-subtotal">
                                        Bs. {{ number_format($subtotalLiquors, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- 2. TABLA DE MIXERS / SODAS (EXTRAS) -->
                <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                    <div class="px-4 sm:px-5 py-3.5 bg-zinc-950 border-b theme-border flex flex-wrap items-center justify-between gap-2.5">
                        <div>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">2. Mixers, Sodas & Aguas (Control de Extras)</h3>
                            <p class="text-[11px] text-zinc-400 mt-0.5">
                                Las sodas de combos se descuentan automáticamente. Solo las adicionales (extras) se cobran.
                            </p>
                        </div>
                        <span class="text-xs font-mono font-bold text-[#F5B81C]" id="badge-mixer-subtotal">
                            Subtotal Extras: Bs. {{ number_format($subtotalMixers, 2) }}
                        </span>
                    </div>

                    <div class="overflow-x-auto sales-table-wrapper">
                        <table class="w-full text-xs text-left border-collapse excel-table" id="table-mixers">
                            <thead class="bg-zinc-950 border-b theme-border text-zinc-400 font-semibold uppercase text-[11px] tracking-wider">
                                <tr>
                                    <th class="px-3 py-3.5 w-8 text-center col-num">N°</th>
                                    <th class="px-4 py-3.5 col-product col-sticky-product">Mixer / Soda</th>
                                    <th class="px-2 py-3.5 w-16 text-center border-l theme-border bg-zinc-950/60 col-pkg">
                                        <div class="flex flex-col items-center">
                                            <span class="text-zinc-200">Cajas</span>
                                            <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                        </div>
                                    </th>
                                    <th class="px-2 py-3.5 w-16 text-center bg-zinc-950/60 col-units">
                                        <div class="flex flex-col items-center">
                                            <span class="text-zinc-200">Sueltas</span>
                                            <span class="text-[9px] text-zinc-500 lowercase">stock</span>
                                        </div>
                                    </th>
                                    <th class="px-3 py-3.5 w-20 text-center border-l theme-border col-totini">Total Inicial</th>
                                    <th class="px-3 py-3.5 w-24 text-center border-l theme-border bg-zinc-950/80 col-saldo">
                                        <div class="flex flex-col items-center text-[#F5B81C]">
                                            <span class="font-bold">Saldo Cierre</span>
                                            <span class="text-[9px] text-zinc-400 lowercase">conteo</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3.5 w-20 text-center border-l theme-border col-mixer-consumido">Consumidas</th>
                                    <th class="px-4 py-3.5 w-24 text-center border-l theme-border text-zinc-400 col-mixer-included">En Combos</th>
                                    <th class="px-4 py-3.5 w-20 text-center border-l theme-border font-bold text-[#F5B81C] col-mixer-extras">Extras</th>
                                    <th class="px-4 py-3.5 w-28 text-right border-l theme-border col-mixer-price">Precio Extra</th>
                                    <th class="px-4 py-3.5 w-32 text-right border-l theme-border col-subtotal">Total Extras (Bs.)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y theme-border font-sans">
                                @forelse($mixerSales as $index => $sale)
                                    <tr class="hover:bg-zinc-900/40 transition-colors mixer-row" 
                                        data-row-id="{{ $sale->id }}" 
                                        data-units-per-pkg="{{ $sale->product->units_per_package ?? 1 }}"
                                        data-product-id="{{ $sale->product_id }}">
                                        
                                        <!-- N° -->
                                        <td class="px-3 py-2.5 text-center text-zinc-500 font-mono col-num">{{ $index + 1 }}</td>
                                        
                                        <!-- Mixer Name -->
                                        <td class="px-4 py-2.5 font-bold text-white col-product col-sticky-product">
                                            {{ $sale->product->name }}
                                        </td>

                                        <!-- Cajas (Stock) -->
                                        <td class="px-2 py-2 text-center border-l theme-border col-pkg">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Cajas (Stock)</span>
                                            <input type="number" 
                                                   inputmode="numeric"
                                                   data-excel-col="0"
                                                   name="sales[{{ $sale->id }}][packages]" 
                                                   value="{{ $sale->packages }}" 
                                                   min="0"
                                                   class="excel-input input-packages w-14 sm:w-16 h-10 sm:h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                        </td>

                                        <!-- Sueltas (Stock) -->
                                        <td class="px-2 py-2 text-center col-units">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Sueltas (Stock)</span>
                                            <input type="number" 
                                                   inputmode="numeric"
                                                   data-excel-col="1"
                                                   name="sales[{{ $sale->id }}][units]" 
                                                   value="{{ $sale->units }}" 
                                                   min="0"
                                                   class="excel-input input-units w-14 sm:w-16 h-10 sm:h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                        </td>

                                        <!-- Total Inicial -->
                                        <td class="px-3 py-2.5 text-center border-l theme-border col-totini">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Total Inicial</span>
                                            <div class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-800 font-bold font-mono text-zinc-300 text-xs h-10 sm:h-auto w-full sm:w-auto cell-total-initial">
                                                {{ $sale->total_initial }}
                                            </div>
                                        </td>

                                        <!-- Saldo Cierre -->
                                        <td class="px-3 py-2 text-center border-l theme-border bg-zinc-950/40 col-saldo">
                                            <span class="card-label text-[10px] font-bold text-[#F5B81C] mb-1">Saldo Cierre</span>
                                            <input type="number" 
                                                   inputmode="numeric"
                                                   data-excel-col="2"
                                                   name="sales[{{ $sale->id }}][saldo]" 
                                                   value="{{ $sale->saldo }}" 
                                                   min="0"
                                                   class="excel-input input-saldo w-16 sm:w-20 h-10 sm:h-9 text-center rounded-xl font-mono font-bold text-xs sm:text-sm bg-zinc-950 border-2 border-zinc-700/80 text-white focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all shadow-inner">
                                        </td>

                                        <!-- Consumidas -->
                                        <td class="px-4 py-2.5 text-center border-l theme-border col-mixer-consumido">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Consumo</span>
                                            <div class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-800 font-mono text-zinc-300 text-xs h-10 sm:h-auto w-full sm:w-auto cell-consumido">
                                                {{ $sale->vendido }}
                                            </div>
                                        </td>

                                        <!-- En Combos -->
                                        <td class="px-4 py-2.5 text-center border-l theme-border col-mixer-included">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">En Combos</span>
                                            <div class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-800 font-mono text-zinc-400 text-xs h-10 sm:h-auto w-full sm:w-auto cell-included">
                                                {{ $sale->included_in_combos ?? 0 }}
                                            </div>
                                        </td>

                                        <!-- Extras -->
                                        <td class="px-4 py-2.5 text-center border-l theme-border col-mixer-extras">
                                            <span class="card-label text-[10px] font-bold text-[#F5B81C] mb-1">Extras Cobrar</span>
                                            <div class="inline-flex items-center justify-center px-3 py-1 rounded-xl bg-zinc-950 border border-zinc-800 font-black font-mono text-[#F5B81C] text-sm sm:text-base h-10 sm:h-auto w-full sm:w-auto cell-extras">
                                                {{ $sale->extras ?? 0 }}
                                            </div>
                                        </td>

                                        <!-- Precio Extra -->
                                        <td class="px-4 py-2 text-right border-l theme-border col-mixer-price">
                                            <span class="card-label text-[10px] font-semibold text-zinc-400 mb-1">Precio Extra</span>
                                            <input type="number" 
                                                   step="0.5" 
                                                   inputmode="decimal"
                                                   data-excel-col="3"
                                                   name="sales[{{ $sale->id }}][unit_price]" 
                                                   value="{{ $sale->unit_price }}" 
                                                   min="0"
                                                   class="excel-input input-price w-20 sm:w-24 h-10 sm:h-9 text-right rounded-xl px-2 font-mono font-bold text-xs sm:text-sm bg-zinc-950 border border-zinc-800 text-zinc-200 focus:outline-none focus:ring-2 focus:ring-[#F5B81C] focus:border-[#F5B81C] focus:bg-[#181507] focus:text-[#F5B81C] transition-all">
                                        </td>

                                        <!-- Subtotal Extras -->
                                        <td class="px-4 py-2.5 text-right font-bold font-mono text-white text-xs sm:text-sm border-l theme-border cell-subtotal col-subtotal">
                                            Bs. {{ number_format($sale->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-4 py-8 text-center text-zinc-500 font-sans">No hay mixers registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-zinc-950 border-t theme-border font-bold text-xs text-white">
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-right uppercase tracking-wider font-sans">
                                        <span>Totales Mixers:</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-bold" id="tfoot-mixer-consumido">
                                        {{ $totalMixerConsumed }}
                                    </td>
                                    <td class="hidden sm:table-cell"></td>
                                    <td class="px-4 py-3 text-center font-mono font-black text-[#F5B81C] text-base" id="tfoot-mixer-extras">
                                        {{ $totalMixerExtras }}
                                    </td>
                                    <td class="hidden sm:table-cell"></td>
                                    <td class="px-4 py-3 text-right font-mono font-black text-[#F5B81C] text-sm" id="tfoot-mixer-subtotal">
                                        Bs. {{ number_format($subtotalMixers, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- ======================================================== -->
                <!-- DETALLE DE COBROS DIGITALES (QR Y TARJETAS POS) -->
                <!-- ======================================================== -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    
                    <!-- 1. TABLA COBROS QR EN ESTA BARRA -->
                    <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                        <div class="px-4 py-3 bg-zinc-950 border-b theme-border flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">
                                    Cobros QR en {{ $selectedBar }}
                                </h3>
                                <p class="text-[11px] text-zinc-400">Transferencias YASTA & YAPE</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="text-xs font-mono font-bold text-blue-400">
                                    Bs. {{ number_format($barTotalQr, 2) }}
                                </span>
                                <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" 
                                   class="px-2.5 py-1 text-[10px] font-semibold rounded-lg bg-zinc-900 border border-zinc-800 text-blue-400 hover:text-white transition-all">
                                    Ver QR &rarr;
                                </a>
                            </div>
                        </div>

                        <div class="overflow-x-auto max-h-60 overflow-y-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[10px] tracking-wider sticky top-0 font-semibold">
                                    <tr>
                                        <th class="px-4 py-2.5">Cobrante</th>
                                        <th class="px-3 py-2.5 text-center">App</th>
                                        <th class="px-4 py-2.5">Ref / Comprobante</th>
                                        <th class="px-4 py-2.5 text-right">Monto (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y theme-border font-sans">
                                    @forelse($barQrPayments as $qr)
                                        <tr class="hover:bg-zinc-900/30">
                                            <td class="px-4 py-2 text-zinc-200 font-medium">{{ $qr->operator_name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-zinc-900 border border-zinc-800 text-zinc-300">
                                                    {{ $qr->bank_app }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-zinc-400 text-[11px] truncate max-w-[120px] font-mono">{{ $qr->reference_code ?? '—' }}</td>
                                            <td class="px-4 py-2 text-right font-bold font-mono text-blue-400">Bs. {{ number_format($qr->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-zinc-500 text-xs">No hay cobros QR registrados para esta barra.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. TABLA TARJETAS POS EN ESTA BARRA -->
                    <div class="rounded-2xl theme-card border theme-border overflow-hidden">
                        <div class="px-4 py-3 bg-zinc-950 border-b theme-border flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">
                                    Pagos con Tarjeta (POS) en {{ $selectedBar }}
                                </h3>
                                <p class="text-[11px] text-zinc-400">Facturas y cobro con tarjeta</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="text-xs font-mono font-bold text-purple-400">
                                    Bs. {{ number_format($barTotalCard, 2) }}
                                </span>
                                <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" 
                                   class="px-2.5 py-1 text-[10px] font-semibold rounded-lg bg-zinc-900 border border-zinc-800 text-purple-400 hover:text-white transition-all">
                                    Ver POS &rarr;
                                </a>
                            </div>
                        </div>

                        <div class="overflow-x-auto max-h-60 overflow-y-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[10px] tracking-wider sticky top-0 font-semibold">
                                    <tr>
                                        <th class="px-4 py-2.5 text-center"># Factura</th>
                                        <th class="px-4 py-2.5">Detalle</th>
                                        <th class="px-4 py-2.5 text-right">Comisión</th>
                                        <th class="px-4 py-2.5 text-right">Monto Bruto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y theme-border font-sans">
                                    @forelse($barCardInvoices as $inv)
                                        <tr class="hover:bg-zinc-900/30">
                                            <td class="px-4 py-2 text-center text-zinc-400 font-mono font-bold">#{{ $inv->correlative_num }}</td>
                                            <td class="px-4 py-2 text-zinc-300 text-[11px] truncate max-w-[140px]">{{ $inv->notes ?? 'Pago Tarjeta POS' }}</td>
                                            <td class="px-4 py-2 text-right text-zinc-500 text-[11px] font-mono">- Bs. {{ number_format($inv->commission_amount, 2) }}</td>
                                            <td class="px-4 py-2 text-right font-bold font-mono text-purple-400">Bs. {{ number_format($inv->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-zinc-500 text-xs">No hay pagos con tarjeta registrados para esta barra.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Pie de Formulario Estático con Guardado -->
                <div class="p-4 rounded-2xl theme-card border theme-border flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="w-9 h-9 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-[#F5B81C] shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-400 block font-normal">Total Bebidas Liquidado en {{ $selectedBar }}:</span>
                            <span class="text-lg font-bold font-mono text-white" id="footer-grand-total">
                                Bs. {{ number_format($grandTotalBar, 2) }}
                            </span>
                        </div>
                    </div>

                    @if($session->isOpen())
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#F5B81C] text-black font-bold text-xs uppercase tracking-wider hover:bg-[#e5ac18] transition-all flex items-center justify-center gap-2 dilemo-btn cursor-pointer shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Guardar Cambios de {{ $selectedBar }}</span>
                        </button>
                    @else
                        <div class="px-4 py-2 rounded-xl bg-zinc-950 border border-zinc-800 text-zinc-400 text-xs font-semibold flex items-center gap-2 w-full sm:w-auto justify-center">
                            <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>Noche cerrada (solo lectura)</span>
                        </div>
                    @endif
                </div>

            </div>
        </form>
        @endif

        <!-- Barra Flotante Inferior en Celular (Acceso Rápido a Guardar) -->
        @if($session && $session->isOpen() && $selectedBar !== 'Tienda')
            <div class="sm:hidden fixed bottom-3 left-3 right-3 z-40 p-2.5 rounded-2xl bg-black/95 border border-zinc-800 shadow-2xl backdrop-blur-md flex items-center justify-between gap-2.5">
                <div class="flex flex-col min-w-0">
                    <span class="text-[9px] text-zinc-400 uppercase tracking-wider font-semibold">Total {{ $selectedBar }}:</span>
                    <span class="text-sm font-black font-mono text-[#F5B81C] truncate" id="mobile-sticky-grand-total">
                        Bs. {{ number_format($grandTotalBar, 2) }}
                    </span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span id="sales-autosave-mobile-status" class="text-[10px] font-mono text-zinc-500"></span>
                    <button type="submit" form="sales-form" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all flex items-center gap-1.5 cursor-pointer shadow-md">
                        <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Guardar</span>
                    </button>
                </div>
            </div>
        @endif

        <!-- Script Reactivo para Combos, Deducción Automática y Mixers Especiales -->
        <script>
        const barQrTotal = {{ (float)$barTotalQr }};
        const barCardNetTotal = {{ (float)$barTotalCardNet }};
        let currentActiveRowId = null;


        function openSpecialModal(rowId) {
            currentActiveRowId = rowId;
            const row = document.querySelector(`.liquor-row[data-row-id="${rowId}"]`);
            if (!row) return;

            const liquorName = row.getAttribute('data-product-name');
            const defaultMixerName = row.getAttribute('data-mixer-name');
            const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
            const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
            const units = parseInt(row.querySelector('.input-units').value) || 0;
            const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
            const totalCombos = Math.max(0, (pkg * unitsPerPkg + units) - saldo);

            document.getElementById('modal-liquor-title').textContent = liquorName;
            document.getElementById('modal-total-combos').textContent = totalCombos;
            document.getElementById('modal-default-mixer-name').textContent = defaultMixerName;

            // Cargar valores actuales del input JSON
            const inputJson = document.getElementById(`input-specials-${rowId}`);
            let currentSpecials = {};
            try {
                currentSpecials = JSON.parse(inputJson.value || '{}');
            } catch(e) {
                currentSpecials = {};
            }

            // Llenar inputs del modal
            document.querySelectorAll('.modal-special-qty').forEach(inp => {
                const mId = inp.getAttribute('data-mixer-id');
                inp.value = currentSpecials[mId] || 0;
            });

            updateModalRemaining(totalCombos);
            document.getElementById('modal-assign-special').classList.remove('hidden');
        }

        function updateModalRemaining(totalCombos) {
            let specialSum = 0;
            document.querySelectorAll('.modal-special-qty').forEach(inp => {
                specialSum += parseInt(inp.value) || 0;
            });
            const remaining = Math.max(0, totalCombos - specialSum);
            const remEl = document.getElementById('modal-remaining-default');
            remEl.textContent = remaining;
            if (specialSum > totalCombos) {
                remEl.className = 'font-bold text-rose-400 text-sm font-mono';
                remEl.textContent = `${remaining} (¡Excede los combos!)`;
            } else {
                remEl.className = 'font-bold text-[#F5B81C] text-sm font-mono';
            }
        }

        document.querySelectorAll('.modal-special-qty').forEach(inp => {
            inp.addEventListener('input', function() {
                const totalCombos = parseInt(document.getElementById('modal-total-combos').textContent) || 0;
                updateModalRemaining(totalCombos);
            });
        });

        function closeSpecialModal() {
            document.getElementById('modal-assign-special').classList.add('hidden');
            currentActiveRowId = null;
        }

        function saveSpecialModal() {
            if (!currentActiveRowId) return;

            const rowId = currentActiveRowId;
            const inputJson = document.getElementById(`input-specials-${rowId}`);
            const badgeEl = document.getElementById(`badge-special-${rowId}`);
            const btnEl = document.getElementById(`btn-special-${rowId}`);

            let specialsObj = {};
            let badgeParts = [];

            document.querySelectorAll('.modal-special-qty').forEach(inp => {
                const qty = parseInt(inp.value) || 0;
                const mId = inp.getAttribute('data-mixer-id');
                const mName = inp.getAttribute('data-mixer-name');
                if (qty > 0) {
                    specialsObj[mId] = qty;
                    const shortName = mName.split(' ')[0];
                    badgeParts.push(`${shortName} (${qty})`);
                }
            });

            inputJson.value = JSON.stringify(specialsObj);

            if (badgeParts.length > 0) {
                badgeEl.textContent = badgeParts.join(', ');
                btnEl.className = 'px-2.5 py-1 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 cursor-pointer border bg-zinc-900 text-[#F5B81C] border-[#F5B81C]/50';
            } else {
                badgeEl.textContent = '+ Especial';
                btnEl.className = 'px-2.5 py-1 rounded-lg text-xs font-semibold transition-all inline-flex items-center gap-1 cursor-pointer border bg-zinc-950 text-zinc-400 border-zinc-800 hover:text-white';
            }

            closeSpecialModal();
            window.recalculateSales();
            window.queueSalesAutosave?.();
        }

        document.addEventListener('DOMContentLoaded', function () {

            const liquorRows = document.querySelectorAll('.liquor-row');
            const mixerRows = document.querySelectorAll('.mixer-row');
            const salesForm = document.getElementById('sales-form');
            const autosaveStatus = document.getElementById('sales-autosave-status');
            const mobileAutosaveStatus = document.getElementById('sales-autosave-mobile-status');
            let autosaveTimer = null;
            let autosaveInFlight = false;
            let autosaveQueued = false;

            function setAutosaveStatus(message, className) {
                if (autosaveStatus) {
                    autosaveStatus.textContent = message;
                    autosaveStatus.className = `text-[10px] font-mono ${className}`;
                }
                if (mobileAutosaveStatus) {
                    mobileAutosaveStatus.textContent = message;
                    mobileAutosaveStatus.className = `text-[10px] font-mono ${className}`;
                }
            }

            async function autosaveSales() {
                if (!salesForm) return;
                if (autosaveInFlight) {
                    autosaveQueued = true;
                    return;
                }

                autosaveInFlight = true;
                setAutosaveStatus('Guardando...', 'text-[#F5B81C]');
                try {
                    const response = await fetch(salesForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(salesForm)
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    setAutosaveStatus('Guardado', 'text-emerald-400');
                } catch (error) {
                    console.error('Error en autoguardado de ventas:', error);
                    setAutosaveStatus('Error al guardar', 'text-rose-400');
                } finally {
                    autosaveInFlight = false;
                    if (autosaveQueued) {
                        autosaveQueued = false;
                        queueAutosave();
                    }
                }
            }

            function queueAutosave() {
                if (!salesForm) return;
                clearTimeout(autosaveTimer);
                setAutosaveStatus('Pendiente...', 'text-zinc-400');
                autosaveTimer = setTimeout(autosaveSales, 450);
            }

            window.queueSalesAutosave = queueAutosave;

            if (!liquorRows.length && !mixerRows.length) return;

            window.recalculateSales = function() {
                let combosPerMixer = {};
                let totalLiquorCombos = 0;
                let subtotalLiquors = 0;

                liquorRows.forEach(row => {
                    const rowId = row.getAttribute('data-row-id');
                    const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                    const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
                    const units = parseInt(row.querySelector('.input-units').value) || 0;
                    const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
                    const price = parseFloat(row.querySelector('.input-price').value) || 0;
                    const defaultMixerId = parseInt(row.getAttribute('data-mixer-id')) || 0;
                    const mixerRatio = parseInt(row.getAttribute('data-mixer-ratio')) || 1;

                    const totalInitial = (pkg * unitsPerPkg) + units;
                    const vendido = Math.max(0, totalInitial - saldo);
                    const subtotal = vendido * price;

                    const cellTotIni = row.querySelector('.cell-total-initial');
                    if (cellTotIni) cellTotIni.textContent = totalInitial;

                    const cellVend = row.querySelector('.cell-vendido');
                    if (cellVend) cellVend.textContent = vendido;

                    const cellSub = row.querySelector('.cell-subtotal');
                    if (cellSub) cellSub.textContent = 'Bs. ' + subtotal.toFixed(2);

                    totalLiquorCombos += vendido;
                    subtotalLiquors += subtotal;

                    // Parsear especiales asignados a esta fila
                    const specialInput = document.getElementById(`input-specials-${rowId}`);
                    let specials = {};
                    try {
                        specials = JSON.parse(specialInput.value || '{}');
                    } catch(e) {
                        specials = {};
                    }

                    let specialCount = 0;
                    for (const [mId, qty] of Object.entries(specials)) {
                        const q = parseInt(qty) || 0;
                        if (q > 0) {
                            combosPerMixer[mId] = (combosPerMixer[mId] || 0) + q;
                            specialCount += q;
                        }
                    }

                    // El mixer por defecto recibe el remanente
                    if (defaultMixerId > 0) {
                        const remaining = Math.max(0, vendido - specialCount);
                        combosPerMixer[defaultMixerId] = (combosPerMixer[defaultMixerId] || 0) + (remaining * mixerRatio);
                    }
                });

                let totalMixerConsumed = 0;
                let totalMixerExtras = 0;
                let subtotalMixers = 0;

                mixerRows.forEach(row => {
                    const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                    const pkg = parseInt(row.querySelector('.input-packages').value) || 0;
                    const units = parseInt(row.querySelector('.input-units').value) || 0;
                    const saldo = parseInt(row.querySelector('.input-saldo').value) || 0;
                    const price = parseFloat(row.querySelector('.input-price').value) || 0;
                    const productId = parseInt(row.getAttribute('data-product-id')) || 0;

                    const totalInitial = (pkg * unitsPerPkg) + units;
                    const consumido = Math.max(0, totalInitial - saldo);
                    const included = combosPerMixer[productId] || 0;
                    const extras = Math.max(0, consumido - included);
                    const subtotal = extras * price;

                    const cellTotIni = row.querySelector('.cell-total-initial');
                    if (cellTotIni) cellTotIni.textContent = totalInitial;

                    const cellCons = row.querySelector('.cell-consumido');
                    if (cellCons) cellCons.textContent = consumido;

                    const cellInc = row.querySelector('.cell-included');
                    if (cellInc) cellInc.textContent = included;

                    const cellExt = row.querySelector('.cell-extras');
                    if (cellExt) cellExt.textContent = extras;

                    const cellSub = row.querySelector('.cell-subtotal');
                    if (cellSub) cellSub.textContent = 'Bs. ' + subtotal.toFixed(2);

                    totalMixerConsumed += consumido;
                    totalMixerExtras += extras;
                    subtotalMixers += subtotal;
                });

                const storeSalesTotalEl = document.getElementById('store-sales-total-val');
                const storeSalesTotal = storeSalesTotalEl ? parseFloat(storeSalesTotalEl.value || 0) : 0;
                const grandTotal = subtotalLiquors + subtotalMixers + storeSalesTotal;
                const cashRemaining = Math.max(0, grandTotal - barQrTotal - barCardNetTotal);

                const headerCombos = document.getElementById('header-total-combos');
                if (headerCombos) headerCombos.textContent = `${totalLiquorCombos} combos`;

                const headerSubtotalCombosVal = document.getElementById('header-subtotal-combos-val');
                if (headerSubtotalCombosVal) headerSubtotalCombosVal.textContent = subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const badgeLiquorSubtotal = document.getElementById('badge-liquor-subtotal');
                if (badgeLiquorSubtotal) badgeLiquorSubtotal.textContent = 'Subtotal: Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const tfootLiquorVendido = document.getElementById('tfoot-liquor-vendido');
                if (tfootLiquorVendido) tfootLiquorVendido.textContent = totalLiquorCombos;

                const tfootLiquorSubtotal = document.getElementById('tfoot-liquor-subtotal');
                if (tfootLiquorSubtotal) tfootLiquorSubtotal.textContent = 'Bs. ' + subtotalLiquors.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const headerExtras = document.getElementById('header-total-extras');
                if (headerExtras) headerExtras.textContent = `${totalMixerExtras} extras`;

                const headerSubtotalExtrasVal = document.getElementById('header-subtotal-extras-val');
                if (headerSubtotalExtrasVal) headerSubtotalExtrasVal.textContent = subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const badgeMixerSubtotal = document.getElementById('badge-mixer-subtotal');
                if (badgeMixerSubtotal) badgeMixerSubtotal.textContent = 'Subtotal Extras: Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const tfootMixerConsumido = document.getElementById('tfoot-mixer-consumido');
                if (tfootMixerConsumido) tfootMixerConsumido.textContent = totalMixerConsumed;

                const tfootMixerExtras = document.getElementById('tfoot-mixer-extras');
                if (tfootMixerExtras) tfootMixerExtras.textContent = totalMixerExtras;

                const tfootMixerSubtotal = document.getElementById('tfoot-mixer-subtotal');
                if (tfootMixerSubtotal) tfootMixerSubtotal.textContent = 'Bs. ' + subtotalMixers.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const headerGrandTotal = document.getElementById('header-grand-total');
                if (headerGrandTotal) headerGrandTotal.innerHTML = `<span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>${grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                const headerBarCash = document.getElementById('header-bar-cash');
                if (headerBarCash) headerBarCash.textContent = cashRemaining.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const footerGrandTotal = document.getElementById('footer-grand-total');
                if (footerGrandTotal) footerGrandTotal.textContent = 'Bs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const mobileStickyGrandTotal = document.getElementById('mobile-sticky-grand-total');
                if (mobileStickyGrandTotal) mobileStickyGrandTotal.textContent = 'Bs. ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            /* ========================================================
               MOTOR DE NAVEGACIÓN TIPO EXCEL
               ======================================================== */
            function initExcelNavigation(form) {
                if (!form) return;

                function getMatrix(currentTable) {
                    const table = currentTable || form;
                    const visibleRows = Array.from(table.querySelectorAll('.liquor-row, .mixer-row')).filter(tr => tr.style.display !== 'none');
                    return visibleRows.map(tr => {
                        const inputs = Array.from(tr.querySelectorAll('input[data-excel-col]')).filter(inp => !inp.disabled);
                        inputs.sort((a, b) => parseInt(a.dataset.excelCol) - parseInt(b.dataset.excelCol));
                        return { tr, inputs };
                    });
                }

                form.addEventListener('focusin', function (e) {
                    const input = e.target;
                    if (!input.matches('input[data-excel-col]')) return;

                    setTimeout(() => {
                        if (document.activeElement === input) {
                            try { input.select(); } catch (err) {}
                        }
                    }, 25);

                    form.querySelectorAll('.liquor-row, .mixer-row').forEach(r => r.classList.remove('excel-active-row'));
                    const tr = input.closest('tr');
                    if (tr) tr.classList.add('excel-active-row');
                });

                form.addEventListener('focusout', function (e) {
                    const input = e.target;
                    if (!input.matches('input[data-excel-col]')) return;
                    const tr = input.closest('tr');
                    if (tr && !tr.contains(document.activeElement)) {
                        tr.classList.remove('excel-active-row');
                    }
                });

                form.addEventListener('keydown', function (e) {
                    const input = e.target;
                    if (!input.matches('input[data-excel-col]')) return;

                    const key = e.key;
                    if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Enter', 'Tab'].includes(key)) {
                        return;
                    }

                    const currentTable = input.closest('table');
                    const matrix = getMatrix(currentTable);
                    const currentTr = input.closest('tr');
                    const rowIndex = matrix.findIndex(item => item.tr === currentTr);
                    if (rowIndex === -1) return;

                    const rowInputs = matrix[rowIndex].inputs;
                    const colIndex = rowInputs.indexOf(input);
                    if (colIndex === -1) return;

                    let targetInput = null;

                    if (key === 'ArrowDown') {
                        e.preventDefault();
                        if (rowIndex + 1 < matrix.length) {
                            const nextRowInputs = matrix[rowIndex + 1].inputs;
                            targetInput = nextRowInputs[colIndex] || nextRowInputs[nextRowInputs.length - 1];
                        }
                    } else if (key === 'ArrowUp') {
                        e.preventDefault();
                        if (rowIndex - 1 >= 0) {
                            const prevRowInputs = matrix[rowIndex - 1].inputs;
                            targetInput = prevRowInputs[colIndex] || prevRowInputs[prevRowInputs.length - 1];
                        }
                    } else if (key === 'Enter') {
                        e.preventDefault();
                        if (e.shiftKey) {
                            if (rowIndex - 1 >= 0) {
                                const prevRowInputs = matrix[rowIndex - 1].inputs;
                                targetInput = prevRowInputs[colIndex] || prevRowInputs[prevRowInputs.length - 1];
                            }
                        } else {
                            if (rowIndex + 1 < matrix.length) {
                                const nextRowInputs = matrix[rowIndex + 1].inputs;
                                targetInput = nextRowInputs[colIndex] || nextRowInputs[nextRowInputs.length - 1];
                            }
                        }
                    } else if (key === 'ArrowRight' || (key === 'Tab' && !e.shiftKey)) {
                        e.preventDefault();
                        if (colIndex + 1 < rowInputs.length) {
                            targetInput = rowInputs[colIndex + 1];
                        } else if (rowIndex + 1 < matrix.length) {
                            targetInput = matrix[rowIndex + 1].inputs[0];
                        }
                    } else if (key === 'ArrowLeft' || (key === 'Tab' && e.shiftKey)) {
                        e.preventDefault();
                        if (colIndex - 1 >= 0) {
                            targetInput = rowInputs[colIndex - 1];
                        } else if (rowIndex - 1 >= 0) {
                            const prevRowInputs = matrix[rowIndex - 1].inputs;
                            targetInput = prevRowInputs[prevRowInputs.length - 1];
                        }
                    }

                    if (targetInput) {
                        targetInput.focus();
                        try { targetInput.select(); } catch (err) {}
                    }
                });
            }

            if (salesForm) initExcelNavigation(salesForm);

            document.querySelectorAll('#sales-form input').forEach(input => {
                input.addEventListener('input', window.recalculateSales);
                if (input.type === 'number') input.addEventListener('change', queueAutosave);
            });

            // Recalcular inmediatamente al cargar la página
            if (typeof window.recalculateSales === 'function') {
                window.recalculateSales();
            }
        });

        /* ========================================================
           TERMINAL POS TÁCTIL DE TIENDA (VENTA DIRECTA)
           ======================================================== */
        let tiendaCart = [];
        let tiendaActiveCategory = 'todas';
        let tiendaActivePaymentPreset = 'efectivo';
        let tiendaActiveCobrante = 'DON LUDO';
        let tiendaActiveBankApp = 'YASTA';

        window.addDrinkToTiendaCart = function(id, name, price, imageUrl, stock) {
            const existing = tiendaCart.find(item => item.id === id);
            if (existing) {
                existing.quantity += 1;
            } else {
                tiendaCart.push({
                    id: id,
                    name: name,
                    unit_price: parseFloat(price) || 0,
                    image_url: imageUrl,
                    stock: stock,
                    quantity: 1
                });
            }
            window.renderTiendaCart();
        };

        window.updateTiendaCartItemQty = function(id, delta) {
            const index = tiendaCart.findIndex(item => item.id === id);
            if (index !== -1) {
                tiendaCart[index].quantity += delta;
                if (tiendaCart[index].quantity <= 0) {
                    tiendaCart.splice(index, 1);
                }
            }
            window.renderTiendaCart();
        };

        window.removeDrinkFromTiendaCart = function(id) {
            tiendaCart = tiendaCart.filter(item => item.id !== id);
            window.renderTiendaCart();
        };

        window.clearTiendaCart = function() {
            tiendaCart = [];
            window.renderTiendaCart();
        };

        window.renderTiendaCart = function() {
            const listEl = document.getElementById('tienda-cart-list');
            const hiddenContainer = document.getElementById('tienda-cart-hidden-inputs');
            const badgeCountEl = document.getElementById('tienda-cart-badge-count');
            const totalDisplayEl = document.getElementById('tienda-cart-total-display');
            const submitBtn = document.getElementById('tienda-btn-submit');
            const submitText = document.getElementById('tienda-btn-submit-text');

            if (!listEl) return;

            const totalItems = tiendaCart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = tiendaCart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);

            if (badgeCountEl) {
                badgeCountEl.textContent = `${totalItems} ${totalItems === 1 ? 'item' : 'items'}`;
            }
            if (totalDisplayEl) {
                totalDisplayEl.textContent = 'Bs. ' + totalPrice.toFixed(2);
            }

            // Hidden inputs para el form
            if (hiddenContainer) {
                let hiddenHtml = '';
                tiendaCart.forEach((item, idx) => {
                    hiddenHtml += `
                        <input type="hidden" name="orders[${idx}][product_id]" value="${item.id}">
                        <input type="hidden" name="orders[${idx}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="orders[${idx}][unit_price]" value="${item.unit_price}">
                    `;
                });
                hiddenContainer.innerHTML = hiddenHtml;
            }

            // Render items list
            if (tiendaCart.length === 0) {
                listEl.innerHTML = `
                    <div id="tienda-cart-placeholder" class="text-center py-8 text-zinc-500">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-40 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs font-medium text-zinc-400">Ninguna bebida seleccionada</p>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Toca los botones grandes de la izquierda para agregar.</p>
                    </div>
                `;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.className = 'w-full py-3.5 px-4 rounded-xl bg-zinc-800 text-zinc-500 font-black text-xs sm:text-sm uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-not-allowed shadow-none';
                }
                if (submitText) {
                    submitText.textContent = 'SELECCIONA BEBIDAS PARA COBRAR';
                }
            } else {
                let html = '';
                tiendaCart.forEach(item => {
                    const subtotal = item.quantity * item.unit_price;
                    html += `
                        <div class="p-2.5 rounded-xl bg-zinc-950/80 border border-zinc-850 flex items-center justify-between gap-2 transition-all hover:border-zinc-700">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <img src="${item.image_url}" alt="${item.name}" class="w-8 h-8 object-contain shrink-0 rounded-lg bg-zinc-900 p-0.5 border border-zinc-800">
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-white truncate">${item.name}</div>
                                    <div class="text-[10px] text-zinc-400 font-mono">Bs. ${item.unit_price.toFixed(2)} c/u</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <div class="flex items-center rounded-lg bg-zinc-900 border border-zinc-800 p-0.5">
                                    <button type="button" onclick="updateTiendaCartItemQty(${item.id}, -1)" 
                                            class="w-6 h-6 rounded-md bg-zinc-950 hover:bg-zinc-800 text-zinc-300 hover:text-white flex items-center justify-center font-bold text-xs cursor-pointer transition-all">
                                        -
                                    </button>
                                    <span class="w-7 text-center font-mono font-bold text-xs text-white">${item.quantity}</span>
                                    <button type="button" onclick="updateTiendaCartItemQty(${item.id}, 1)" 
                                            class="w-6 h-6 rounded-md bg-zinc-950 hover:bg-zinc-800 text-zinc-300 hover:text-white flex items-center justify-center font-bold text-xs cursor-pointer transition-all">
                                        +
                                    </button>
                                </div>
                                <div class="w-16 text-right font-mono font-bold text-xs text-[#F5B81C]">
                                    Bs. ${subtotal.toFixed(2)}
                                </div>
                                <button type="button" onclick="removeDrinkFromTiendaCart(${item.id})" 
                                        title="Quitar" 
                                        class="text-zinc-500 hover:text-rose-400 text-sm font-bold px-1 transition-colors cursor-pointer">
                                    &times;
                                </button>
                            </div>
                        </div>
                    `;
                });
                listEl.innerHTML = html;

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.className = 'w-full py-3.5 px-4 rounded-xl bg-[#F5B81C] hover:bg-[#e5ac18] text-black font-black text-xs sm:text-sm uppercase tracking-wider transition-all flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-[#F5B81C]/20 dilemo-btn active:scale-[0.99]';
                }
                if (submitText) {
                    submitText.textContent = `REGISTRAR COBRO (Bs. ${totalPrice.toFixed(2)})`;
                }
            }

            window.syncTiendaPaymentInputs(totalPrice);
        };

        window.syncTiendaPaymentInputs = function(total) {
            const cashInput = document.getElementById('tienda-input-cash');
            const qrInput = document.getElementById('tienda-input-qr');
            const cardInput = document.getElementById('tienda-input-card');

            if (!cashInput || !qrInput || !cardInput) return;

            if (tiendaActivePaymentPreset === 'efectivo') {
                cashInput.value = total.toFixed(2);
                qrInput.value = '0';
                cardInput.value = '0';
            } else if (tiendaActivePaymentPreset === 'qr') {
                cashInput.value = '0';
                qrInput.value = total.toFixed(2);
                cardInput.value = '0';
            } else if (tiendaActivePaymentPreset === 'tarjeta') {
                cashInput.value = '0';
                qrInput.value = '0';
                cardInput.value = total.toFixed(2);
            } else if (tiendaActivePaymentPreset === 'mixto') {
                const mCash = document.getElementById('tienda-manual-cash');
                const mQr = document.getElementById('tienda-manual-qr');
                const mCard = document.getElementById('tienda-manual-card');
                if (mCash && mQr && mCard) {
                    const currentSum = (parseFloat(mCash.value) || 0) + (parseFloat(mQr.value) || 0) + (parseFloat(mCard.value) || 0);
                    if (currentSum === 0) {
                        mCash.value = total.toFixed(2);
                        mQr.value = '0';
                        mCard.value = '0';
                    }
                    cashInput.value = mCash.value;
                    qrInput.value = mQr.value;
                    cardInput.value = mCard.value;
                    updateTiendaSplitDifference(total);
                }
            }
        };

        window.setTiendaPaymentPreset = function(preset) {
            tiendaActivePaymentPreset = preset;
            const buttons = {
                'efectivo': document.getElementById('btn-pay-efectivo'),
                'qr': document.getElementById('btn-pay-qr'),
                'tarjeta': document.getElementById('btn-pay-tarjeta'),
                'mixto': document.getElementById('btn-pay-mixto')
            };

            Object.keys(buttons).forEach(key => {
                const btn = buttons[key];
                if (!btn) return;
                if (key === preset) {
                    btn.className = 'tienda-pay-btn py-2 px-2.5 rounded-xl border font-bold flex items-center justify-center gap-1.5 transition-all bg-[#F5B81C] text-black border-[#F5B81C] shadow-sm';
                } else {
                    btn.className = 'tienda-pay-btn py-2 px-2.5 rounded-xl border font-semibold flex items-center justify-center gap-1.5 transition-all bg-zinc-950 border-zinc-800 text-zinc-400 hover:text-white';
                }
            });

            const qrPicker = document.getElementById('tienda-qr-app-picker');
            const splitInputs = document.getElementById('tienda-split-inputs');

            if (qrPicker) {
                if (preset === 'qr') {
                    qrPicker.classList.remove('hidden');
                } else {
                    qrPicker.classList.add('hidden');
                }
            }

            if (splitInputs) {
                if (preset === 'mixto') {
                    splitInputs.classList.remove('hidden');
                } else {
                    splitInputs.classList.add('hidden');
                }
            }

            const totalPrice = tiendaCart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            window.syncTiendaPaymentInputs(totalPrice);
        };

        window.setTiendaBankApp = function(app) {
            tiendaActiveBankApp = app;
            const input = document.getElementById('tienda-input-bank-app');
            if (input) input.value = app;

            const btnYasta = document.getElementById('btn-app-yasta');
            const btnYape = document.getElementById('btn-app-yape');

            if (app === 'YASTA') {
                if (btnYasta) btnYasta.className = 'py-1.5 rounded-lg text-xs font-bold border transition-all bg-[#004b93] text-white border-blue-400';
                if (btnYape) btnYape.className = 'py-1.5 rounded-lg text-xs font-bold border transition-all bg-zinc-900 text-zinc-400 border-zinc-800 hover:text-white';
            } else {
                if (btnYasta) btnYasta.className = 'py-1.5 rounded-lg text-xs font-bold border transition-all bg-zinc-900 text-zinc-400 border-zinc-800 hover:text-white';
                if (btnYape) btnYape.className = 'py-1.5 rounded-lg text-xs font-bold border transition-all bg-[#742284] text-white border-purple-400';
            }
        };

        window.setTiendaCobrante = function(cobrante) {
            const input = document.getElementById('tienda-input-cobrante');
            if (input) input.value = cobrante;
        };

        window.onTiendaManualPaymentChange = function() {
            const mCash = parseFloat(document.getElementById('tienda-manual-cash').value) || 0;
            const mQr = parseFloat(document.getElementById('tienda-manual-qr').value) || 0;
            const mCard = parseFloat(document.getElementById('tienda-manual-card').value) || 0;

            document.getElementById('tienda-input-cash').value = mCash.toFixed(2);
            document.getElementById('tienda-input-qr').value = mQr.toFixed(2);
            document.getElementById('tienda-input-card').value = mCard.toFixed(2);

            const qrPicker = document.getElementById('tienda-qr-app-picker');
            if (qrPicker) {
                if (mQr > 0) qrPicker.classList.remove('hidden');
                else qrPicker.classList.add('hidden');
            }

            const totalPrice = tiendaCart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            updateTiendaSplitDifference(totalPrice);
        };

        function updateTiendaSplitDifference(total) {
            const mCash = parseFloat(document.getElementById('tienda-manual-cash').value) || 0;
            const mQr = parseFloat(document.getElementById('tienda-manual-qr').value) || 0;
            const mCard = parseFloat(document.getElementById('tienda-manual-card').value) || 0;
            const sum = mCash + mQr + mCard;
            const diff = total - sum;

            const diffEl = document.getElementById('tienda-split-diff-msg');
            if (!diffEl) return;

            if (Math.abs(diff) < 0.01) {
                diffEl.textContent = '✓ Total cuadrado: Bs. ' + sum.toFixed(2);
                diffEl.className = 'text-[10px] text-emerald-400 font-mono text-right font-bold';
            } else if (diff > 0) {
                diffEl.textContent = `Falta asignar: Bs. ${diff.toFixed(2)}`;
                diffEl.className = 'text-[10px] text-amber-400 font-mono text-right font-bold';
            } else {
                diffEl.textContent = `Excede por: Bs. ${Math.abs(diff).toFixed(2)}`;
                diffEl.className = 'text-[10px] text-rose-400 font-mono text-right font-bold';
            }
        }

        window.setTiendaCategory = function(cat, btn) {
            tiendaActiveCategory = cat;
            document.querySelectorAll('.tienda-cat-btn').forEach(b => {
                b.className = 'tienda-cat-btn px-3 py-1.5 rounded-xl font-semibold text-xs cursor-pointer transition-all bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white hover:border-zinc-700 shrink-0';
            });
            if (btn) {
                btn.className = 'tienda-cat-btn px-3 py-1.5 rounded-xl font-bold text-xs cursor-pointer transition-all bg-[#F5B81C] text-black shadow-sm shrink-0';
            }
            window.filterTiendaDrinks();
        };

        window.filterTiendaDrinks = function() {
            const term = (document.getElementById('tienda-search-input')?.value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.tienda-drink-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cat = card.getAttribute('data-category-key') || '';
                const name = card.getAttribute('data-name') || '';

                const matchesCat = (tiendaActiveCategory === 'todas') || (cat === tiendaActiveCategory);
                const matchesTerm = !term || name.includes(term);

                if (matchesCat && matchesTerm) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            const emptySearchEl = document.getElementById('tienda-empty-search');
            if (emptySearchEl) {
                if (visibleCount === 0) emptySearchEl.classList.remove('hidden');
                else emptySearchEl.classList.add('hidden');
            }
        };

        window.validateAndSubmitTiendaPos = function(event) {
            if (tiendaCart.length === 0) {
                event.preventDefault();
                alert('Selecciona al menos una bebida para registrar el despacho.');
                return false;
            }

            const cobranteInput = document.getElementById('tienda-input-cobrante');
            if (!cobranteInput || !cobranteInput.value.trim()) {
                event.preventDefault();
                alert('Por favor escribe el nombre del mesero que retira o cobra.');
                cobranteInput?.focus();
                return false;
            }

            const totalPrice = tiendaCart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
            const cash = parseFloat(document.getElementById('tienda-input-cash')?.value) || 0;
            const qr = parseFloat(document.getElementById('tienda-input-qr')?.value) || 0;
            const card = parseFloat(document.getElementById('tienda-input-card')?.value) || 0;
            const paymentTotal = cash + qr + card;

            if (Math.abs(totalPrice - paymentTotal) > 0.01) {
                event.preventDefault();
                alert(`La suma del pago (Bs. ${paymentTotal.toFixed(2)}) no coincide con el total de las bebidas (Bs. ${totalPrice.toFixed(2)}). Ajusta los montos.`);
                return false;
            }

            return true;
        };
        </script>

    @endif

</div>
@endsection
