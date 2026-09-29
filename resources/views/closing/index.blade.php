@extends('layouts.app')

@section('title', 'Cierre de Caja & Gastos')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA (LIQUID GLASS)
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Módulo 5 — Resumen de Cierre
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Arqueo Nocturno & Liquidación</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Cierre de Caja & Control de Gastos
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Consolidación matemática de ingresos, planilla liquidada de trabajadores, egresos del turno y balance en mano.
            </p>
        </div>

        <!-- Selector de Noche y Botón de Estado -->
        <div class="flex flex-wrap items-center gap-3">
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('closing.index') }}" class="glass-panel p-2 rounded-2xl border border-white/10 flex items-center gap-2">
                    <label for="session_id" class="text-xs text-zinc-400 font-medium pl-1">Noche:</label>
                    <select name="session_id" id="session_id" onchange="this.form.submit()" 
                            class="glass-input text-xs font-mono font-bold rounded-xl px-2.5 py-1.5 text-zinc-100 cursor-pointer">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-[#12141c] text-zinc-100">
                                {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->status == 'open' ? 'Abierta' : 'Cerrada' }})
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($session)
                    @if($session->isOpen())
                        <form method="POST" action="{{ route('closing.close', $session) }}" onsubmit="return confirm('¿Seguro que deseas dar por cerrada esta noche de atención?');">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-rose-500 to-red-600 text-white text-xs font-bold rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-rose-500/20 cursor-pointer">
                                Cerrar Noche Definitiva
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('closing.reopen', $session) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 glass-card text-zinc-300 text-xs font-semibold rounded-xl hover:text-white transition-all cursor-pointer">
                                Reabrir Noche
                            </button>
                        </form>
                    @endif
                @endif
            @endif
        </div>
    </div>

    @if(!$session)
        <div class="glass-panel border border-white/10 rounded-3xl p-12 text-center shadow-2xl">
            <h3 class="text-base font-bold text-white">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-6">Crea una noche para ver el resumen de cierre de caja.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 transition-all shadow-md shadow-amber-500/20">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- ==========================================
             GRAN CUADRE DE CAJA CON EFECTO CRISTAL TRANSLÚCIDO
             ========================================== -->
        <div class="glass-panel-elevated rounded-3xl p-8 border border-white/10 shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6 border-b border-white/10">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-amber-400 font-bold">Ficha Contable Oficial</span>
                    <h3 class="text-xl font-black text-white tracking-tight mt-0.5">
                        Cuadre Consolidado — {{ $session->day_name }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                    </h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Verificación matemática en tiempo real según las 6 hojas de Excel integradas.</p>
                </div>
                <div class="text-left md:text-right bg-white/[0.03] border border-white/10 p-4 rounded-2xl">
                    <span class="text-xs font-mono text-zinc-400 block uppercase tracking-wider font-semibold">Saldo Neto Resultante en Caja:</span>
                    <span class="text-3xl font-black font-mono tracking-tight {{ ($closing->net_cash_closing ?? 0) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        <span class="text-base font-sans mr-1">Bs.</span>{{ number_format($closing->net_cash_closing ?? 0, 2) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6">
                <!-- Bloque Ingresos -->
                <div>
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-white/10">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.6)]"></span>
                        <h4 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-mono">1. Ingresos y Cobros Líquidos</h4>
                    </div>
                    <table class="w-full text-xs text-left font-mono">
                        <tbody class="divide-y divide-white/5">
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-2.5 text-zinc-300 font-sans">Tarjetas POS (Bruto)</td>
                                <td class="py-2.5 text-right text-zinc-200">Bs. {{ number_format($closing->total_card ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-2.5 text-zinc-400 font-sans pl-4">↳ Comisión Bancaria POS ({{ number_format(($session->pos_commission_rate ?? 0.035) * 100, 1) }}%)</td>
                                <td class="py-2.5 text-right text-rose-400 font-semibold">- Bs. {{ number_format($closing->total_card_commission ?? 0, 2) }}</td>
                            </tr>
                            <tr class="bg-amber-400/[0.04]">
                                <td class="py-2.5 px-3 text-amber-300 font-sans font-semibold">= Tarjetas Neto Recibido</td>
                                <td class="py-2.5 px-3 text-right text-amber-300 font-bold">Bs. {{ number_format($closing->total_card_net ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-2.5 text-zinc-300 font-sans">Cobros QR YASTA (Banco Unión)</td>
                                <td class="py-2.5 text-right text-zinc-200">Bs. {{ number_format($closing->total_qr_yasta ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-2.5 text-zinc-300 font-sans">Cobros QR YAPE (Banco BCP)</td>
                                <td class="py-2.5 text-right text-zinc-200">Bs. {{ number_format($closing->total_qr_yape ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-2.5 text-zinc-300 font-sans">Efectivo por Facturas</td>
                                <td class="py-2.5 text-right text-zinc-200">Bs. {{ number_format($closing->total_cash_invoices ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-2.5 text-zinc-300 font-sans">Tienda: Guardarropa</td>
                                <td class="py-2.5 text-right text-zinc-200">Bs. {{ number_format($closing->total_guardarropa ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-2.5 text-zinc-300 font-sans">Tienda: Snacks & Golosinas</td>
                                <td class="py-2.5 text-right text-zinc-200">Bs. {{ number_format($closing->total_snacks ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-t border-white/10 font-bold bg-white/[0.02]">
                                <td class="py-3 px-3 text-white font-sans">Total Ingresos Recaudados</td>
                                <td class="py-3 px-3 text-right text-emerald-400 font-black text-sm">
                                    Bs. {{ number_format(($closing->total_card_net ?? 0) + ($closing->total_qr_yasta ?? 0) + ($closing->total_qr_yape ?? 0) + ($closing->total_cash_invoices ?? 0) + ($closing->total_guardarropa ?? 0) + ($closing->total_snacks ?? 0), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-[11px] text-zinc-500 mt-2 italic font-mono">
                        * Referencia: El valor de bebidas vendidas en barras fue de Bs. {{ number_format($closing->total_bar_sales ?? 0, 2) }}.
                    </p>
                </div>

                <!-- Bloque Egresos -->
                <div>
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-white/10">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400 shadow-[0_0_8px_rgba(251,113,133,0.6)]"></span>
                        <h4 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-mono">2. Egresos y Pagos de Turno</h4>
                    </div>
                    <table class="w-full text-xs text-left font-mono">
                        <tbody class="divide-y divide-white/5">
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-3 text-zinc-300 font-sans">Pago a Personal (Staff, Bartenders, Seguridad)</td>
                                <td class="py-3 text-right text-rose-400 font-bold">Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-3 text-zinc-300 font-sans">Gastos Operativos (Hielo, insumos de urgencia, DJ)</td>
                                <td class="py-3 text-right text-rose-400 font-bold">Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-t border-white/10 font-bold bg-white/[0.02]">
                                <td class="py-3 px-3 text-white font-sans">Total Egresos de la Noche</td>
                                <td class="py-3 px-3 text-right text-rose-400 font-black text-sm">
                                    - Bs. {{ number_format(($closing->total_staff_paid ?? 0) + ($closing->total_expenses ?? 0), 2) }}
                                </td>
                            </tr>
                            <tr class="border-t-2 border-white/20 font-black text-sm bg-amber-400/[0.08]">
                                <td class="py-4 px-3 text-amber-300 font-sans tracking-wide">BALANCE NETO FINAL EN CAJA</td>
                                <td class="py-4 px-3 text-right text-amber-300 text-base">
                                    Bs. {{ number_format($closing->net_cash_closing ?? 0, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Resumen de Pagos al Personal de la Noche -->
            <div class="glass-panel rounded-2xl p-6 flex flex-col justify-between shadow-xl">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-4">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 font-mono">
                                Pagos al Personal — {{ $session->day_name }}
                            </h3>
                            <p class="text-[11px] text-zinc-400">{{ $attendances->count() }} trabajadores asignados para esta noche.</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-amber-400">
                            Total: Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between items-center py-2 px-3 glass-card rounded-xl text-xs font-mono">
                            <span class="text-zinc-300 font-sans">Total Pagado al Personal:</span>
                            <span class="font-bold text-emerald-400">
                                Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 px-3 glass-card rounded-xl text-xs font-mono">
                            <span class="text-zinc-300 font-sans">Total Planilla Estimada:</span>
                            <span class="font-semibold text-zinc-200">
                                Bs. {{ number_format($attendances->sum('pay_amount'), 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-[220px] rounded-xl border border-white/5 mb-4">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="px-3 py-2">Personal</th>
                                    <th class="px-3 py-2 text-right">Monto</th>
                                    <th class="px-3 py-2 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-mono">
                                @forelse($attendances as $att)
                                    <tr class="hover:bg-white/[0.02]">
                                        <td class="px-3 py-2 font-medium text-zinc-200 font-sans">{{ $att->staff->name }}</td>
                                        <td class="px-3 py-2 text-right">Bs. {{ number_format($att->pay_amount, 2) }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($att->is_paid)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">Pagado</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-800 text-zinc-400">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-center text-zinc-500">Sin personal cargado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-3 border-t border-white/10 flex justify-end">
                    <a href="{{ route('staffPayments.index', ['session_id' => $session->id]) }}" 
                       class="px-4 py-2 glass-card rounded-xl text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors">
                        Gestionar Pagos en Personal &rarr;
                    </a>
                </div>
            </div>

            <!-- Registro y Lista de Gastos Operativos -->
            <div class="glass-panel rounded-2xl p-6 shadow-xl flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 font-mono pb-3 border-b border-white/10 mb-4">
                        Gastos de la Noche (Compras e Imprevistos)
                    </h3>

                    <!-- Formulario de Inserción Rápida con Estilo Liquid Glass -->
                    <form method="POST" action="{{ route('closing.expenses.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-4 pb-4 border-b border-white/10">
                        @csrf
                        <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                        <div class="sm:col-span-2">
                            <input type="text" name="description" required placeholder="Descripción (Hielo, taxis, insumos...)"
                                   class="glass-input w-full text-xs rounded-xl px-3 py-2 text-zinc-100 placeholder-zinc-500">
                        </div>

                        <div>
                            <select name="category" class="glass-input w-full text-xs rounded-xl px-3 py-2 text-zinc-100 cursor-pointer">
                                <option value="interno" class="bg-[#12141c]">Interno</option>
                                <option value="externo" class="bg-[#12141c]">Externo</option>
                            </select>
                        </div>

                        <div>
                            <input type="number" step="0.5" name="amount" required placeholder="Bs. Monto"
                                   class="glass-input w-full text-xs rounded-xl px-3 py-2 text-zinc-100 font-mono font-bold">
                        </div>

                        <div class="sm:col-span-4 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-md shadow-amber-500/20 cursor-pointer">
                                + Agregar Gasto
                            </button>
                        </div>
                    </form>

                    <!-- Listado de Gastos Registrados -->
                    <div class="overflow-x-auto max-h-[260px] rounded-xl border border-white/5">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider sticky top-0">
                                <tr>
                                    <th class="px-3 py-2">Descripción</th>
                                    <th class="px-3 py-2">Tipo</th>
                                    <th class="px-3 py-2 text-right">Monto</th>
                                    <th class="px-3 py-2 text-right w-12">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-mono">
                                @forelse($expenses as $exp)
                                    <tr class="hover:bg-white/[0.02]">
                                        <td class="px-3 py-2.5 font-medium text-zinc-100 font-sans">{{ $exp->description }}</td>
                                        <td class="px-3 py-2.5">
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $exp->category === 'interno' ? 'bg-zinc-800 text-zinc-300' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30' }}">
                                                {{ ucfirst($exp->category) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-black text-rose-400">
                                            Bs. {{ number_format($exp->amount, 2) }}
                                        </td>
                                        <td class="px-3 py-2.5 text-right">
                                            <form method="POST" action="{{ route('closing.expenses.destroy', $exp) }}" onsubmit="return confirm('¿Eliminar gasto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold text-xs cursor-pointer">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-zinc-500 font-sans">No hay gastos registrados en esta noche.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    @endif

</div>
@endsection
