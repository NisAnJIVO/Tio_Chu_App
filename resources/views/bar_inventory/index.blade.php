@extends('layouts.app')

@section('title', 'Inventario de Barras')

@section('content')

<style>
    /* Ocultar flechas de números nativas del navegador */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
        appearance: textfield !important;
    }

    /* ========================================================
       ESTILOS DE IMPRESIÓN OFICIAL DON LUDO (IDÉNTICO A PLANILLA FÍSICA)
       ======================================================== */
    @media print {
        #main-sidebar, header, nav, .print-hide, .no-print,
        .screen-only, #bar-inventory-form, .dilemo-btn,
        #flash-success-toast, #flash-error-toast, #discreet-toast,
        button, input, select, form {
            display: none !important;
        }

        body, html, main, #bar-inventory-app {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: visible !important;
        }

        .print-only {
            display: block !important;
        }

        @page {
            size: portrait;
            margin: 6mm 10mm 6mm 10mm;
        }

        .don-ludo-print-table {
            width: 100% !important;
            border-collapse: collapse !important;
            border: 2px solid #000000 !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            color: #000000 !important;
            background: #ffffff !important;
        }

        .don-ludo-print-table th,
        .don-ludo-print-table td {
            border: 1px solid #000000 !important;
            color: #000000 !important;
        }

        .don-ludo-print-table .header-main {
            border-bottom: 2px solid #000000 !important;
            text-align: center !important;
            font-size: 14px !important;
            font-weight: 900 !important;
            letter-spacing: 0.08em !important;
            padding: 4px 6px !important;
            text-transform: uppercase !important;
            color: #000000 !important;
            background: #ffffff !important;
        }

        .don-ludo-print-table .header-info {
            border-bottom: 2px solid #000000 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            padding: 3.5px 8px !important;
            color: #000000 !important;
            background: #ffffff !important;
        }

        .don-ludo-print-table .header-value {
            font-weight: 900 !important;
            margin-left: 6px !important;
            font-size: 12px !important;
            text-transform: capitalize !important;
        }

        .don-ludo-print-table .header-bar-badge {
            font-size: 10px !important;
            font-weight: normal !important;
            margin-left: 4px !important;
            color: #444444 !important;
        }

        .don-ludo-print-table .col-header {
            border-bottom: 2px solid #000000 !important;
            font-size: 10.5px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            padding: 3px 6px !important;
            color: #000000 !important;
            background: #ffffff !important;
        }

        .don-ludo-print-table .cell-num {
            text-align: center !important;
            font-weight: bold !important;
            font-family: monospace, sans-serif !important;
            font-size: 11px !important;
            padding: 2.5px 4px !important;
            width: 6% !important;
        }

        .don-ludo-print-table .cell-desc {
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 10px !important;
            padding: 2.5px 6px !important;
            white-space: nowrap !important;
            width: 54% !important;
        }

        .don-ludo-print-table .cell-qty {
            text-align: center !important;
            font-weight: 900 !important;
            font-family: monospace, sans-serif !important;
            font-size: 12px !important;
            padding: 2.5px 4px !important;
            width: 20% !important;
        }
    }
</style>

