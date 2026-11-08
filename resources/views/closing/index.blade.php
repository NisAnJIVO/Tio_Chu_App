@extends('layouts.app')

@section('title', 'Cierre de Caja')

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

    @media print {
        #main-sidebar, header, nav, .print-hide,
        button, form, select,
        #staff-drawer, #staff-drawer-backdrop {
            display: none !important;
        }

        body, main, .max-w-7xl {
            background: #ffffff !important;
            color: #000000 !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .print-only {
            display: block !important;
        }

        .print-card {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #000000 !important;
            box-shadow: none !important;
        }
    }
</style>

<div class="max-w-7xl mx-auto w-full space-y-4 pb-12 font-sans select-none">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: ESTADO DE NOCHE & SELECTOR
         ======================================================== -->
    <div class="notoria-card px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print-hide">
        
        <!-- Izquierda: Título y Noche Activa -->
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-[#F5B81C] shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                        <circle cx="12" cy="12" r="3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.5 9.5-5 5"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                        Cierre de Caja
                    </h1>
                    <p class="text-xs theme-text-secondary font-medium">
                        Arqueo y conciliación contable de jornada
                    </p>
                </div>
            </div>

            @if($session)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $session->isOpen() ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-500' : 'notoria-subtile text-zinc-400' }}">
                    <span class="w-2 h-2 rounded-full {{ $session->isOpen() ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-500' }}"></span>
                    <span class="text-xs font-black uppercase tracking-wider">
                        {{ $session->isOpen() ? 'Noche Abierta' : 'Noche Cerrada' }}
                    </span>
                </div>

                <div class="flex items-center gap-2 text-sm font-bold theme-text-secondary">
                    <span class="uppercase tracking-wide font-black theme-text-primary">{{ $session->day_name }}</span>
                    <span>•</span>
                    <span class="font-mono text-sm font-bold">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</span>
                </div>
            @endif

            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('closing.index') }}" class="flex items-center">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl notoria-subtile text-xs hover:border-[#F5B81C] transition-colors shadow-sm">
                        <svg class="w-4 h-4 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs sm:text-sm font-bold theme-text-primary focus:outline-none cursor-pointer pr-1">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'Abierta' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif
        </div>

        <!-- Derecha: Botones de Acción -->
        <div class="flex items-center gap-2.5 shrink-0">
            @if($session)
                <a href="{{ route('sessions.consolidatedSummary') }}" 
                   class="px-3.5 py-2.5 rounded-xl notoria-subtile hover:border-amber-500 font-bold text-xs sm:text-sm transition-all flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-[#F5B81C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="hidden md:inline">Consolidado 3 Días</span>
                </a>

                <button type="button" onclick="window.print()" 
                        class="px-3.5 py-2.5 rounded-xl notoria-subtile hover:border-zinc-400 font-bold text-xs sm:text-sm transition-all cursor-pointer active:scale-95 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Imprimir</span>
                </button>

                @if($session->isOpen())
                    <form method="POST" action="{{ route('closing.close', $session) }}" onsubmit="return confirm('¿Seguro que deseas dar por cerrada esta noche de atención contable?');">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2.5 rounded-xl bg-rose-600 text-white font-black text-xs sm:text-sm hover:bg-rose-700 transition-all cursor-pointer active:scale-95 flex items-center gap-2 shadow-md">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>Cerrar Noche</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('closing.reopen', $session) }}" onsubmit="return confirm('¿Deseas reabrir esta noche para editar inventario o ventas?');">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2.5 rounded-xl notoria-subtile hover:border-amber-500 font-bold text-xs sm:text-sm transition-all cursor-pointer active:scale-95 flex items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            <span>Reabrir Noche</span>
                        </button>
                    </form>
                @endif
            @endif
        </div>

    </div>

    @if($session)
        <!-- CABECERA DE IMPRESIÓN OFICIAL (SOLO EN IMPRESIÓN) -->
        <div class="hidden print-only text-center pb-4 mb-4 border-b-2 border-black font-sans">
            <h1 class="text-2xl font-black uppercase tracking-tight text-black">DISCOTECA TÍO CHU</h1>
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 mt-0.5">ACTA OFICIAL DE CIERRE DE CAJA</h2>
            <div class="flex items-center justify-center gap-4 text-xs text-slate-700 mt-1">
                <span>Noche: <strong class="uppercase text-black">{{ $session->day_name }}</strong></span>
                <span>•</span>
                <span>Fecha: <strong class="text-black">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</strong></span>
                <span>•</span>
                <span>Estado: <strong class="text-black">{{ $session->isOpen() ? 'ABIERTA / EN CURSO' : 'CERRADA CONTABLEMENTE' }}</strong></span>
            </div>
        </div>
    @endif

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-xl notoria-card shadow-sm flex flex-col items-center justify-center">
            <div class="w-14 h-14 rounded-xl notoria-subtile flex items-center justify-center theme-text-muted mb-4">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                </svg>
            </div>
            <h3 class="text-base font-black uppercase tracking-wider theme-text-primary">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-sm theme-text-secondary mt-1 mb-6">Apertura una noche para consultar y registrar el cierre de caja.</p>
            <a href="{{ route('sessions.create') }}" class="px-6 py-3 rounded-lg bg-[#F5B81C] text-black font-black text-sm hover:bg-[#e5ac18] transition-all shadow-sm">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        @php
            $paidStaffCount = $attendances->where('is_paid', true)->count();
            $totalStaffCount = $attendances->count();
            $pendingStaffAmount = max(0, $staffTotalDue - $staffAlreadyPaid);
            
            // Total ingresado en dinero bruto real
            $totalEntradasRecaudadas = $posGross + $qrTotal + $cashCollectedFromSales;
            // Total egresos reales ejecutados (Personal pagado en efectivo + Gastos operativos + Cerveza total)
            $totalSalidasEjecutadas = $staffAlreadyPaid + $expenses->sum('amount') + $beerPaymentTotal;
            // Balance neto de dinero líquido que queda disponible
            $balanceNetoLiquido = max(0, $totalEntradasRecaudadas - $totalSalidasEjecutadas);
        @endphp

        <!-- ========================================================
             2. CUADRÍCULA PRINCIPAL: ARQUEO DE CAJA Y CUADRE
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- ==========================================
                 COLUMNA IZQUIERDA: ARQUEO DE CAJA Y DINERO (7/12)
                 ========================================== -->
            <div class="lg:col-span-7 notoria-card overflow-hidden print-card">
                
                <!-- Encabezado Notorio de la Tarjeta -->
                <div class="notoria-card-header flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                        <h2 class="text-sm font-black uppercase tracking-wider text-white">
                            Arqueo Contable & Fondos Disponibles
                        </h2>
                    </div>
                    <span class="text-[11px] font-mono font-black px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                        {{ $session->isOpen() ? 'EN VIVO' : 'CERRADA' }}
                    </span>
                </div>

                <div class="p-5 sm:p-6 space-y-5">
                    <!-- Tarjeta Principal: Dinero Líquido Disponible del Turno -->
                    <div class="notoria-card-elevated border-2 border-amber-500/50 p-5 rounded-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-lg">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/15 border border-amber-500/30 text-xs font-black uppercase tracking-wider text-[#F5B81C]">
                                <span class="w-2 h-2 rounded-full bg-[#F5B81C]"></span>
                                Dinero Disponible del Turno (Neto)
                            </span>
                            <div class="text-3xl sm:text-4xl lg:text-5xl font-black font-mono tracking-tight mt-2 theme-text-primary">
                                <span class="text-2xl sm:text-3xl font-sans font-black mr-1 text-[#F5B81C]">Bs.</span>{{ number_format($balanceNetoLiquido, 2) }}
                            </div>
                            <p class="text-xs theme-text-secondary mt-1 font-medium">
                                Tarjetas Bruto ({{ number_format($posGross, 2) }}) + QR en Banco ({{ number_format($qrRemaining, 2) }}) + Efectivo en Mano ({{ number_format($effectiveCashInHand, 2) }})
                            </p>
                        </div>
                        
                        <div class="sm:text-right pt-2 sm:pt-0 border-t sm:border-t-0 notoria-border">
                            <span class="text-xs font-bold uppercase tracking-wider theme-text-muted block">
                                Movimiento del Turno
                            </span>
                            <div class="flex items-center sm:justify-end gap-2 mt-1.5">
                                <span class="text-xs sm:text-sm font-mono font-black text-emerald-500 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/30">
                                    +Bs. {{ number_format($totalEntradasRecaudadas, 2) }}
                                </span>
                                <span class="text-xs sm:text-sm font-mono font-black text-rose-500 bg-rose-500/10 px-2.5 py-1 rounded-lg border border-rose-500/30">
                                    -Bs. {{ number_format($totalSalidasEjecutadas, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Desglose de Entradas vs Salidas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- ENTRADAS (INGRESOS RECAUDADOS) -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b-2 notoria-border">
                                <span class="text-xs font-black theme-text-primary uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    Entradas Recaudadas
                                </span>
                                <span class="text-sm font-mono font-black text-emerald-500">
                                    Bs. {{ number_format($totalEntradasRecaudadas, 2) }}
                                </span>
                            </div>

                            <!-- 1. Tarjetas POS / Tarjetero (Bruto Sin Descuento) -->
                            <div class="notoria-subtile">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-black theme-text-primary block uppercase tracking-wide">Tarjetas (Bruto)</span>
                                        <span class="text-[11px] theme-text-secondary font-medium">Lector POS tarjetero</span>
                                    </div>
                                    <span class="font-mono font-black theme-text-primary text-base">
                                        Bs. {{ number_format($posGross, 2) }}
                                    </span>
                                </div>
                                <div class="mt-1.5 pt-1.5 border-t notoria-border text-[10px] theme-text-secondary flex items-center justify-between">
                                    <span>Sin descuento diario</span>
                                    <span>Comisión fds (1.3%): Bs. {{ number_format($posCommission, 2) }}</span>
                                </div>
                            </div>

                            <!-- 2. QR Total y Descuento de Gastos en QR -->
                            <div class="notoria-subtile space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-black theme-text-primary block uppercase tracking-wide">Cobros por QR</span>
                                        <span class="text-[11px] theme-text-secondary font-medium">Recaudación total</span>
                                    </div>
                                    <span class="font-mono font-black theme-text-primary text-base">
                                        Bs. {{ number_format($qrTotal, 2) }}
                                    </span>
                                </div>

                                <div class="space-y-1 text-[11px] font-mono notoria-card-elevated p-2 rounded-lg border notoria-border">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 font-bold theme-text-primary">
                                            <img src="{{ asset('images/LogosQR/yasta.png') }}" alt="Yasta" class="h-3.5 w-auto object-contain"> Yasta (Unión):
                                        </span>
                                        <span class="theme-text-primary font-bold">Bs. {{ number_format($totalQrYasta, 2) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center gap-1.5 font-bold theme-text-primary">
                                            <img src="{{ asset('images/LogosQR/yape.png') }}" alt="Yape" class="h-3.5 w-auto object-contain"> Yape (BCP):
                                        </span>
                                        <span class="theme-text-primary font-bold">Bs. {{ number_format($totalQrYape, 2) }}</span>
                                    </div>
                                    @if($beerPaymentQrTotal > 0)
                                        <div class="flex items-center justify-between text-rose-500 pt-1 border-t notoria-border font-bold">
                                            <span>(-) Gasto Cerveza por QR:</span>
                                            <span>-Bs. {{ number_format($beerPaymentQrTotal, 2) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Saldo Real en Banco tras gastos -->
                                <div class="flex items-center justify-between pt-1 border-t notoria-border text-xs font-bold">
                                    <span class="text-amber-500 font-black">= Saldo QR en Banco:</span>
                                    <span class="font-mono font-black text-[#F5B81C] text-sm">Bs. {{ number_format($qrRemaining, 2) }}</span>
                                </div>
                            </div>

                            <!-- 3. Efectivo en Mano -->
                            <div class="notoria-subtile">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-black theme-text-primary block uppercase tracking-wide">Efectivo en Caja</span>
                                        <span class="text-[11px] theme-text-secondary font-medium">Billetes físicos en mano</span>
                                    </div>
                                    <span class="font-mono font-black theme-text-primary text-base">
                                        Bs. {{ number_format($effectiveCashInHand, 2) }}
                                    </span>
                                </div>
                                <div class="mt-1.5 pt-1.5 border-t notoria-border text-[10px] theme-text-secondary flex items-center justify-between">
                                    <span>Cobrado ventas: Bs. {{ number_format($cashCollectedFromSales, 2) }}</span>
                                    <span class="{{ $effectiveCashInHand >= 0 ? 'theme-text-primary font-bold' : 'text-rose-500 font-bold' }}">
                                        {{ $reportedCash > 0 ? 'Conteo verificado' : 'Calculado de caja' }}
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- SALIDAS (EGRESOS Y PAGOS DEL TURNO) -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b-2 notoria-border">
                                <span class="text-xs font-black theme-text-primary uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                    Salidas / Egresos
                                </span>
                                <span class="text-sm font-mono font-black text-rose-500">
                                    -Bs. {{ number_format($totalSalidasEjecutadas, 2) }}
                                </span>
                            </div>

                            <!-- 1. Pago Personal (Efectivo) -->
                            <div class="notoria-subtile">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-black theme-text-primary block uppercase tracking-wide">Pago al Personal</span>
                                        <span class="text-[11px] theme-text-secondary font-medium">Pagado en efectivo en caja</span>
                                    </div>
                                    <span class="font-mono font-black text-rose-500 text-base">
                                        -Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                                    </span>
                                </div>
                                <div class="mt-1.5 pt-1.5 border-t notoria-border text-[10px] theme-text-secondary flex items-center justify-between">
                                    <span>{{ $paidStaffCount }} de {{ $totalStaffCount }} pagados</span>
                                    @if($pendingStaffAmount > 0)
                                        <span class="text-amber-500 font-bold">Por pagar: Bs. {{ number_format($pendingStaffAmount, 2) }}</span>
                                    @else
                                        <span class="text-emerald-500 font-bold">Planilla al día</span>
                                    @endif
                                </div>
                            </div>

                            <!-- 2. Pago de Cerveza (QR / Efectivo) -->
                            <div class="notoria-subtile">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-black theme-text-primary block uppercase tracking-wide">Pago de Cerveza</span>
                                        <span class="text-[11px] theme-text-secondary font-medium">Reposición de stock</span>
                                    </div>
                                    <span class="font-mono font-black text-rose-500 text-base">
                                        -Bs. {{ number_format($beerPaymentTotal, 2) }}
                                    </span>
                                </div>
                                <div class="mt-1.5 pt-1.5 border-t notoria-border text-[10px] theme-text-secondary flex items-center justify-between">
                                    <span>Vía QR: -Bs. {{ number_format($beerPaymentQrTotal, 2) }}</span>
                                    <span>En efectivo: -Bs. {{ number_format($beerPaymentCash, 2) }}</span>
                                </div>
                            </div>

                            <!-- 3. Gastos Operativos -->
                            <div class="notoria-subtile">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-black theme-text-primary block uppercase tracking-wide">Gastos de Turno</span>
                                        <span class="text-[11px] theme-text-secondary font-medium">Insumos, compras, varios</span>
                                    </div>
                                    <span class="font-mono font-black text-rose-500 text-base">
                                        -Bs. {{ number_format($expenses->sum('amount'), 2) }}
                                    </span>
                                </div>
                                <div class="mt-1.5 pt-1.5 border-t notoria-border text-[10px] theme-text-secondary flex items-center justify-between">
                                    <span>{{ $expenses->count() }} gastos registrados</span>
                                    <span>Pagados en efectivo</span>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- ========================================================
                         CUADRE DE CAJA CONTABLE (EXACTO AL EXCEL TÍO CHU)
                         ======================================================== -->
                    <div class="notoria-subtile border-2 {{ $cashDifference >= 0 ? 'border-amber-500/50 bg-amber-500/5' : 'border-rose-500/50 bg-rose-500/5' }} mt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider theme-text-primary flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $cashDifference >= 0 ? 'bg-[#F5B81C]' : 'bg-rose-500' }}"></span>
                                    Conciliación de Caja (Cuadre con Ventas de Barra)
                                </span>
                                <div class="mt-1 text-xs theme-text-secondary space-x-3">
                                    <span>Total Rendido: <strong class="theme-text-primary font-mono">Bs. {{ number_format($cashComparisonTotal, 2) }}</strong></span>
                                    <span>•</span>
                                    <span>Ventas en Barras: <strong class="theme-text-primary font-mono">Bs. {{ number_format($barTotal, 2) }}</strong></span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-[11px] font-bold uppercase tracking-wider block {{ $cashDifference >= 0 ? 'text-amber-500' : 'text-rose-500' }}">
                                    {{ $cashDifference >= 0 ? 'Sobrante Contable' : 'Faltante de Caja' }}
                                </span>
                                <div class="text-xl sm:text-2xl font-black font-mono {{ $cashDifference >= 0 ? 'text-[#F5B81C]' : 'text-rose-500' }}">
                                    {{ $cashDifference >= 0 ? '+Bs. ' . number_format($cashDifference, 2) : '-Bs. ' . number_format(abs($cashDifference), 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ==========================================
                 COLUMNA DERECHA: PERSONAL, CERVEZA Y GASTOS (5/12)
                 ========================================== -->
            <div class="lg:col-span-5 space-y-4">
                
                <!-- 1. TARJETA: PERSONAL DE LA NOCHE -->
                <div class="notoria-card overflow-hidden print-card">
                    <div class="notoria-card-header flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-xs font-black uppercase tracking-wider text-white">
                                Personal de Turno ({{ $totalStaffCount }})
                            </span>
                        </div>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-lg {{ $paidStaffCount === $totalStaffCount && $totalStaffCount > 0 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'notoria-subtile text-zinc-400' }}">
                            {{ $paidStaffCount }}/{{ $totalStaffCount }} pagados
                        </span>
                    </div>

                    <div class="p-5 space-y-3.5">
                        <!-- 3 Métricas Claras -->
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="p-2.5 rounded-xl notoria-subtile">
                                <span class="text-[10px] theme-text-secondary uppercase font-black block tracking-wider">Pagado</span>
                                <span class="font-mono font-black theme-text-primary block mt-0.5 text-sm sm:text-base">
                                    Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                                </span>
                            </div>
                            <div class="p-2.5 rounded-xl notoria-subtile">
                                <span class="text-[10px] theme-text-secondary uppercase font-black block tracking-wider">Planilla</span>
                                <span class="font-mono font-black theme-text-primary block mt-0.5 text-sm sm:text-base">
                                    Bs. {{ number_format($staffTotalDue, 2) }}
                                </span>
                            </div>
                            <div class="p-2.5 rounded-xl notoria-subtile">
                                <span class="text-[10px] theme-text-secondary uppercase font-black block tracking-wider">Por Pagar</span>
                                <span class="font-mono font-black block mt-0.5 text-sm sm:text-base {{ $pendingStaffAmount > 0 ? 'text-amber-500' : 'theme-text-muted' }}">
                                    Bs. {{ number_format($pendingStaffAmount, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Botón para ver lista de personal -->
                        <button type="button" onclick="openStaffDrawer()" 
                                class="w-full py-2.5 px-4 rounded-xl notoria-subtile hover:border-amber-500 theme-text-primary font-bold text-xs sm:text-sm flex items-center justify-between transition-all cursor-pointer shadow-sm">
                            <span class="font-bold">Ver y gestionar lista de trabajadores</span>
                            <span class="font-black text-[#F5B81C]">&rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- 2. TARJETA: PAGO DE CERVEZA (EGRESO CRÍTICO) -->
                <div class="notoria-card overflow-hidden print-card">
                    <div class="notoria-card-header flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="text-xs font-black uppercase tracking-wider text-white">
                                Pagos de Cerveza ({{ $beerPayments->count() }})
                            </span>
                        </div>
                        <span class="text-xs font-mono font-black text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-lg border border-rose-500/30">
                            Total: Bs. {{ number_format($beerPaymentTotal, 2) }}
                        </span>
                    </div>

                    <div class="p-5 space-y-3">
                        <!-- Lista de Pagos de Cerveza -->
                        @if($beerPayments->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($beerPayments as $bp)
                                    <div class="flex items-center justify-between p-2.5 rounded-xl notoria-subtile text-xs">
                                        <div>
                                            <span class="font-black theme-text-primary uppercase">
                                                {{ $bp->payment_method === 'qr' ? 'Pago QR (' . ($bp->qr_provider ?? 'Yape') . ')' : 'Pago en Efectivo' }}
                                            </span>
                                            @if($bp->notes)
                                                <span class="text-[11px] theme-text-secondary block">{{ $bp->notes }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-mono font-black text-rose-500 text-sm">
                                                -Bs. {{ number_format($bp->amount, 2) }}
                                            </span>
                                            @if($session->isOpen())
                                                <form method="POST" action="{{ route('beerPayments.destroy', $bp) }}" onsubmit="return confirm('¿Eliminar este pago de cerveza?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-zinc-400 hover:text-rose-500 p-1 cursor-pointer">
                                                        &times;
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs theme-text-muted text-center py-2">Sin pagos de cerveza registrados en esta noche.</p>
                        @endif
                    </div>
                </div>

                <!-- 3. TARJETA: GASTOS DE LA NOCHE -->
                <div class="notoria-card overflow-hidden print-card">
                    <div class="notoria-card-header flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-xs font-black uppercase tracking-wider text-white">
                                Gastos Operativos ({{ $expenses->count() }})
                            </span>
                        </div>
                        <span class="text-xs font-mono font-black text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-lg border border-rose-500/30">
                            Total: Bs. {{ number_format($expenses->sum('amount'), 2) }}
                        </span>
                    </div>

                    <div class="p-5 space-y-3.5">
                        <!-- Formulario de Registro de Gasto -->
                        <form method="POST" action="{{ route('closing.expenses.store') }}" 
                              class="space-y-2.5 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                            @csrf
                            <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
                                <!-- Categoría -->
                                <select name="category" class="text-xs rounded-xl px-2.5 py-2 notoria-input font-bold focus:outline-none">
                                    <option value="interno">Interno</option>
                                    <option value="externo">Externo</option>
                                </select>

                                <!-- Descripción -->
                                <input type="text" name="description" id="expense_description" required placeholder="Hielo, taxis, compras..."
                                       class="flex-1 min-w-[130px] text-xs sm:text-sm rounded-xl px-3 py-2 notoria-input placeholder-zinc-400">

                                <!-- Monto & Botón -->
                                <div class="flex items-center gap-2 shrink-0">
                                    <div class="relative w-28">
                                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-sans font-bold text-xs theme-text-muted">Bs.</span>
                                        <input type="number" step="any" min="0.01" name="amount" id="expense_amount" required placeholder="0.00"
                                               class="w-full text-xs sm:text-sm font-mono font-black rounded-xl pl-8 pr-2.5 py-2 notoria-input">
                                    </div>
                                    <button type="submit" title="Registrar gasto"
                                            class="px-3.5 py-2 bg-[#F5B81C] text-black font-black text-xs rounded-xl hover:bg-[#e5ac18] active:scale-95 transition-all shadow-sm shrink-0 cursor-pointer">
                                        + Gasto
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Tabla de Gastos -->
                        <div class="max-h-[160px] overflow-y-auto rounded-xl border notoria-border">
                            <table class="w-full text-xs text-left border-collapse">
                                <tbody class="divide-y notoria-border font-sans">
                                    @forelse($expenses as $exp)
                                        <tr id="expense-row-{{ $exp->id }}" class="hover:bg-slate-500/10 transition-colors">
                                            <td class="px-3 py-2 font-bold theme-text-primary text-xs truncate max-w-[150px]">{{ $exp->description }}</td>
                                            <td class="px-2 py-2 text-center">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-lg text-[10px] font-black uppercase {{ $exp->category === 'interno' ? 'notoria-subtile' : 'bg-amber-500/10 text-amber-500 border border-amber-500/30' }}">
                                                    {{ ucfirst($exp->category) }}
                                                </span>
                                            </td>
                                            <td class="px-2 py-2 text-right font-mono font-black text-rose-500 text-xs sm:text-sm">
                                                -Bs. {{ number_format($exp->amount, 2) }}
                                            </td>
                                            <td class="px-2 py-2 text-right w-8">
                                                <form method="POST" action="{{ route('closing.expenses.destroy', $exp) }}" 
                                                       class="delete-expense-form {{ !$session->isOpen() ? 'pointer-events-none opacity-40' : '' }}"
                                                       data-expense-id="{{ $exp->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            title="Eliminar gasto"
                                                            class="text-zinc-400 hover:text-rose-500 p-1 cursor-pointer">
                                                        &times;
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center theme-text-muted text-xs">Sin gastos operativos registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        @if($session)
            <!-- FIRMAS PARA REPORTE IMPRESO (SOLO EN IMPRESIÓN) -->
            <div class="hidden print-only pt-12 mt-8 border-t border-black text-center text-xs font-sans">
                <div class="grid grid-cols-2 gap-12">
                    <div>
                        <div class="border-t border-black w-48 mx-auto pt-2 font-bold uppercase text-black">Cajero / Administrador</div>
                        <span class="text-[10px] text-slate-600 block mt-0.5">Firma y Aclaración</span>
                    </div>
                    <div>
                        <div class="border-t border-black w-48 mx-auto pt-2 font-bold uppercase text-black">Don Ludo / Gerencia</div>
                        <span class="text-[10px] text-slate-600 block mt-0.5">Visto Bueno y Aprobación</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- ========================================================
             3. SLIDE-OVER DRAWER LATERAL: LISTA DE PERSONAL
             ======================================================== -->
        <div id="staff-drawer-backdrop" 
             onclick="closeStaffDrawer()" 
             class="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 hidden opacity-0 transition-opacity duration-200"></div>

        <aside id="staff-drawer" 
               class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] notoria-card rounded-none border-y-0 border-r-0 border-l notoria-border shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-200 ease-out">
            
            <!-- Cabecera del Drawer -->
            <div class="p-5 notoria-card-header rounded-none flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#F5B81C] text-black font-black flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-white">
                            Personal del Turno
                        </h3>
                        <p class="text-xs text-zinc-300">
                            {{ $session->day_name }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }} • {{ $attendances->count() }} personas
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeStaffDrawer()" 
                        class="w-8 h-8 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-white flex items-center justify-center cursor-pointer transition-colors text-lg">
                    &times;
                </button>
            </div>

            <!-- Buscador -->
            <div class="px-5 py-3 border-b notoria-border notoria-subtile rounded-none">
                <input type="text" id="staff-drawer-search" oninput="filterDrawerStaff(this.value)" placeholder="Buscar por nombre o cargo..." 
                       class="w-full text-xs sm:text-sm notoria-input px-3 py-2 focus:outline-none focus:border-[#F5B81C]">
            </div>

            <!-- Lista de Trabajadores -->
            <div class="flex-1 overflow-y-auto p-5 space-y-2.5" id="drawer-staff-list">
                @forelse($attendances as $att)
                    @php
                        $workerRole = str_ireplace(['mozo', 'mozos'], ['MESERO', 'MESEROS'], $att->staff->role ?? 'Staff');
                    @endphp
                    <div class="drawer-staff-item p-3.5 rounded-xl notoria-subtile flex items-center justify-between gap-3 hover:border-amber-500/60 transition-colors"
                         data-search="{{ strtolower(($att->staff->name ?? '') . ' ' . $workerRole) }}">
                        <div class="min-w-0">
                            <span class="text-xs sm:text-sm font-bold theme-text-primary block truncate uppercase">{{ $att->staff->name }}</span>
                            <span class="text-[11px] theme-text-secondary font-semibold block truncate">{{ strtoupper($workerRole) }}</span>
                        </div>

                        <div class="text-right shrink-0 flex flex-col items-end gap-1">
                            <span class="text-sm sm:text-base font-mono font-black theme-text-primary">
                                Bs. {{ number_format($att->pay_amount, 2) }}
                            </span>
                            @if($att->is_paid)
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-500/15 text-emerald-500 border border-emerald-500/30 text-[10px] font-bold">
                                    Pagado
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg bg-amber-500/15 text-amber-500 border border-amber-500/30 text-[10px] font-bold">
                                    Pendiente
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center theme-text-muted text-sm">
                        No hay personal asignado a este turno.
                    </div>
                @endforelse
            </div>

            <!-- Pie del Drawer -->
            <div class="p-4 border-t notoria-border notoria-subtile rounded-none flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider theme-text-secondary block">Total Pagado</span>
                    <span class="text-base font-black font-mono theme-text-primary">Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}</span>
                </div>
                <a href="{{ route('staffPayments.index', ['session_id' => $session->id]) }}" 
                   class="px-4 py-2.5 rounded-xl bg-[#F5B81C] text-black font-black text-xs sm:text-sm hover:bg-[#e5ac18] transition-all flex items-center gap-1.5 shadow-sm">
                    <span>Gestionar Pagos &rarr;</span>
                </a>
            </div>

        </aside>
    @endif

</div>

<!-- Scripts de Interacción -->
<script>
function openStaffDrawer() {
    const backdrop = document.getElementById('staff-drawer-backdrop');
    const drawer = document.getElementById('staff-drawer');
    if (!drawer || !backdrop) return;
    backdrop.classList.remove('hidden');
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        drawer.classList.remove('translate-x-full');
        drawer.classList.add('translate-x-0');
    });
    const search = document.getElementById('staff-drawer-search');
    if (search) {
        search.value = '';
        filterDrawerStaff('');
        setTimeout(() => search.focus(), 250);
    }
}

function closeStaffDrawer() {
    const backdrop = document.getElementById('staff-drawer-backdrop');
    const drawer = document.getElementById('staff-drawer');
    if (!drawer || !backdrop) return;
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    drawer.classList.remove('translate-x-0');
    drawer.classList.add('translate-x-full');
    setTimeout(() => {
        backdrop.classList.add('hidden');
    }, 220);
}

function filterDrawerStaff(query) {
    const q = (query || '').toLowerCase().trim();
    document.querySelectorAll('.drawer-staff-item').forEach(item => {
        const searchData = item.getAttribute('data-search') || '';
        if (searchData.includes(q)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeStaffDrawer();
    }
});
</script>
@endsection
