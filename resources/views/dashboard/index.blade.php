@extends('layouts.app')

@section('title', 'Resumen General & Caja')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DEL DASHBOARD
         ========================================== -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b" style="border-color: #1e212d;">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#F5B81C]/10 text-[#F5B81C] border border-[#F5B81C]/20">
                    Mando Central
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400">Control de Turno en Tiempo Real</span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold tracking-tight text-white">
                Resumen General & Arqueo de Caja
            </h1>
            <p class="text-xs text-zinc-400 mt-1 max-w-2xl">
                Auditoría centralizada: ventas en barras, liquidación de tarjetero POS, transferencias QR, planilla y balance final de caja.
            </p>
        </div>

        @if($allSessions->isNotEmpty())
            <!-- Selector de Noche Destacado -->
            <div class="p-2.5 rounded-2xl border flex items-center gap-3 shadow-lg" style="background-color: #12141c; border-color: #202330;">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 border" style="background-color: rgba(245, 184, 28, 0.12); border-color: rgba(245, 184, 28, 0.25); color: #F5B81C;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="flex flex-col">
                        <label for="session_id" class="text-[10px] uppercase tracking-wider text-zinc-400 font-semibold">Cambiar Noche:</label>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none focus:ring-0 p-0 cursor-pointer pr-4">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-[#12141c] text-white">
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
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-2xl border shadow-xl" style="background-color: #12141c; border-color: #202330;">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center border" style="background-color: rgba(245, 184, 28, 0.12); border-color: rgba(245, 184, 28, 0.25); color: #F5B81C;">
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
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#F5B81C] text-black font-semibold text-xs shadow-lg shadow-[#F5B81C]/20 hover:bg-[#e5ac18] active:scale-95 transition-all">
                <span>+ Aperturar Primera Noche</span>
            </a>
        </div>
    @else

        <!-- ========================================================
             4 TARJETAS KPI (ALTA VISIBILIDAD & CONTRASTE)
             ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- KPI 1: Ventas en Barras -->
            <div class="rounded-2xl border p-5 overflow-hidden group transition-all duration-200 shadow-xl" 
                 style="background-color: #12141c; border-color: #202330;">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-wider text-zinc-400 font-semibold">1. Ventas en Barras</span>
                    <a href="{{ route('sales.index', ['session_id' => $session->id]) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#F5B81C]/10 text-[#F5B81C] border border-[#F5B81C]/25 hover:bg-[#F5B81C] hover:text-black transition-all flex items-center gap-1 cursor-pointer">
                        <span>Detalle</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>
                <div class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    <span class="text-[#F5B81C] text-lg mr-1 font-semibold">Bs.</span>{{ number_format($closing->total_bar_sales ?? 0, 2) }}
                </div>
                <div class="mt-4 text-xs text-zinc-400 border-t pt-3 flex items-center justify-between" style="border-color: #1c1f2b;">
                    <span>Liquidación física</span>
                    <span class="text-[#F5B81C] font-semibold">Principal & Subte</span>
                </div>
            </div>

            <!-- KPI 2: Tarjetas POS (Neto) -->
            <div class="rounded-2xl border p-5 overflow-hidden group transition-all duration-200 shadow-xl" 
                 style="background-color: #12141c; border-color: #202330;">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-wider text-zinc-400 font-semibold">2. Tarjetas POS (Neto)</span>
                    <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#F5B81C]/10 text-[#F5B81C] border border-[#F5B81C]/25 hover:bg-[#F5B81C] hover:text-black transition-all flex items-center gap-1 cursor-pointer">
                        <span>Vouchers</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>
                <div class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    <span class="text-[#F5B81C] text-lg mr-1 font-semibold">Bs.</span>{{ number_format($closing->total_pos_net ?? 0, 2) }}
                </div>
                <div class="mt-4 text-xs text-zinc-400 border-t pt-3 flex items-center justify-between" style="border-color: #1c1f2b;">
                    <span>Total Bruto POS</span>
                    <span class="text-zinc-300 font-medium">Bs. {{ number_format($closing->total_pos_gross ?? 0, 2) }}</span>
                </div>
            </div>

            <!-- KPI 3: Cobros QR -->
            <div class="rounded-2xl border p-5 overflow-hidden group transition-all duration-200 shadow-xl" 
                 style="background-color: #12141c; border-color: #202330;">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-wider text-zinc-400 font-semibold">3. Cobros QR Totales</span>
                    <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#F5B81C]/10 text-[#F5B81C] border border-[#F5B81C]/25 hover:bg-[#F5B81C] hover:text-black transition-all flex items-center gap-1 cursor-pointer">
                        <span>Ver QRs</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>
                <div class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                    <span class="text-[#F5B81C] text-lg mr-1 font-semibold">Bs.</span>{{ number_format($closing->total_qr ?? 0, 2) }}
                </div>
                <div class="mt-4 text-xs text-zinc-400 border-t pt-3 flex items-center justify-between" style="border-color: #1c1f2b;">
                    <span>Yasta (Unión) / Yape (BCP)</span>
                    <span class="text-emerald-400 font-semibold">100% Neto</span>
                </div>
            </div>

            <!-- KPI 4: Balance Neto en Caja -->
            @php
                $netCash = $closing->net_cash_balance ?? 0;
            @endphp
            <div class="rounded-2xl border p-5 overflow-hidden group transition-all duration-200 shadow-xl" 
                 style="background-color: #12141c; border-color: {{ $netCash >= 0 ? '#10b981' : '#f43f5e' }};">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase tracking-wider font-bold {{ $netCash >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        4. Balance Neto en Caja
                    </span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $netCash >= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 hover:bg-emerald-500 hover:text-black' : 'bg-rose-500/10 text-rose-400 border border-rose-500/25 hover:bg-rose-500 hover:text-black' }} transition-all flex items-center gap-1 cursor-pointer">
                        <span>Arqueo</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>
                <div class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight {{ $netCash >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    <span class="text-lg mr-1 font-semibold">Bs.</span>{{ number_format($netCash, 2) }}
                </div>
                <div class="mt-4 text-xs text-zinc-400 border-t pt-3 flex items-center justify-between" style="border-color: #1c1f2b;">
                    <span>Ingresos - Egresos</span>
                    <span class="text-xs font-bold {{ $netCash >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $netCash >= 0 ? 'Superávit' : 'Déficit' }}
                    </span>
                </div>
            </div>

        </div>

        <!-- ========================================================
             LIBRO MAYOR DE DOBLE ENTRADA (INGRESOS VS EGRESOS)
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Columna Izquierda: Ingresos Verificados -->
            <div class="rounded-2xl border p-6 space-y-5 shadow-xl" style="background-color: #12141c; border-color: #202330;">
                <div class="flex items-center justify-between border-b pb-3" style="border-color: #1e212d;">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Ingresos Verificados de la Noche</h3>
                    </div>
                    <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20">
                        + Bs. {{ number_format($closing->total_income ?? 0, 2) }}
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-3.5 rounded-xl border transition-colors" style="background-color: #0b0c12; border-color: #1c1f2b;">
                        <span class="text-zinc-300 font-medium">Ventas Directas en Barras</span>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_bar_sales ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl border transition-colors" style="background-color: #0b0c12; border-color: #1c1f2b;">
                        <div class="flex items-center gap-2">
                            <span class="text-zinc-300 font-medium">Cobros por Tarjetero POS (Neto)</span>
                            <span class="text-[10px] text-[#F5B81C] font-semibold bg-[#F5B81C]/10 px-1.5 py-0.5 rounded border border-[#F5B81C]/25">-3.5% com.</span>
                        </div>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_pos_net ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl border transition-colors" style="background-color: #0b0c12; border-color: #1c1f2b;">
                        <span class="text-zinc-300 font-medium">Transferencias QR (Yasta / Yape)</span>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_qr ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl border transition-colors" style="background-color: #0b0c12; border-color: #1c1f2b;">
                        <span class="text-zinc-300 font-medium">Tienda de Entrada / Ventas Mostrador</span>
                        <span class="font-bold text-white text-sm">Bs. {{ number_format($closing->total_store_sales ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Egresos & Planilla Operativa -->
            <div class="rounded-2xl border p-6 space-y-5 shadow-xl" style="background-color: #12141c; border-color: #202330;">
                <div class="flex items-center justify-between border-b pb-3" style="border-color: #1e212d;">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shadow-[0_0_8px_rgba(251,113,133,0.8)]"></span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Egresos & Salidas de Caja</h3>
                    </div>
                    <span class="text-xs font-bold text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-lg border border-rose-500/20">
                        - Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-3.5 rounded-xl border transition-colors" style="background-color: #0b0c12; border-color: #1c1f2b;">
                        <span class="text-zinc-300 font-medium">Planilla de Personal Pagada (Cuadrilla)</span>
                        <span class="font-bold text-rose-300 text-sm">Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl border transition-colors" style="background-color: #0b0c12; border-color: #1c1f2b;">
                        <span class="text-zinc-300 font-medium">Gastos Operativos & Compras Inmediatas</span>
                        <span class="font-bold text-rose-300 text-sm">Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl border flex items-center justify-between" style="background-color: rgba(245, 184, 28, 0.05); border-color: rgba(245, 184, 28, 0.2);">
                        <span class="text-xs text-zinc-300 font-medium">Retención Bancaria Estimada (POS):</span>
                        <span class="font-bold text-[#F5B81C] text-sm">
                            Bs. {{ number_format(($closing->total_pos_gross ?? 0) - ($closing->total_pos_net ?? 0), 2) }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================
             MATRIZ DE ACCESOS RÁPIDOS A LOS MÓDULOS OPERATIVOS
             ======================================================== -->
        <div class="space-y-4">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Acceso Directo a Módulos</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                <!-- 1. Tarjetero POS -->
                <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" 
                   class="rounded-2xl border p-5 shadow-xl transition-all duration-200 group flex flex-col justify-between hover:border-[#F5B81C]/50 hover:bg-[#161924]"
                   style="background-color: #12141c; border-color: #202330;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/15 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">Módulo 1</span>
                            <span class="text-xs text-zinc-500 group-hover:text-[#F5B81C] group-hover:translate-x-1 transition-all">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white group-hover:text-[#F5B81C] transition-colors">Tarjetero & POS</h4>
                        <p class="text-xs text-zinc-400 mt-1">Registro de vouchers de tarjeta con retención automática del 3.5% de comisión.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-zinc-400" style="border-color: #1c1f2b;">
                        <span>Vouchers Procesados:</span>
                        <span class="text-white font-semibold">{{ $closing->pos_invoices_count ?? 0 }} comprobantes</span>
                    </div>
                </a>

                <!-- 2. Catálogo & Bodega -->
                <a href="{{ route('products.index') }}" 
                   class="rounded-2xl border p-5 shadow-xl transition-all duration-200 group flex flex-col justify-between hover:border-[#F5B81C]/50 hover:bg-[#161924]"
                   style="background-color: #12141c; border-color: #202330;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/15 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">Módulo 2</span>
                            <span class="text-xs text-zinc-500 group-hover:text-[#F5B81C] group-hover:translate-x-1 transition-all">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white group-hover:text-[#F5B81C] transition-colors">Catálogo de Bebidas</h4>
                        <p class="text-xs text-zinc-400 mt-1">Lista oficial de licores, cervezas, precios de barra y combos con mixers.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-zinc-400" style="border-color: #1c1f2b;">
                        <span>Inventario Total:</span>
                        <span class="text-white font-semibold">Catálogo Maestro</span>
                    </div>
                </a>

                <!-- 3. Personal & Asistencia -->
                <a href="{{ route('staffPayments.index') }}" 
                   class="rounded-2xl border p-5 shadow-xl transition-all duration-200 group flex flex-col justify-between hover:border-[#F5B81C]/50 hover:bg-[#161924]"
                   style="background-color: #12141c; border-color: #202330;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/15 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">Módulo 3</span>
                            <span class="text-xs text-zinc-500 group-hover:text-[#F5B81C] group-hover:translate-x-1 transition-all">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white group-hover:text-[#F5B81C] transition-colors">Personal & Asistencia</h4>
                        <p class="text-xs text-zinc-400 mt-1">Planilla de mozos, seguridad, bartenders y DJ con botón de pago a 1-clic.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-zinc-400" style="border-color: #1c1f2b;">
                        <span>Personal Asignado:</span>
                        <span class="text-white font-semibold">Cuadrilla Activa</span>
                    </div>
                </a>

                <!-- 4. Cobros QR -->
                <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" 
                   class="rounded-2xl border p-5 shadow-xl transition-all duration-200 group flex flex-col justify-between hover:border-[#F5B81C]/50 hover:bg-[#161924]"
                   style="background-color: #12141c; border-color: #202330;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/15 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">Módulo 4</span>
                            <span class="text-xs text-zinc-500 group-hover:text-[#F5B81C] group-hover:translate-x-1 transition-all">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white group-hover:text-[#F5B81C] transition-colors">Transferencias QR</h4>
                        <p class="text-xs text-zinc-400 mt-1">Control discriminado por banco (Yasta Banco Unión vs Yape Banco BCP) y punto de venta.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-zinc-400" style="border-color: #1c1f2b;">
                        <span>Canales Activos:</span>
                        <span class="text-white font-semibold">Unión & BCP</span>
                    </div>
                </a>

                <!-- 5. Resumen Cierre de Caja -->
                <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" 
                   class="rounded-2xl border p-5 shadow-xl transition-all duration-200 group flex flex-col justify-between hover:border-[#F5B81C]/50 hover:bg-[#161924]"
                   style="background-color: #12141c; border-color: #202330;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/15 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">Módulo 5</span>
                            <span class="text-xs text-zinc-500 group-hover:text-[#F5B81C] group-hover:translate-x-1 transition-all">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white group-hover:text-[#F5B81C] transition-colors">Cierre de Caja & Gastos</h4>
                        <p class="text-xs text-zinc-400 mt-1">Arqueo definitivo de dinero físico, desglose bancario digital y gastos de la noche.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-zinc-400" style="border-color: #1c1f2b;">
                        <span>Estado:</span>
                        <span class="text-emerald-400 font-semibold">Balance al Día</span>
                    </div>
                </a>

                <!-- 6. Ventas por Barra -->
                <a href="{{ route('sales.index', ['session_id' => $session->id]) }}" 
                   class="rounded-2xl border p-5 shadow-xl transition-all duration-200 group flex flex-col justify-between hover:border-[#F5B81C]/50 hover:bg-[#161924]"
                   style="background-color: #12141c; border-color: #202330;">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-bold text-[#F5B81C] uppercase tracking-wider bg-[#F5B81C]/15 px-2 py-0.5 rounded-md border border-[#F5B81C]/30">Módulo 6</span>
                            <span class="text-xs text-zinc-500 group-hover:text-[#F5B81C] group-hover:translate-x-1 transition-all">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white group-hover:text-[#F5B81C] transition-colors">Ventas por Barra</h4>
                        <p class="text-xs text-zinc-400 mt-1">Despacho de licores en Barra Kelly, Barra Ariel y Tienda con deducción de gaseosas/mixers.</p>
                    </div>
                    <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-zinc-400" style="border-color: #1c1f2b;">
                        <span>Barras:</span>
                        <span class="text-white font-semibold">3 Puntos de Venta</span>
                    </div>
                </a>

            </div>
        </div>

    @endif

</div>
@endsection