<div class="space-y-4 max-w-7xl mx-auto w-full pb-8" id="bar-inventory-app">

    @if($session)
        <!-- ========================================================
             PLANILLA DE INVENTARIO FÍSICA DON LUDO (SOLO IMPRESIÓN)
             Idéntica a la planilla manual oficial de Tío Chu
             ======================================================== -->
        <div class="hidden print-only font-sans text-black bg-white">
            <table class="don-ludo-print-table">
                <thead>
                    <!-- 1. TÍTULO PRINCIPAL -->
                    <tr>
                        <th colspan="4" class="header-main">
                            INVENTARIO BEBIDAS
                        </th>
                    </tr>
                    <!-- 2. NOMBRE Y FECHA -->
                    <tr>
                        <th colspan="2" class="header-info" style="text-align: left; width: 60%;">
                            <span>NOMBRE:</span>
                            <span class="header-value">
                                {{ str_contains($selectedBar, 'Subte') ? 'Ariel' : 'Kelly' }}
                            </span>
                            <span class="header-bar-badge">({{ $selectedBar }})</span>
                        </th>
                        <th colspan="2" class="header-info" style="text-align: left; width: 40%;">
                            <span>DIA Y FECHA:</span>
                            <span class="header-value">
                                {{ ucfirst($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/y') }}
                            </span>
                        </th>
                    </tr>
                    <!-- 3. CABECERA DE COLUMNAS -->
                    <tr>
                        <th class="col-header" style="text-align: center;">N°</th>
                        <th class="col-header" style="text-align: left;">DESCRIPCION</th>
                        <th class="col-header" style="text-align: center;">PAQUETE</th>
                        <th class="col-header" style="text-align: center;">UNIDAD</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $printSales = $allBarSales->sortBy('product_id')->values();
                    @endphp
                    @forelse($printSales as $idx => $sale)
                        @php
                            $prodName = $sale->product ? mb_strtoupper($sale->product->name) : '—';
                            $prodName = str_replace(
                                ['GINGER ALE 2.0L', 'COCA COLA 2.0L', 'AGUA VITAL 2.0L', 'AGUA TÓNICA 1.0L', 'SPRITE 2.0L', 'AQUARIUS PERA 2.0L', 'AGUA VITAL 600 ML'],
                                ['GINGER ALE 2.0 LITROS', 'COCA COLA 2.0 LITROS', 'AGUA VITAL 2.0 LITROS', 'AGUA TONICA 1.0 LITRO', 'SPRITE 2.0 LITROS', 'AQUARIUS PERA 2.0 LITROS', 'AGUA VITAL 600 ML'],
                                $prodName
                            );
                            $pkgNotation = $sale->getPrintPackageNotation();
                            $unitNotation = $sale->getPrintUnitNotation();
                        @endphp
                        <tr>
                            <td class="cell-num">{{ $idx + 1 }}</td>
                            <td class="cell-desc">{{ $prodName }}</td>
                            <td class="cell-qty">{{ $pkgNotation }}</td>
                            <td class="cell-qty">{{ $unitNotation }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 10px;">No hay productos asignados a esta barra.</td>
                        </tr>
                    @endforelse

                    <!-- Filas adicionales vacías como en la planilla original para extras manuales -->
                    <tr>
                        <td class="cell-num">{{ $printSales->count() + 1 }}</td>
                        <td class="cell-desc"></td>
                        <td class="cell-qty"></td>
                        <td class="cell-qty"></td>
                    </tr>
                    <tr>
                        <td class="cell-num">{{ $printSales->count() + 2 }}</td>
                        <td class="cell-desc"></td>
                        <td class="cell-qty"></td>
                        <td class="cell-qty"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <!-- CONTENIDO INTERACTIVO VISIBLE EN PANTALLA -->
    <div class="screen-only space-y-4">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: TÍTULO, NOCHE & ACCIÓN DE GUARDAR
         ======================================================== -->
    <div class="notoria-card px-5 py-3.5 flex flex-col md:flex-row md:items-center md:justify-between gap-3 shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                Inventario de Barras
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('barInventory.index') }}" class="flex items-center">
                    <input type="hidden" name="bar" value="{{ $selectedBar }}">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl notoria-subtile text-xs hover:border-[#F5B81C] transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" onchange="this.form.submit()" 
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

            <!-- Botón Imprimir Planilla Inventario Oficial Don Ludo -->
            @if($session)
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 rounded-xl notoria-subtile hover:border-amber-500 text-xs font-bold theme-text-primary transition-all flex items-center gap-1.5 cursor-pointer dilemo-btn shadow-sm active:scale-95">
                    <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Imprimir Planilla</span>
                </button>
            @endif

            <!-- Estado de Noche Cerrada -->
            @if($session && !$session->isOpen())
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl notoria-subtile text-xs theme-text-muted">
                    <svg class="w-3.5 h-3.5 theme-text-muted shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Modo lectura</span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="text-[#F5B81C] hover:underline ml-1 font-semibold">Cierre &rarr;</a>
                </div>
            @endif

            <!-- Copiar Sobrante de Noche Anterior -->
            @if($session && $previousSession && $session->isOpen())
                <form method="POST" action="{{ route('barInventory.sync') }}" onsubmit="return confirm('¿Copiar lo que sobró de la noche anterior ({{ \Carbon\Carbon::parse($previousSession->session_date)->format('d/m') }}) como saldo inicial?');">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $session->id }}">
                    <input type="hidden" name="bar_name" value="{{ $selectedBar }}">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl notoria-subtile hover:border-amber-500 text-xs font-semibold theme-text-primary transition-all flex items-center gap-1.5 cursor-pointer dilemo-btn">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Traer Sobrante de Ayer</span>
                    </button>
                </form>
            @endif

            <!-- Botón Principal Guardar -->
            @if($session && $session->isOpen())
                <span id="inventory-autosave-status" class="text-[10px] font-mono theme-text-muted" aria-live="polite"></span>
                <button type="submit" form="bar-inventory-form" class="px-4 py-1.5 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all flex items-center gap-1.5 cursor-pointer dilemo-btn shadow-md active:scale-95">
                    <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Guardar Cambios</span>
                </button>
            @endif
        </div>
    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-xl bg-[#09090b] border border-zinc-800 shadow-sm">
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-4">Apertura una noche para registrar el stock de las barras.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all dilemo-btn inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. SELECTOR DE BARRAS, FILTROS & BUSCADOR
             ======================================================== -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-2.5 rounded-xl notoria-card shadow-sm">
            
            <!-- Selector de Barras (iOS Segmented Control) -->
            <div class="inline-flex p-1.5 rounded-2xl notoria-subtile gap-1.5 w-full sm:w-auto">
                @foreach($availableBars as $bar)
                    @php
                        $isSelected = $selectedBar === $bar;
                        $barLabel = match($bar) {
                            'Barra Principal', 'Barra Kelly (Principal)' => 'Barra Principal',
                            'Barra Subterráneo', 'Barra Ariel (Subte)' => 'Barra Subterráneo',
                            default => $bar,
                        };
                    @endphp
                    <a href="{{ route('barInventory.index', ['session_id' => $session->id, 'bar' => $bar]) }}" 
                       class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl font-bold text-sm sm:text-base transition-all flex items-center justify-center cursor-pointer {{ $isSelected ? 'bg-[#F5B81C] text-black shadow-md font-black' : 'theme-text-secondary hover:theme-text-primary hover:bg-black/5 dark:hover:bg-white/5' }}">
                        <span>{{ $barLabel }}</span>
                    </a>
                @endforeach
            </div>

            <!-- Filtros de Categoría & Buscador -->
            <div class="flex flex-wrap items-center gap-2.5">
                <div class="inline-flex p-1 rounded-xl notoria-subtile text-xs">
                    <button type="button" 
                            id="filter-all-btn"
                            onclick="filterCategory('all', this)"
                            class="px-3 py-1.5 rounded-lg font-bold transition-all cursor-pointer bg-[#F5B81C] text-black shadow-sm">
                        Todos
                    </button>
                    <button type="button" 
                            id="filter-licores-btn"
                            onclick="filterCategory('licores', this)"
                            class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer theme-text-secondary hover:theme-text-primary">
                        Licores
                    </button>
                    <button type="button" 
                            id="filter-mixers-btn"
                            onclick="filterCategory('mixers', this)"
                            class="px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer theme-text-secondary hover:theme-text-primary">
                        Gaseosas & Mixers
                    </button>
                </div>

                <div class="relative w-40 sm:w-48">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none theme-text-muted">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input type="text" 
                           placeholder="Buscar bebida..." 
                           oninput="filterByName(this.value)"
                           class="w-full pl-8 pr-2.5 py-1.5 text-xs notoria-input focus:outline-none focus:border-[#F5B81C] transition-all font-sans">
                </div>
            </div>

        </div>

        <!-- ========================================================
             3. TABLA PRINCIPAL DE INVENTARIO (GRID TIPO EXCEL)
             ======================================================== -->
        <form method="POST" action="{{ route('barInventory.updateBulk') }}" id="bar-inventory-form" data-ajax="true">
            @csrf
            @method('PUT')
            <input type="hidden" name="night_session_id" value="{{ $session->id }}">
            <input type="hidden" name="bar_name" value="{{ $selectedBar }}">

            <div class="notoria-card overflow-hidden shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-left border-collapse text-xs excel-table" id="inventory-table">
                        <thead>
                            <tr class="notoria-card-header font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3.5 px-4 w-1/4">Bebida / Presentación</th>
                                <th class="py-3.5 px-3 text-center w-1/5 border-l border-zinc-700/50">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="font-bold text-white">1. Saldo de Ayer</span>
                                        <span class="text-[10px] text-zinc-300 font-normal normal-case">Cierre anterior en barra</span>
                                    </div>
                                </th>
                                <th class="py-3.5 px-3 text-center w-1/5 border-l border-zinc-700/50">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="font-bold text-white">2. Subido al Abrir</span>
                                        <span class="text-[10px] text-zinc-300 font-normal normal-case">Apertura inicial del turno</span>
                                    </div>
                                </th>
                                <th class="py-3.5 px-3 text-center w-1/5 border-l border-zinc-700/50">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="font-bold text-white">3. Reposición en Noche</span>
                                        <span class="text-[10px] text-zinc-300 font-normal normal-case">Durante la fiesta (rápido)</span>
                                    </div>
                                </th>
                                <th class="py-3.5 px-4 text-center w-1/6 border-l border-zinc-700/50">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="text-[#F5B81C] font-black">Total Disponible</span>
                                        <span class="text-[10px] text-zinc-300 font-normal normal-case">Suma total botellas</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y notoria-border font-sans" id="inventory-rows-tbody">
                            
                            @php
                                $allItems = $liquorSales->merge($mixerSales);
                            @endphp

                            @forelse($allItems as $sale)
                                @php
                                    $isMixer = $sale->product && $sale->product->category === 'Mixers';
                                    $itemCategory = $isMixer ? 'mixers' : 'licores';
                                    $unitsPerPkg = $sale->product->units_per_package > 0 ? (int)$sale->product->units_per_package : 1;
                                    
                                    $initPkg = (int)$sale->initial_packages;
                                    $initUnits = (int)$sale->initial_units;
                                    $addPkg = (int)$sale->added_packages;
                                    $addUnits = (int)$sale->added_units;
                                    $nightPkg = (int)$sale->night_packages;
                                    $nightUnits = (int)$sale->night_units;

                                    $apertPkg = $initPkg + $addPkg;
                                    $apertUnits = $initUnits + $addUnits;

                                    $totNightPkg = $apertPkg + $nightPkg;
                                    $totNightUnits = $apertUnits + $nightUnits;
                                    $totNightBot = ($totNightPkg * $unitsPerPkg) + $totNightUnits;
                                    $initBot = ($initPkg * $unitsPerPkg) + $initUnits;
                                    $addBot = ($addPkg * $unitsPerPkg) + $addUnits;
                                    $nightBot = ($nightPkg * $unitsPerPkg) + $nightUnits;
                                @endphp
                                <tr class="inv-row hover:bg-slate-500/10 transition-colors"
                                    data-row-id="{{ $sale->id }}"
                                    data-category="{{ $itemCategory }}"
                                    data-name="{{ strtolower($sale->product->name ?? '') }}"
                                    data-units-per-pkg="{{ $unitsPerPkg }}">
                                    
                                    <!-- 1. Bebida -->
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg notoria-subtile flex items-center justify-center theme-text-primary font-black text-xs shrink-0">
                                                {{ mb_substr($sale->product->name ?? 'B', 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-black theme-text-primary text-xs block truncate">{{ $sale->product->name }}</span>
                                                <span class="text-[11px] theme-text-secondary font-medium block mt-0.5">
                                                    {{ $sale->product->unit }} • {{ $unitsPerPkg }} unid. x caja
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 2. Saldo de Ayer (Cajas & Sueltas) -->
                                    <td class="py-2.5 px-3 text-center border-l notoria-border">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] theme-text-muted font-bold uppercase mb-1">Cajas</span>
                                                <input type="number" 
                                                       data-excel-col="0"
                                                       name="inventory[{{ $sale->id }}][initial_packages]" 
                                                       value="{{ $initPkg }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-init-pkg excel-input w-14 sm:w-16 h-9 text-center font-mono font-bold text-xs sm:text-sm rounded-xl focus:outline-none transition-all">
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] theme-text-muted font-bold uppercase mb-1">Sueltas</span>
                                                <input type="number" 
                                                       data-excel-col="1"
                                                       name="inventory[{{ $sale->id }}][initial_units]" 
                                                       value="{{ $initUnits }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-init-units excel-input w-14 sm:w-16 h-9 text-center font-mono font-bold text-xs sm:text-sm rounded-xl focus:outline-none transition-all">
                                            </div>
                                        </div>
                                        <div class="text-[10px] theme-text-muted mt-1 font-mono">
                                            = <strong class="cell-init-bot theme-text-primary font-bold">{{ $initBot }}</strong> bot.
                                        </div>
                                    </td>

                                    <!-- 3. Subido al Abrir (Cajas & Sueltas) -->
                                    <td class="py-2.5 px-3 text-center border-l notoria-border">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] theme-text-muted font-bold uppercase mb-1">Cajas</span>
                                                <input type="number" 
                                                       data-excel-col="2"
                                                       name="inventory[{{ $sale->id }}][added_packages]" 
                                                       value="{{ $addPkg }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-add-pkg excel-input w-14 sm:w-16 h-9 text-center font-mono font-bold text-xs sm:text-sm rounded-xl focus:outline-none transition-all">
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] theme-text-muted font-bold uppercase mb-1">Sueltas</span>
                                                <input type="number" 
                                                       data-excel-col="3"
                                                       name="inventory[{{ $sale->id }}][added_units]" 
                                                       value="{{ $addUnits }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-add-units excel-input w-14 sm:w-16 h-9 text-center font-mono font-bold text-xs sm:text-sm rounded-xl focus:outline-none transition-all">
                                            </div>
                                        </div>
                                        <div class="text-[10px] theme-text-muted mt-1 font-mono">
                                            + <strong class="cell-add-bot theme-text-primary font-bold">{{ $addBot }}</strong> bot.
                                        </div>
                                    </td>

                                    <!-- 4. Reposición en Noche (Entrada directa sin steppers) -->
                                    <td class="py-2.5 px-3 text-center border-l notoria-border">
                                        <div class="inline-flex items-center justify-center gap-2">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] theme-text-muted font-bold uppercase mb-1">Cajas</span>
                                                <input type="number" 
                                                       data-excel-col="4"
                                                       name="inventory[{{ $sale->id }}][night_packages]" 
                                                       value="{{ $nightPkg }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-night-pkg excel-input w-14 sm:w-16 h-9 text-center font-mono font-bold text-xs sm:text-sm rounded-xl focus:outline-none transition-all">
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] theme-text-muted font-bold uppercase mb-1">Sueltas</span>
                                                <input type="number" 
                                                       data-excel-col="5"
                                                       name="inventory[{{ $sale->id }}][night_units]" 
                                                       value="{{ $nightUnits }}" 
                                                       min="0" 
                                                       @disabled(!$session->isOpen())
                                                       class="input-night-units excel-input w-14 sm:w-16 h-9 text-center font-mono font-bold text-xs sm:text-sm rounded-xl focus:outline-none transition-all">
                                            </div>
                                        </div>
                                        <div class="text-[10px] theme-text-muted mt-1 font-mono">
                                            + <strong class="cell-night-bot theme-text-primary font-bold">{{ $nightBot }}</strong> bot.
                                        </div>
                                    </td>

                                    <!-- 5. Total Disponible para Ventas (Destacado y Claro) -->
                                    <td class="py-2.5 px-4 text-center border-l notoria-border">
                                        <div class="stat-pill inline-flex flex-col items-center justify-center px-4 py-2 rounded-xl min-w-[96px] shadow-sm notoria-subtile">
                                            <span class="cell-total-bot font-black font-mono text-xl text-[#F5B81C] tracking-tight">
                                                {{ $totNightBot }}
                                            </span>
                                            <span class="text-[10px] theme-text-muted font-mono mt-0.5">
                                                <span class="cell-total-pkg">{{ $totNightPkg }}</span> c • <span class="cell-total-units">{{ $totNightUnits }}</span> u
                                            </span>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-zinc-500 font-sans">
                                        No hay bebidas registradas en esta barra.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================================
                 4. PIE DE FORMULARIO ESTÁTICO (CARD NOTORIA)
                 ======================================================== -->
            <div class="mt-4 p-4 notoria-card shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div>
                    <span class="text-xs theme-text-secondary block font-bold">Total botellas disponibles en {{ $selectedBar }}:</span>
                    <span class="text-base font-black font-mono theme-text-primary" id="bar-total-sum">0 botellas</span>
                </div>

                @if($session->isOpen())
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#F5B81C] text-black font-black text-xs uppercase tracking-wider hover:bg-[#e5ac18] transition-all flex items-center justify-center gap-2 dilemo-btn cursor-pointer shadow-md active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Guardar Inventario de {{ $selectedBar }}</span>
                    </button>
                @else
                    <div class="px-4 py-2 rounded-xl notoria-subtile theme-text-muted text-xs font-bold flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 theme-text-muted" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span>Noche cerrada (solo lectura)</span>
                    </div>
                @endif
            </div>

        </form>

        <!-- ========================================================
             5. SCRIPT REACTIVO CON MOTOR DE NAVEGACIÓN TIPO EXCEL
             ======================================================== -->
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('.inv-row');
            const totalSumEl = document.getElementById('bar-total-sum');
            const inventoryForm = document.getElementById('bar-inventory-form');
            const autosaveStatus = document.getElementById('inventory-autosave-status');
            let autosaveTimer = null;
            let autosaveInFlight = false;
            let autosaveQueued = false;

            function setAutosaveStatus(message, className) {
                if (!autosaveStatus) return;
                autosaveStatus.textContent = message;
                autosaveStatus.className = `text-[10px] font-mono ${className}`;
            }

            async function autosaveInventory() {
                if (!inventoryForm) return;
                if (autosaveInFlight) {
                    autosaveQueued = true;
                    return;
                }

                autosaveInFlight = true;
                setAutosaveStatus('Guardando...', 'text-[#F5B81C]');
                try {
                    const response = await fetch(inventoryForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(inventoryForm)
                    });
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    setAutosaveStatus('Guardado', 'text-emerald-400');
                } catch (error) {
                    console.error('Error en autoguardado de inventario:', error);
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
                if (!inventoryForm) return;
                clearTimeout(autosaveTimer);
                setAutosaveStatus('Pendiente...', 'text-zinc-400');
                autosaveTimer = setTimeout(autosaveInventory, 450);
            }

            function updateTotalSum() {
                let grandTotal = 0;
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const totBotEl = row.querySelector('.cell-total-bot');
                        if (totBotEl) {
                            grandTotal += parseInt(totBotEl.textContent) || 0;
                        }
                    }
                });
                if (totalSumEl) {
                    totalSumEl.textContent = grandTotal + ' botellas';
                }
            }

            function recalculateRow(row) {
                const unitsPerPkg = parseInt(row.getAttribute('data-units-per-pkg')) || 1;
                
                // 1. Saldo de ayer
                const initPkg = parseInt(row.querySelector('.input-init-pkg')?.value) || 0;
                const initUnits = parseInt(row.querySelector('.input-init-units')?.value) || 0;
                
                // 2. Subido al abrir
                const addPkg = parseInt(row.querySelector('.input-add-pkg')?.value) || 0;
                const addUnits = parseInt(row.querySelector('.input-add-units')?.value) || 0;

                // 3. Durante la fiesta
                const nightPkg = parseInt(row.querySelector('.input-night-pkg')?.value) || 0;
                const nightUnits = parseInt(row.querySelector('.input-night-units')?.value) || 0;

                // Conteo de botellas
                const initBot = (initPkg * unitsPerPkg) + initUnits;
                const addBot = (addPkg * unitsPerPkg) + addUnits;
                const nightBot = (nightPkg * unitsPerPkg) + nightUnits;

                // Total Apertura
                const apertPkg = initPkg + addPkg;
                const apertUnits = initUnits + addUnits;

                // Total Noche
                const totalNightPkg = apertPkg + nightPkg;
                const totalNightUnits = apertUnits + nightUnits;
                const totalNightBot = (totalNightPkg * unitsPerPkg) + totalNightUnits;

                // Actualizar textos intermedios
                const initBotEl = row.querySelector('.cell-init-bot');
                if (initBotEl) initBotEl.textContent = initBot;

                const addBotEl = row.querySelector('.cell-add-bot');
                if (addBotEl) addBotEl.textContent = addBot;

                const nightBotEl = row.querySelector('.cell-night-bot');
                if (nightBotEl) nightBotEl.textContent = nightBot;

                // Actualizar total final
                const totBotEl = row.querySelector('.cell-total-bot');
                const totPkgEl = row.querySelector('.cell-total-pkg');
                const totUnitsEl = row.querySelector('.cell-total-units');

                if (totBotEl) totBotEl.textContent = totalNightBot;
                if (totPkgEl) totPkgEl.textContent = totalNightPkg;
                if (totUnitsEl) totUnitsEl.textContent = totalNightUnits;

                updateTotalSum();
            }

            rows.forEach(row => {
                row.querySelectorAll('input').forEach(input => {
                    input.addEventListener('input', () => recalculateRow(row));
                    input.addEventListener('change', queueAutosave);
                });
            });

            /* ========================================================
               MOTOR DE NAVEGACIÓN Y LLENADO RÁPIDO TIPO EXCEL
               - Selección automática al enfocar (sobrescribe directo)
               - Flecha Abajo / Enter: siguiente fila, misma columna
               - Flecha Arriba / Shift+Enter: fila anterior, misma columna
               - Flecha Derecha / Izquierda: columna adyacente
               ======================================================== */
            function initExcelNavigation(container) {
                if (!container) return;

                function getMatrix() {
                    const visibleRows = Array.from(container.querySelectorAll('.inv-row')).filter(tr => tr.style.display !== 'none');
                    return visibleRows.map(tr => {
                        const inputs = Array.from(tr.querySelectorAll('input[data-excel-col]')).filter(inp => !inp.disabled);
                        inputs.sort((a, b) => parseInt(a.dataset.excelCol) - parseInt(b.dataset.excelCol));
                        return { tr, inputs };
                    });
                }

                container.addEventListener('focusin', function (e) {
                    const input = e.target;
                    if (!input.matches('input[data-excel-col]')) return;

                    // Seleccionar todo el número al entrar para escribir directo
                    setTimeout(() => {
                        if (document.activeElement === input) {
                            try { input.select(); } catch (err) {}
                        }
                    }, 25);

                    // Iluminar fila activa
                    container.querySelectorAll('.inv-row').forEach(r => r.classList.remove('excel-active-row'));
                    const tr = input.closest('.inv-row');
                    if (tr) tr.classList.add('excel-active-row');
                });

                container.addEventListener('focusout', function (e) {
                    const input = e.target;
                    if (!input.matches('input[data-excel-col]')) return;
                    const tr = input.closest('.inv-row');
                    if (tr && !tr.contains(document.activeElement)) {
                        tr.classList.remove('excel-active-row');
                    }
                });

                container.addEventListener('keydown', function (e) {
                    const input = e.target;
                    if (!input.matches('input[data-excel-col]')) return;

                    const key = e.key;
                    if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Enter', 'Tab'].includes(key)) {
                        return;
                    }

                    const matrix = getMatrix();
                    const currentTr = input.closest('.inv-row');
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
                        e.preventDefault(); // Nunca enviar el formulario con Enter
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

            const tbody = document.getElementById('inventory-rows-tbody');
            if (tbody) initExcelNavigation(tbody);

            // Conteo inicial
            updateTotalSum();
        });

        /* --- FILTROS DE CATEGORÍA --- */
        function filterCategory(cat, btn) {
            document.querySelectorAll('#filter-all-btn, #filter-licores-btn, #filter-mixers-btn').forEach(b => {
                b.className = 'px-3 py-1.5 rounded-lg font-semibold transition-all cursor-pointer theme-text-secondary hover:theme-text-primary';
            });
            btn.className = 'px-3 py-1.5 rounded-lg font-black transition-all cursor-pointer bg-[#F5B81C] text-black shadow-sm';

            document.querySelectorAll('.inv-row').forEach(row => {
                if (cat === 'all' || row.dataset.category === cat) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Recalcular suma total tras filtrar
            let grandTotal = 0;
            document.querySelectorAll('.inv-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const totBotEl = row.querySelector('.cell-total-bot');
                    if (totBotEl) grandTotal += parseInt(totBotEl.textContent) || 0;
                }
            });
            const totalSumEl = document.getElementById('bar-total-sum');
            if (totalSumEl) totalSumEl.textContent = grandTotal + ' botellas';
        }

        /* --- FILTRO POR NOMBRE --- */
        function filterByName(q) {
            const query = q.toLowerCase().trim();
            document.querySelectorAll('.inv-row').forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = (!query || name.includes(query)) ? '' : 'none';
            });

            // Recalcular suma total tras buscar
            let grandTotal = 0;
            document.querySelectorAll('.inv-row').forEach(row => {
                if (row.style.display !== 'none') {
                    const totBotEl = row.querySelector('.cell-total-bot');
                    if (totBotEl) grandTotal += parseInt(totBotEl.textContent) || 0;
                }
            });
            const totalSumEl = document.getElementById('bar-total-sum');
            if (totalSumEl) totalSumEl.textContent = grandTotal + ' botellas';
        }
        </script>

    @endif

    </div> <!-- Fin .screen-only -->

</div> <!-- Fin #bar-inventory-app -->
@endsection
