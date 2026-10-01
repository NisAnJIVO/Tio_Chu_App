@extends('layouts.app')

@section('title', 'Facturas & POS Tarjetero')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Módulo 1 — Tarjetero & POS
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Facturación Oficial y Liquidación Bancaria</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Facturas Emitidas & Tarjetero POS
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Conciliación automática de vouchers de tarjeta con retención de comisión bancaria y cobros en efectivo.
            </p>
        </div>

        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('invoices.index') }}" class="glass-panel p-2 rounded-2xl border border-white/10 flex items-center gap-2">
                <label for="session_id" class="text-xs text-zinc-400 font-medium pl-1">Noche:</label>
                <select name="session_id" id="session_id" onchange="this.form.submit()" 
                        class="glass-input text-xs font-mono font-bold rounded-xl px-2.5 py-1.5 text-zinc-100 cursor-pointer">
                    @foreach($allSessions as $s)
                        <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-[#12141c] text-zinc-100">
                            {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!$session)
        <div class="glass-panel border border-white/10 rounded-3xl p-12 text-center shadow-2xl">
            <h3 class="text-base font-bold text-white">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Crea una noche para registrar facturas emitidas.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 transition-all shadow-md shadow-amber-500/20">
                + Crear Noche
            </a>
        </div>
    @else

        @if(!$session->isOpen())
            <div class="glass-panel p-4 rounded-2xl border border-rose-500/40 bg-rose-500/10 shadow-xl flex items-center justify-between gap-4">
                <div class="flex items-center gap-3"><span class="text-xl">🔒</span><div><h4 class="text-xs font-black text-rose-300 uppercase tracking-wider font-mono">Noche Cerrada (Modo Solo Lectura)</h4><p class="text-xs text-zinc-300 mt-0.5">Esta jornada fue finalizada en Cierre de Caja. Las facturas no pueden modificarse.</p></div></div>
                <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="px-3.5 py-1.5 glass-card border border-rose-400/30 text-rose-300 rounded-xl text-xs font-bold font-mono shrink-0">Ver en Cierre de Caja &rarr;</a>
            </div>
        @endif

        <!-- ==========================================
             TARJETAS DE RESUMEN EJECUTIVO (GRANDES Y LEGIBLES)
             ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Total Tarjetas (Bruto)</span>
                <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalTarjeta, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">Total según vouchers POS</p>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">
                    Comisión POS ({{ number_format(($session->pos_commission_rate ?? 0.035) * 100, 1) }}%)
                </span>
                <div class="mt-2 text-3xl font-black font-mono text-rose-400 tracking-tight">
                    - <span class="text-rose-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalTarjetaComision, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">Deducción de procesador</p>
            </div>

            <div class="glass-panel-elevated rounded-2xl p-6 border border-emerald-500/30">
                <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-wider">Total Tarjetas (Neto)</span>
                <div class="mt-2 text-3xl font-black font-mono text-emerald-400 tracking-tight">
                    <span class="text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalTarjetaNeto, 2) }}
                </div>
                <p class="text-[11px] text-emerald-300/70 mt-1 font-mono">Dinero real que ingresa al banco</p>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Facturas en Efectivo</span>
                <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalEfectivo, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">Recibido físicamente en caja</p>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Formulario de Entrada Rápida de Factura (Liquid Glass Drawer/Card) -->
            <div class="glass-panel rounded-2xl p-6 h-fit shadow-2xl">
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 mb-4 pb-3 border-b border-white/10 font-mono">
                    Registrar Factura
                </h3>
                
                <form method="POST" action="{{ route('invoices.store') }}" class="space-y-4 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div>
                        <label for="correlative_num" class="block text-xs font-medium text-zinc-300 mb-1">N° de Factura</label>
                        <input type="number" name="correlative_num" id="correlative_num" required value="{{ $nextCorrelative }}" min="1"
                               class="glass-input w-full text-xs font-mono font-bold rounded-xl px-3.5 py-2.5">
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-medium text-zinc-300 mb-1">Método de Pago</label>
                        <select name="payment_method" id="payment_method" required 
                                class="glass-input w-full text-xs font-bold rounded-xl px-3.5 py-2.5 cursor-pointer">
                            <option value="tarjeta" class="bg-[#12141c]">TARJETA (POS / Débito / Crédito)</option>
                            <option value="efectivo" class="bg-[#12141c]">EFECTIVO</option>
                        </select>
                    </div>

                    <div>
                        <label for="bar_name" class="block text-xs font-medium text-zinc-300 mb-1">Barra / Punto de Venta</label>
                        <select name="bar_name" id="bar_name" required 
                                class="glass-input w-full text-xs font-bold rounded-xl px-3.5 py-2.5 cursor-pointer">
                            <option value="Principal" class="bg-[#12141c]">Principal (Barra Kelly)</option>
                            <option value="Subterraneo" class="bg-[#12141c]">Subterráneo (Barra Ariel)</option>
                            <option value="Tienda" class="bg-[#12141c]">Tienda</option>
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-medium text-zinc-300 mb-1">Monto de la Factura (Bs.)</label>
                        <input type="number" step="0.5" name="amount" id="amount" required placeholder="0.00"
                               class="glass-input w-full text-sm font-mono font-black rounded-xl px-3.5 py-2.5 text-amber-400">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-medium text-zinc-300 mb-1">Notas / Detalle (Opcional)</label>
                        <input type="text" name="notes" id="notes" placeholder="Ej. Voucher #1234"
                               class="glass-input w-full text-xs rounded-xl px-3.5 py-2 text-zinc-100 placeholder-zinc-500">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-md shadow-amber-500/20 cursor-pointer">
                        + Registrar Factura
                    </button>
                </form>
            </div>

            <!-- Tabla de Facturas Registradas en la Noche -->
            <div class="lg:col-span-2 glass-panel rounded-2xl overflow-hidden shadow-2xl">
                <div class="px-6 py-4 border-b border-white/10 bg-white/[0.02] flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 font-mono">
                        Historial de Facturas Emitidas ({{ $invoices->count() }})
                    </h3>
                    <span class="text-xs font-mono font-bold text-amber-400">
                        Total Facturado: Bs. {{ number_format($totalGeneral, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto max-h-[500px]">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider sticky top-0">
                            <tr>
                                <th class="px-3.5 py-3 w-10 text-center">N°</th>
                                <th class="px-3.5 py-3">Método</th>
                                <th class="px-3.5 py-3">Barra</th>
                                <th class="px-3.5 py-3 text-right">Monto Bruto</th>
                                <th class="px-3.5 py-3 text-right">Comisión</th>
                                <th class="px-3.5 py-3 text-right">Neto Recibido</th>
                                <th class="px-3.5 py-3">Notas</th>
                                <th class="px-3.5 py-3 text-right w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 font-mono">
                            @forelse($invoices as $inv)
                                <tr class="hover:bg-white/[0.02]">
                                    <td class="px-3.5 py-2.5 text-center font-bold text-zinc-100">{{ $inv->correlative_num }}</td>
                                    <td class="px-3.5 py-2.5">
                                        @if($inv->payment_method === 'tarjeta')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/25">TARJETA</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-800 text-zinc-300">EFECTIVO</span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-2.5">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/10 text-amber-300 border border-amber-400/25">
                                            {{ $inv->bar_name ?? 'Principal' }}
                                        </span>
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right font-bold text-white">
                                        Bs. {{ number_format($inv->amount, 2) }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right text-rose-400 font-semibold">
                                        {{ $inv->commission_amount > 0 ? '- Bs. ' . number_format($inv->commission_amount, 2) : '—' }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right font-extrabold text-amber-400">
                                        Bs. {{ number_format($inv->net_amount, 2) }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-zinc-400 font-sans">{{ $inv->notes ?? '—' }}</td>
                                    <td class="px-4 py-2.5 text-right">
                                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" onsubmit="return confirm('¿Eliminar factura?');" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold text-xs cursor-pointer">Borrar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-zinc-500 font-sans">
                                        No hay facturas registradas en esta noche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    @endif

</div>
@endsection
