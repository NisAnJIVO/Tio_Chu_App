@extends('layouts.app')

@section('title', 'Resumen General')

@section('content')

<div class="space-y-4 max-w-7xl mx-auto w-full pb-12 font-sans select-none">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: ESTADO DE NOCHE & SELECTOR
         ======================================================== -->
    <div class="notoria-card px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        
        <!-- Izquierda: Estado de la Noche & Selector -->
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-[#F5B81C] shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                        Resumen General
                    </h1>
                    <p class="text-xs theme-text-secondary font-medium">
                        Panel de control ejecutivo
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
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl notoria-subtile text-xs hover:border-[#F5B81C] transition-colors shadow-sm">
                        <svg class="w-4 h-4 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" onchange="this.form.submit()" 
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

        <!-- Derecha: Botón Aperturar Noche -->
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('sessions.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-[#F5B81C] text-black font-black text-xs sm:text-sm hover:bg-[#e5ac18] transition-all flex items-center gap-1.5 shadow-md active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Aperturar Noche</span>
            </a>
        </div>

    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-xl bg-[#09090b] border border-zinc-800 shadow-sm flex flex-col items-center justify-center">
            <div class="w-14 h-14 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-500 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                </svg>
            </div>
            <h3 class="text-base font-black uppercase tracking-wider text-white">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-sm text-zinc-400 mt-1 mb-6">Apertura una nueva jornada para ver el resumen de caja, ventas y personal.</p>
            <a href="{{ route('sessions.create') }}" class="px-6 py-3 rounded-lg bg-[#F5B81C] text-black font-black text-sm hover:bg-[#e5ac18] transition-all shadow-sm">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. 4 TARJETAS KPI RESUMEN: GRANDES, CÓMODAS Y CLARAS (CARDS NOTORIAS)
             ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- TARJETA 1: Venta Total en Barras -->
            <div class="notoria-card p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-xs font-bold uppercase tracking-wider theme-text-secondary block truncate">
                        Venta en Barras
                    </span>
                    <div class="text-2xl sm:text-3xl font-black theme-text-primary font-mono tracking-tight mt-1 truncate">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($barTotal, 2) }}
                    </div>
                    <span class="text-[11px] theme-text-muted font-medium block mt-0.5 truncate">Principal y Subterráneo</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-[#F5B81C] shrink-0 ml-3 shadow-sm">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 22h8M12 15v7M19 3l-7 8-7-8h14z"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 2: Cobros por Tarjeta (POS Bruto) -->
            <div class="notoria-card p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-xs font-bold uppercase tracking-wider theme-text-secondary block truncate">
                        Tarjetas (Bruto)
                    </span>
                    <div class="text-2xl sm:text-3xl font-black theme-text-primary font-mono tracking-tight mt-1 truncate">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($posGross, 2) }}
                    </div>
                    <span class="text-[11px] theme-text-muted font-medium block mt-0.5 truncate">
                        Sin descuento diario (POS banco)
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-500 shrink-0 ml-3 shadow-sm">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 3: QR Disponible en Banco -->
            <div class="notoria-card p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-xs font-bold uppercase tracking-wider theme-text-secondary block truncate">
                        QR en Banco (Disponible)
                    </span>
                    <div class="text-2xl sm:text-3xl font-black theme-text-primary font-mono tracking-tight mt-1 truncate">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($qrRemaining, 2) }}
                    </div>
                    <span class="text-[11px] theme-text-muted font-medium block mt-0.5 truncate">
                        Recaudado: Bs. {{ number_format($qrTotal, 2) }}
                        @if($beerPaymentQrTotal > 0)
                            · Cerveza: -{{ number_format($beerPaymentQrTotal, 2) }}
                        @endif
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-500 shrink-0 ml-3 shadow-sm">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="6" height="6" x="3" y="3" rx="1"/>
                        <rect width="6" height="6" x="15" y="3" rx="1"/>
                        <rect width="6" height="6" x="3" y="15" rx="1"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 15h3m3 0h-3v3m0 3v-3m3 3v.01M10 7v3a2 2 0 01-2 2H5"/>
                    </svg>
                </div>
            </div>

            <!-- TARJETA 4: Efectivo Físico en Mano -->
            <div class="notoria-card p-5 flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-xs font-bold uppercase tracking-wider theme-text-secondary block truncate">
                        Efectivo en Mano (Caja)
                    </span>
                    <div class="text-2xl sm:text-3xl font-black font-mono tracking-tight mt-1 truncate theme-text-primary">
                        <span class="text-xs font-sans font-bold text-[#F5B81C] mr-0.5">Bs.</span>{{ number_format($effectiveCashInHand, 2) }}
                    </div>
                    <span class="text-[11px] font-medium block mt-0.5 truncate theme-text-muted">
                        {{ $reportedCash > 0 ? 'Conteo físico verificado' : 'Calculado en caja' }}
                    </span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-500/10 border notoria-border flex items-center justify-center theme-text-primary shrink-0 ml-3 shadow-sm">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="18" height="18" x="3" y="2" rx="2"/>
                        <circle cx="12" cy="12" r="3"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.5 9.5-5 5"/>
                    </svg>
                </div>
            </div>

        </div>

        <!-- ========================================================
             3. CUADRO OPERATIVO: DISTRIBUCIÓN POR BARRA Y CUENTAS
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            
            <!-- Columna Izquierda (5 Cols): Ventas por Barra -->
            <div class="notoria-card lg:col-span-5 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="notoria-card-header flex items-center justify-between">
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-sans">
                            Ventas por Barra
                        </h2>
                        <a href="{{ route('sales.index', ['session_id' => $session->id]) }}" 
                           class="text-xs font-bold text-[#F5B81C] hover:underline flex items-center gap-1">
                            <span>Ver Detalle &rarr;</span>
                        </a>
                    </div>

                    <div class="p-5 space-y-3">
                        <!-- Barra Principal -->
                        <div class="p-3.5 rounded-xl notoria-subtile">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-black text-xs theme-text-primary block">Barra Principal</span>
                                    <span class="text-[11px] theme-text-secondary font-medium">Piso Principal</span>
                                </div>
                                <span class="font-black theme-text-primary font-mono text-base">
                                    Bs. {{ number_format($salesPrincipal, 2) }}
                                </span>
                            </div>
                            <div class="mt-2 pt-2 border-t notoria-border text-[10px] theme-text-secondary font-mono flex items-center justify-between">
                                <span>QR: Bs. {{ number_format($barPayments['principal']['qr'], 2) }}</span>
                                <span>Tarjeta: Bs. {{ number_format($barPayments['principal']['card'], 2) }}</span>
                                <span>Efectivo: Bs. {{ number_format($barPayments['principal']['cash'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Barra Subterráneo -->
                        <div class="p-3.5 rounded-xl notoria-subtile">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-black text-xs theme-text-primary block">Barra Subterráneo</span>
                                    <span class="text-[11px] theme-text-secondary font-medium">Subterráneo</span>
                                </div>
                                <span class="font-black theme-text-primary font-mono text-base">
                                    Bs. {{ number_format($salesSubte, 2) }}
                                </span>
                            </div>
                            <div class="mt-2 pt-2 border-t notoria-border text-[10px] theme-text-secondary font-mono flex items-center justify-between">
                                <span>QR: Bs. {{ number_format($barPayments['subte']['qr'], 2) }}</span>
                                <span>Tarjeta: Bs. {{ number_format($barPayments['subte']['card'], 2) }}</span>
                                <span>Efectivo: Bs. {{ number_format($barPayments['subte']['cash'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Tienda -->
                        <div class="p-3.5 rounded-xl notoria-subtile">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-black text-xs theme-text-primary block">Tienda Oficial</span>
                                    <span class="text-[11px] theme-text-secondary font-medium">Unidades sueltas / entrada</span>
                                </div>
                                <span class="font-black theme-text-primary font-mono text-base">
                                    Bs. {{ number_format($salesTienda, 2) }}
                                </span>
                            </div>
                            <div class="mt-2 pt-2 border-t notoria-border text-[10px] theme-text-secondary font-mono flex items-center justify-between">
                                <span>QR: Bs. {{ number_format($barPayments['tienda']['qr'], 2) }}</span>
                                <span>Tarjeta: Bs. {{ number_format($barPayments['tienda']['card'], 2) }}</span>
                                <span>Efectivo: Bs. {{ number_format($barPayments['tienda']['cash'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Barras -->
                <div class="px-5 py-4 border-t notoria-border flex items-center justify-between">
                    <span class="theme-text-secondary font-bold uppercase tracking-wider text-xs">Total Ventas Barras</span>
                    <span class="font-black theme-text-primary font-mono text-lg">Bs. {{ number_format($barTotal, 2) }}</span>
                </div>
            </div>

            <!-- Columna Derecha (7 Cols): Cuentas del Turno (Ingresos vs Egresos) -->
            <div class="notoria-card lg:col-span-7 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="notoria-card-header flex items-center justify-between">
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-sans">
                            Cuentas del Turno
                        </h2>
                        <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" 
                           class="text-xs font-bold text-[#F5B81C] hover:underline flex items-center gap-1">
                            <span>Ir al Cierre &rarr;</span>
                        </a>
                    </div>

                    <!-- Dos Columnas: Entradas vs Salidas -->
                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Lado 1: Ingresos -->
                        <div class="p-4 rounded-xl notoria-subtile space-y-2">
                            <div class="flex items-center justify-between border-b notoria-border pb-2">
                                <span class="text-xs font-black theme-text-primary uppercase tracking-wider">Ingresos</span>
                                <span class="font-black text-emerald-500 font-mono text-xs">+ Bs. {{ number_format($totalIncome, 2) }}</span>
                            </div>

                            <div class="space-y-1.5 text-xs">
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">Ventas en Barras</span>
                                    <span class="font-mono font-bold theme-text-primary">Bs. {{ number_format($barTotal, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">Tarjetas (Bruto POS)</span>
                                    <span class="font-mono font-bold theme-text-primary">Bs. {{ number_format($posGross, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">QR Total Cobrado</span>
                                    <span class="font-mono font-bold theme-text-primary">Bs. {{ number_format($qrTotal, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">QR Yasta (Banco Unión)</span>
                                    <span class="font-mono font-bold theme-text-secondary">Bs. {{ number_format($totalQrYasta, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">QR Yape (Banco BCP)</span>
                                    <span class="font-mono font-bold theme-text-secondary">Bs. {{ number_format($totalQrYape, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5 border-t notoria-border pt-1 font-bold">
                                    <span class="theme-text-primary">Saldo QR en Banco</span>
                                    <span class="font-mono text-[#F5B81C]">Bs. {{ number_format($qrRemaining, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lado 2: Egresos y Pagos -->
                        <div class="p-4 rounded-xl notoria-subtile space-y-2">
                            <div class="flex items-center justify-between border-b notoria-border pb-2">
                                <span class="text-xs font-black theme-text-primary uppercase tracking-wider">Egresos</span>
                                <span class="font-black text-rose-500 font-mono text-xs">- Bs. {{ number_format($staffAlreadyPaid + $totalExpenses + $beerPaymentTotal, 2) }}</span>
                            </div>

                            <div class="space-y-1.5 text-xs">
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">Personal Pagado</span>
                                    <span class="font-mono font-bold text-rose-500">-Bs. {{ number_format($staffAlreadyPaid, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">Gastos Operativos</span>
                                    <span class="font-mono font-bold text-rose-500">-Bs. {{ number_format($totalExpenses, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">Pago Cerveza (QR)</span>
                                    <span class="font-mono font-bold text-rose-500">-Bs. {{ number_format($beerPaymentQrTotal, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="theme-text-secondary">Pago Cerveza (Efectivo)</span>
                                    <span class="font-mono font-bold text-rose-500">-Bs. {{ number_format($beerPaymentCash, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-0.5 border-t notoria-border pt-1 theme-text-muted">
                                    <span>Comisión Tarjeta Fin Sem.</span>
                                    <span class="font-mono">Bs. {{ number_format($posCommission, 2) }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Barra Inferior de Efectivo en Mano -->
                <div class="px-5 py-4 border-t notoria-border flex items-center justify-between">
                    <span class="font-bold theme-text-secondary uppercase tracking-wider text-xs">Efectivo Físico en Mano</span>
                    <span class="font-black font-mono text-lg theme-text-primary">
                        Bs. {{ number_format($effectiveCashInHand, 2) }}
                    </span>
                </div>
            </div>

        </div>

        <!-- ========================================================
             4. PAGO DE CERVEZA & ARQUEO DE CAJA
             ======================================================== -->
        <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_1fr] gap-5">
            
            <!-- TARJETA CERVEZA -->
            <div class="notoria-card overflow-hidden">
                <div class="notoria-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-sans">Pago de Cerveza</h2>
                        <p class="text-[11px] text-zinc-300 mt-0.5">Descuenta del efectivo o saldo QR según el método registrado</p>
                    </div>
                    @if($session->isOpen())
                        <button type="button" data-open-beer-modal class="px-3.5 py-1.5 rounded-xl bg-[#F5B81C] text-black font-black text-xs hover:bg-[#e5ac18] transition-all shadow-sm">
                            + Pagar Cerveza
                        </button>
                    @endif
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="p-3 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">Pago Efectivo</span>
                            <span class="mt-1 text-lg font-black theme-text-primary font-mono block">Bs. {{ number_format($beerPaymentCash, 2) }}</span>
                        </div>
                        <div class="p-3 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">Pago Yasta</span>
                            <span class="mt-1 text-lg font-black theme-text-primary font-mono block">Bs. {{ number_format($beerPaymentYasta, 2) }}</span>
                        </div>
                        <div class="p-3 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">Pago Yape</span>
                            <span class="mt-1 text-lg font-black theme-text-primary font-mono block">Bs. {{ number_format($beerPaymentYape, 2) }}</span>
                        </div>
                        <div class="p-3 rounded-xl notoria-subtile border-amber-500/40">
                            <span class="text-[10px] text-amber-500 uppercase tracking-wider font-bold block">QR Restante</span>
                            <span class="mt-1 text-lg font-black text-[#F5B81C] font-mono block">Bs. {{ number_format($qrRemaining, 2) }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-center pt-1">
                        <div class="p-2.5 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">Efectivo en caja</span>
                            <span class="mt-0.5 text-base font-black theme-text-primary font-mono block">Bs. {{ number_format($effectiveCashInHand, 2) }}</span>
                        </div>
                        <div class="p-2.5 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">Yasta disponible</span>
                            <span class="mt-0.5 text-base font-black theme-text-primary font-mono block">Bs. {{ number_format($yastaRemaining, 2) }}</span>
                        </div>
                        <div class="p-2.5 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">Yape disponible</span>
                            <span class="mt-0.5 text-base font-black theme-text-primary font-mono block">Bs. {{ number_format($yapeRemaining, 2) }}</span>
                        </div>
                    </div>

                    <!-- Historial de pagos de cerveza -->
                    @if($beerPayments->isNotEmpty())
                        <div class="space-y-2 pt-2">
                            <span class="text-[10px] font-black uppercase tracking-wider theme-text-secondary block">Historial de pagos de cerveza</span>
                            @foreach($beerPayments as $beerPayment)
                                <div class="flex items-center justify-between gap-3 rounded-xl notoria-subtile px-3 py-2 text-xs">
                                    <div>
                                        <span class="font-bold theme-text-primary">
                                            {{ $beerPayment->payment_method === 'qr' ? 'Pago QR (' . ($beerPayment->qr_provider ?? 'Yape') . ')' : 'Pago en Efectivo' }}
                                        </span>
                                        @if($beerPayment->notes)
                                            <span class="text-[11px] theme-text-muted block">{{ $beerPayment->notes }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono font-black text-rose-500">
                                            -Bs. {{ number_format($beerPayment->amount, 2) }}
                                        </span>
                                        @if($session->isOpen())
                                            <form method="POST" action="{{ route('beerPayments.destroy', $beerPayment) }}" onsubmit="return confirm('¿Eliminar este registro de pago de cerveza?');">
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
                        <div class="rounded-xl border border-dashed notoria-border p-3 text-xs theme-text-muted text-center">
                            Sin pagos de cerveza registrados en esta noche.
                        </div>
                    @endif
                </div>
            </div>

            <!-- TARJETA CONCILIACIÓN Y EFECTIVO EN MANO -->
            <div class="notoria-card overflow-hidden">
                <div class="notoria-card-header flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xs font-black text-white uppercase tracking-wider font-sans">Cuadre Final de Caja</h2>
                        <p class="text-[11px] text-zinc-300 mt-0.5">Conciliación idéntica a la hoja RESUMEN CIERRE</p>
                    </div>
                    @if($session->isOpen())
                        <button type="button" data-open-cash-modal class="px-3.5 py-1.5 rounded-xl bg-zinc-800 text-white hover:bg-zinc-700 font-bold text-xs shadow-sm">
                            Arqueo Físico
                        </button>
                    @endif
                </div>

                <div class="p-5 space-y-3">
                    <div class="p-3 rounded-xl notoria-subtile flex items-center justify-between">
                        <span class="text-xs theme-text-primary font-bold">Efectivo Físico en Mano:</span>
                        <span class="text-base font-black theme-text-primary font-mono">Bs. {{ number_format($effectiveCashInHand, 2) }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs font-mono">
                        <div class="p-3 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">Tarjetas POS</span>
                            <span class="mt-1 text-sm font-black theme-text-primary block">Bs. {{ number_format($posGross, 2) }}</span>
                        </div>
                        <div class="p-3 rounded-xl notoria-subtile">
                            <span class="text-[10px] theme-text-secondary uppercase tracking-wider font-bold block">QR Banco</span>
                            <span class="mt-1 text-sm font-black theme-text-primary block">Bs. {{ number_format($qrRemaining, 2) }}</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl notoria-subtile flex items-center justify-between text-xs">
                        <span class="theme-text-primary font-bold">Personal Pagado en Efectivo:</span>
                        <span class="font-mono font-black theme-text-primary">Bs. {{ number_format($staffAlreadyPaid, 2) }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl notoria-card-elevated border notoria-border flex items-center justify-between">
                        <span class="text-xs theme-text-primary uppercase tracking-wider font-black">Total Rendición:</span>
                        <span class="text-lg font-black text-[#F5B81C] font-mono">Bs. {{ number_format($cashComparisonTotal, 2) }}</span>
                    </div>

                    <!-- Caja Cuadrada / Sobrante / Faltante -->
                    <div class="p-4 rounded-xl notoria-subtile border-2 {{ $cashDifference >= 0 ? 'border-amber-500/50 bg-amber-500/5' : 'border-rose-500/50 bg-rose-500/5' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-black block {{ $cashDifference >= 0 ? 'text-amber-500' : 'text-rose-500' }}">
                                    {{ $cashDifference >= 0 ? 'Sobrante Contable' : 'Faltante de Caja' }}
                                </span>
                                <span class="text-[11px] theme-text-secondary mt-0.5 block">
                                    Ventas registradas: Bs. {{ number_format($barTotal, 2) }}
                                </span>
                            </div>
                            <div class="text-2xl font-black font-mono {{ $cashDifference >= 0 ? 'text-[#F5B81C]' : 'text-rose-500' }}">
                                {{ $cashDifference >= 0 ? '+Bs. ' . number_format($cashDifference, 2) : '-Bs. ' . number_format(abs($cashDifference), 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- MODAL PAGO DE CERVEZA -->
        <div id="beer-payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-xs px-4">
            <div class="w-full max-w-md rounded-2xl notoria-card p-6 shadow-2xl notoria-border">
                <div class="flex items-center justify-between pb-3 border-b notoria-border">
                    <h3 class="text-sm font-black uppercase tracking-wider theme-text-primary">Registrar Pago de Cerveza</h3>
                    <button type="button" data-close-beer-modal class="theme-text-muted hover:theme-text-primary text-xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>

                <form method="POST" action="{{ route('beerPayments.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div>
                        <label for="beer-payment-method" class="block text-xs font-bold uppercase tracking-wider theme-text-secondary mb-1.5">Método de pago</label>
                        <select id="beer-payment-method" name="payment_method" class="w-full rounded-xl notoria-input px-3.5 py-2.5 text-sm theme-text-primary font-bold focus:border-[#F5B81C] focus:outline-none cursor-pointer">
                            <option value="efectivo" class="bg-slate-900 text-white">Efectivo de caja</option>
                            <option value="qr" class="bg-slate-900 text-white">Pago por QR</option>
                        </select>
                    </div>

                    <div id="beer-payment-qr-provider" class="hidden">
                        <label for="beer-qr-provider" class="block text-xs font-bold uppercase tracking-wider theme-text-secondary mb-1.5">Aplicación Bancaria QR</label>
                        <select id="beer-qr-provider" name="qr_provider" class="w-full rounded-xl notoria-input px-3.5 py-2.5 text-sm theme-text-primary font-bold focus:border-[#F5B81C] focus:outline-none cursor-pointer">
                            <option value="YAPE" class="bg-slate-900 text-white">YAPE (Banco BCP)</option>
                            <option value="YASTA" class="bg-slate-900 text-white">YASTA (Banco Unión)</option>
                        </select>
                    </div>

                    <div>
                        <label for="beer-payment-amount" class="block text-xs font-bold uppercase tracking-wider theme-text-secondary mb-1.5">Importe (Bs.)</label>
                        <input id="beer-payment-amount" type="number" name="amount" min="0.01" step="0.01" required class="w-full rounded-xl notoria-input px-3.5 py-2.5 text-sm theme-text-primary font-mono font-bold focus:border-[#F5B81C] focus:outline-none" placeholder="0.00">
                    </div>

                    <div>
                        <label for="beer-payment-notes" class="block text-xs font-bold uppercase tracking-wider theme-text-secondary mb-1.5">Observación / Detalle</label>
                        <input id="beer-payment-notes" type="text" name="notes" maxlength="255" class="w-full rounded-xl notoria-input px-3.5 py-2.5 text-sm theme-text-primary focus:border-[#F5B81C] focus:outline-none placeholder-zinc-400" placeholder="Ej: reposición de cerveza Huari / Paceña">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t notoria-border">
                        <button type="button" data-close-beer-modal class="rounded-xl notoria-subtile px-4 py-2.5 text-xs sm:text-sm font-bold theme-text-secondary hover:theme-text-primary hover:border-zinc-400 transition-all cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="rounded-xl bg-[#F5B81C] text-black px-5 py-2.5 text-xs sm:text-sm font-black hover:bg-[#e5ac18] shadow-sm transition-all cursor-pointer active:scale-95">
                            Guardar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL ARQUEO FÍSICO DE EFECTIVO -->
        <div id="cash-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-xs px-4">
            <div class="w-full max-w-md rounded-2xl notoria-card p-6 shadow-2xl notoria-border">
                <div class="flex items-center justify-between pb-3 border-b notoria-border">
                    <h3 class="text-sm font-black uppercase tracking-wider theme-text-primary">Arqueo Físico de Efectivo</h3>
                    <button type="button" data-close-cash-modal class="theme-text-muted hover:theme-text-primary text-xl font-bold cursor-pointer transition-colors">&times;</button>
                </div>

                <form method="POST" action="{{ route('cash.reported.update') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                    <div>
                        <label for="reported-cash-amount" class="block text-xs font-bold uppercase tracking-wider theme-text-secondary mb-1.5">Monto de Billetes en Mano (Bs.)</label>
                        <input id="reported-cash-amount" type="number" name="reported_cash" min="0" step="0.01" value="{{ number_format($reportedCash, 2, '.', '') }}" required class="w-full rounded-xl notoria-input px-3.5 py-2.5 text-base theme-text-primary font-mono font-black focus:border-[#F5B81C] focus:outline-none" placeholder="0.00">
                        <p class="text-xs theme-text-muted mt-1.5">Conteo físico de billetes y monedas que entregó el cajero o encargado de barra.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t notoria-border">
                        <button type="button" data-close-cash-modal class="rounded-xl notoria-subtile px-4 py-2.5 text-xs sm:text-sm font-bold theme-text-secondary hover:theme-text-primary hover:border-zinc-400 transition-all cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="rounded-xl bg-[#F5B81C] text-black px-5 py-2.5 text-xs sm:text-sm font-black hover:bg-[#e5ac18] shadow-sm transition-all cursor-pointer active:scale-95">
                            Actualizar Arqueo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Script Modales -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const beerModal = document.getElementById('beer-payment-modal');
                const cashModal = document.getElementById('cash-modal');
                const openBeerButtons = document.querySelectorAll('[data-open-beer-modal]');
                const closeBeerButtons = document.querySelectorAll('[data-close-beer-modal]');
                const openCashButtons = document.querySelectorAll('[data-open-cash-modal]');
                const closeCashButtons = document.querySelectorAll('[data-close-cash-modal]');
                const methodSelect = document.getElementById('beer-payment-method');
                const qrProviderWrap = document.getElementById('beer-payment-qr-provider');

                function toggleBeerPaymentFields() {
                    const isQr = methodSelect.value === 'qr';
                    qrProviderWrap.classList.toggle('hidden', !isQr);
                }

                openBeerButtons.forEach((button) => {
                    button.addEventListener('click', function () {
                        beerModal.classList.remove('hidden');
                        beerModal.classList.add('flex');
                    });
                });

                closeBeerButtons.forEach((button) => {
                    button.addEventListener('click', function () {
                        beerModal.classList.add('hidden');
                        beerModal.classList.remove('flex');
                    });
                });

                openCashButtons.forEach((button) => {
                    button.addEventListener('click', function () {
                        cashModal.classList.remove('hidden');
                        cashModal.classList.add('flex');
                    });
                });

                closeCashButtons.forEach((button) => {
                    button.addEventListener('click', function () {
                        cashModal.classList.add('hidden');
                        cashModal.classList.remove('flex');
                    });
                });

                if (methodSelect) {
                    methodSelect.addEventListener('change', toggleBeerPaymentFields);
                    toggleBeerPaymentFields();
                }
            });
        </script>

    @endif

</div>
@endsection
