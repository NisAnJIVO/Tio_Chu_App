@extends('layouts.app')

@section('title', 'Mando Ejecutivo & Caja')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto relative z-10">

    <!-- ==========================================
         CABECERA DE MANDO & CONTEXTO DE OPERACIÓN
         ========================================== -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Consolidado de 6 Hojas de Excel
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Control de Turno en Tiempo Real</span>
            </div>
            <h2 class="text-2xl lg:text-3xl font-black tracking-tight text-white">
                Mando Ejecutivo & Arqueo de Caja
            </h2>
            <p class="text-xs text-zinc-400 mt-1 max-w-2xl">
                Auditoría centralizada: Ventas en barras, liquidación de tarjetero POS neto, transferencias QR, planilla y balance final de caja.
            </p>
        </div>

        @if($allSessions->isNotEmpty())
            <div class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-2.5 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-400/10 flex items-center justify-center text-amber-400 shrink-0 border border-amber-400/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="flex flex-col">
                        <label for="session_id" class="text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold">Cambiar Noche:</label>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-mono font-bold text-white focus:outline-none focus:ring-0 p-0 cursor-pointer pr-4">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-[#040507] text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($session_date ?? $s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'Abierta' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        @endif
    </div>

    @if(!$session)
        <!-- Estado Vacío Translúcido -->
        <div class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-400/10 border border-amber-400/30 flex items-center justify-center text-amber-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-white">No hay noches de evento registradas</h3>
            <p class="text-xs text-zinc-400 max-w-md mx-auto mt-1 mb-6">
                Para liquidar ventas de barra, registrar los cobros por QR, el tarjetero POS y el personal, apertura una nueva noche de atención.
            </p>
            <a href="{{ route('sessions.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 font-bold text-xs shadow-lg shadow-amber-500/25 hover:brightness-110 active:scale-95 transition-all uppercase tracking-wider">
                <span>+ Aperturar Primera Noche</span>
            </a>
        </div>
    @else

        <!-- ========================================================
             4 TARJETAS KPI DE LIQUID GLASS (NÚMEROS GRANDES HIPERLEGIBLES)
             ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- KPI 1: Ventas en Barras -->
            <div class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-6 overflow-hidden group hover:border-amber-500/40 hover:bg-white/[0.04] transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono uppercase tracking-wider text-zinc-400 font-bold">1. Ventas en Barras</span>
                    <a href="{{ route('sales.index', ['session_id' => $session->id]) }}" 
                       class="text-[11px] font-mono text-amber-400 hover:text-amber-300 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform font-bold">
                        Detalle &rarr;
                    </a>
                </div>
                <div class="mt-3 text-3xl lg:text-4xl font-black font-mono tracking-tight text-white">
                    <span class="text-amber-400 text-lg mr-1 font-sans">Bs.</span>{{ number_format($closing->total_bar_sales ?? 0, 2) }}
                </div>
                <div class="mt-3 text-xs text-zinc-400 font-mono border-t border-white/10 pt-2.5 flex items-center justify-between">
                    <span>Liquidación física</span>
                    <span class="text-amber-400/90 font-bold">Principal & Subte</span>
                </div>
            </div>

            <!-- KPI 2: Tarjetas POS (Neto) -->
            <div class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-6 overflow-hidden group hover:border-amber-500/40 hover:bg-white/[0.04] transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono uppercase tracking-wider text-zinc-400 font-bold">2. Tarjetas POS (Neto)</span>
                    <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" 
                       class="text-[11px] font-mono text-amber-400 hover:text-amber-300 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform font-bold">
                        Vouchers &rarr;
                    </a>
                </div>
                <div class="mt-3 text-3xl lg:text-4xl font-black font-mono tracking-tight text-white">
                    <span class="text-amber-400 text-lg mr-1 font-sans">Bs.</span>{{ number_format($closing->total_pos_net ?? 0, 2) }}
                </div>
                <div class="mt-3 text-xs text-zinc-400 font-mono border-t border-white/10 pt-2.5 flex items-center justify-between">
                    <span>Total Bruto POS</span>
                    <span class="text-zinc-300 font-mono">Bs. {{ number_format($closing->total_pos_gross ?? 0, 2) }}</span>
                </div>
            </div>

            <!-- KPI 3: Cobros QR -->
            <div class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-6 overflow-hidden group hover:border-amber-500/40 hover:bg-white/[0.04] transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono uppercase tracking-wider text-zinc-400 font-bold">3. Cobros QR Totales</span>
                    <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" 
                       class="text-[11px] font-mono text-amber-400 hover:text-amber-300 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform font-bold">
                        Ver QRs &rarr;
                    </a>
                </div>
                <div class="mt-3 text-3xl lg:text-4xl font-black font-mono tracking-tight text-white">
                    <span class="text-amber-400 text-lg mr-1 font-sans">Bs.</span>{{ number_format($closing->total_qr ?? 0, 2) }}
                </div>
                <div class="mt-3 text-xs text-zinc-400 font-mono border-t border-white/10 pt-2.5 flex items-center justify-between">
                    <span>Yasta (Unión) / Yape (BCP)</span>
                    <span class="text-emerald-400 font-bold">100% Neto</span>
                </div>
            </div>

            <!-- KPI 4: Balance Neto en Caja -->
            @php
                $netCash = $closing->net_cash_balance ?? 0;
            @endphp
            <div class="backdrop-blur-2xl bg-white/[0.02] border {{ $netCash >= 0 ? 'border-emerald-500/30' : 'border-rose-500/30' }} shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-6 overflow-hidden group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono uppercase tracking-wider font-black {{ $netCash >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        4. Balance Neto en Caja
                    </span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" 
                       class="text-[11px] font-mono text-amber-400 hover:text-amber-300 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform font-bold">
                        Arqueo &rarr;
                    </a>
                </div>
                <div class="mt-3 text-3xl lg:text-4xl font-black font-mono tracking-tight {{ $netCash >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    <span class="text-lg mr-1 font-sans">Bs.</span>{{ number_format($netCash, 2) }}
                </div>
                <div class="mt-3 text-xs text-zinc-400 font-mono border-t border-white/10 pt-2.5 flex items-center justify-between">
                    <span>Ingresos - Egresos</span>
                    <span class="text-xs font-bold {{ $netCash >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $netCash >= 0 ? 'Superávit' : 'Déficit' }}
                    </span>
                </div>
            </div>

        </div>

        <!-- ========================================================
             LIBRO MAYOR DE DOBLE ENTRADA EN LIQUID GLASS
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Columna Izquierda: Ingresos Verificados -->
            <div class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider">Ingresos Verificados de la Noche</h3>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                        + Bs. {{ number_format($closing->total_income ?? 0, 2) }}
                    </span>
                </div>

                <div class="space-y-3 font-mono text-xs">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400">Ventas Directas en Barras</span>
                        </div>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_bar_sales ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400">Cobros por Tarjetero POS (Neto)</span>
                            <span class="text-[10px] text-amber-400/90 font-bold bg-amber-400/10 px-1.5 py-0.5 rounded border border-amber-400/20">-3.5% com.</span>
                        </div>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_pos_net ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400">Transferencias QR (Yasta / Yape)</span>
                        </div>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_qr ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400">Tienda de Entrada / Ventas Mostrador</span>
                        </div>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_store_sales ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Egresos & Planilla Operativa -->
            <div class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shadow-[0_0_8px_rgba(251,113,133,0.8)]"></span>
                        <h3 class="text-sm font-black text-white uppercase tracking-wider">Egresos & Salidas de Caja</h3>
                    </div>
                    <span class="text-xs font-mono font-bold text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-lg border border-rose-500/20">
                        - Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}
                    </span>
                </div>

                <div class="space-y-3 font-mono text-xs">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400">Planilla de Personal Pagada (Cuadrilla)</span>
                        </div>
                        <span class="font-bold text-rose-300 text-sm">Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-400">Gastos Operativos & Compras Inmediatas</span>
                        </div>
                        <span class="font-bold text-rose-300 text-sm">Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl bg-amber-500/[0.05] border border-amber-500/20 text-zinc-300 flex items-center justify-between">
                        <span class="text-xs font-sans">Retención Bancaria Estimada (POS):</span>
                        <span class="font-bold text-amber-400 text-sm font-mono">
                            Bs. {{ number_format(($closing->total_pos_gross ?? 0) - ($closing->total_pos_net ?? 0), 2) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================
             MATRIZ DE ACCESOS RÁPIDOS A LOS 6 MÓDULOS DE EXCEL
             ======================================================== -->
        <div>
            <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-zinc-400 mb-4">
                Accesos a Módulos Operativos (Reemplazo Integral de las 6 Hojas de Excel)
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                
                <!-- 1. Facturas y Tarjetero POS -->
                <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" 
                   class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-5 hover:border-amber-400/40 hover:bg-white/[0.05] transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">Módulo 1</span>
                            <span class="text-xs text-zinc-500 group-hover:text-amber-400 transition-colors">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-black text-white group-hover:text-amber-300 transition-colors">Facturas & POS</h4>
                        <p class="text-xs text-zinc-400 mt-1">Registro de vouchers de tarjetas, clientes con NIT y cálculo de la retención del 3.5%.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-mono text-zinc-400">
                        <span>Total Vouchers:</span>
                        <span class="text-white font-bold">{{ $closing->pos_count ?? 0 }} comprobantes</span>
                    </div>
                </a>

                <!-- 2. Catálogo e Inventario -->
                <a href="{{ route('products.index') }}" 
                   class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-5 hover:border-amber-400/40 hover:bg-white/[0.05] transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">Módulo 2</span>
                            <span class="text-xs text-zinc-500 group-hover:text-amber-400 transition-colors">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-black text-white group-hover:text-amber-300 transition-colors">Inventario Bebidas</h4>
                        <p class="text-xs text-zinc-400 mt-1">Catálogo maestro de 29 bebidas con ajuste táctil (+ / -) y control visual de botellas en almacén.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-mono text-zinc-400">
                        <span>Licores Registrados:</span>
                        <span class="text-white font-bold">29 variedades</span>
                    </div>
                </a>

                <!-- 3. Personal y Cuadrilla -->
                <a href="{{ route('staff.index', ['session_id' => $session->id]) }}" 
                   class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-5 hover:border-amber-400/40 hover:bg-white/[0.05] transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">Módulo 3</span>
                            <span class="text-xs text-zinc-500 group-hover:text-amber-400 transition-colors">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-black text-white group-hover:text-amber-300 transition-colors">Personal & Asistencia</h4>
                        <p class="text-xs text-zinc-400 mt-1">Planilla de mozos, seguridad, bartenders y DJ con botón de pago a 1-clic.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-mono text-zinc-400">
                        <span>Personal Asignado:</span>
                        <span class="text-white font-bold">Cuadrilla Activa</span>
                    </div>
                </a>

                <!-- 4. Cobros QR -->
                <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" 
                   class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-5 hover:border-amber-400/40 hover:bg-white/[0.05] transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">Módulo 4</span>
                            <span class="text-xs text-zinc-500 group-hover:text-amber-400 transition-colors">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-black text-white group-hover:text-amber-300 transition-colors">Transferencias QR</h4>
                        <p class="text-xs text-zinc-400 mt-1">Control discriminado por banco (Yasta Banco Unión vs Yape Banco BCP) y punto de venta.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-mono text-zinc-400">
                        <span>Canales Activos:</span>
                        <span class="text-white font-bold">Unión & BCP</span>
                    </div>
                </a>

                <!-- 5. Resumen Cierre de Caja -->
                <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" 
                   class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-5 hover:border-amber-400/40 hover:bg-white/[0.05] transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">Módulo 5</span>
                            <span class="text-xs text-zinc-500 group-hover:text-amber-400 transition-colors">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-black text-white group-hover:text-amber-300 transition-colors">Cierre de Caja & Gastos</h4>
                        <p class="text-xs text-zinc-400 mt-1">Arqueo definitivo de dinero físico, desglose bancario digital y gastos de la noche.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-mono text-zinc-400">
                        <span>Estado:</span>
                        <span class="text-emerald-400 font-bold">Balance al Día</span>
                    </div>
                </a>

                <!-- 6. Ventas por Barra -->
                <a href="{{ route('sales.index', ['session_id' => $session->id]) }}" 
                   class="backdrop-blur-2xl bg-white/[0.02] border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] rounded-2xl relative z-10 p-5 hover:border-amber-400/40 hover:bg-white/[0.05] transition-all group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20">Módulo 6</span>
                            <span class="text-xs text-zinc-500 group-hover:text-amber-400 transition-colors">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-black text-white group-hover:text-amber-300 transition-colors">Ventas por Barra</h4>
                        <p class="text-xs text-zinc-400 mt-1">Despacho de licores en Barra Kelly, Barra Ariel y Tienda con deducción de gaseosas/mixers.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs font-mono text-zinc-400">
                        <span>Barras:</span>
                        <span class="text-white font-bold">3 Puntos de Venta</span>
                    </div>
                </a>

            </div>
        </div>

    @endif

</div>
@endsection
