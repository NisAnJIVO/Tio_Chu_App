@extends('layouts.app')

@section('title', 'Cierre de Caja & Gastos')

@section('content')
<div class="space-y-6">

    <!-- Cabecera de la Vista -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Cierre de Caja & Control de Gastos</h2>
            <p class="text-xs text-zinc-600">Consolidación de ingresos, pago a trabajadores, control de egresos y cuadre final de Don Ludo.</p>
        </div>

        <!-- Selector de Noche y Botón de Estado -->
        <div class="flex flex-wrap items-center gap-3">
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('closing.index') }}" class="flex items-center gap-2">
                    <label for="session_id" class="text-xs font-medium text-zinc-700">Noche:</label>
                    <select name="session_id" id="session_id" onchange="this.form.submit()" 
                            class="text-xs border border-zinc-300 rounded bg-white px-2.5 py-1.5 text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        @foreach($allSessions as $s)
                            <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }}>
                                {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->status == 'open' ? 'Abierta' : 'Cerrada' }})
                            </option>
                        @endforeach
                    </select>
                </form>

                @if($session)
                    @if($session->isOpen())
                        <form method="POST" action="{{ route('closing.close', $session) }}" onsubmit="return confirm('¿Seguro que deseas dar por cerrada esta noche de atención?');">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                                Cerrar Noche
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('closing.reopen', $session) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 border border-zinc-300 bg-white text-zinc-700 text-xs font-medium rounded hover:bg-zinc-50 transition-colors">
                                Reabrir Noche
                            </button>
                        </form>
                    @endif
                @endif
            @endif
        </div>
    </div>

    @if(!$session)
        <div class="bg-white border border-zinc-200 rounded p-8 text-center">
            <h3 class="text-sm font-semibold text-zinc-800">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-600 mt-1 mb-4">Crea una noche para ver el resumen de cierre de caja.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- Gran Resumen del Cuadre de Caja (Estilo Ficha Contable) -->
        <div class="bg-white border border-zinc-200 rounded p-6">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-200">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">
                        Cuadre Consolidado — {{ $session->day_name }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                    </h3>
                    <p class="text-xs text-zinc-600">Valores verificados matemáticamente de acuerdo a las fórmulas maestras.</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-zinc-600 block">Saldo Neto en Caja:</span>
                    <span class="text-2xl font-mono font-bold {{ ($closing->net_cash_closing ?? 0) >= 0 ? 'text-zinc-900' : 'text-red-600' }}">
                        Bs. {{ number_format($closing->net_cash_closing ?? 0, 2) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6">
                <!-- Bloque Ingresos -->
                <div>
                    <h4 class="text-xs font-bold text-zinc-700 uppercase tracking-wider mb-3">1. Ingresos y Cobros</h4>
                    <table class="w-full text-xs text-left">
                        <tbody class="divide-y divide-zinc-100">
                            <tr>
                                <td class="py-2 text-zinc-600">Tarjetas POS (Bruto)</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_card ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-zinc-600">Comisión Bancaria ({{ number_format($session->pos_commission_rate * 100, 1) }}%)</td>
                                <td class="py-2 text-right font-mono text-red-600">- Bs. {{ number_format($closing->total_card_commission ?? 0, 2) }}</td>
                            </tr>
                            <tr class="bg-zinc-50 font-medium">
                                <td class="py-2 px-2 text-zinc-800">Tarjetas Neto Recibido</td>
                                <td class="py-2 px-2 text-right font-mono text-zinc-900">Bs. {{ number_format($closing->total_card_net ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-zinc-600">Cobros QR YASTA (Banco Unión)</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_qr_yasta ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-zinc-600">Cobros QR YAPE (Banco BCP)</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_qr_yape ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-zinc-600">Efectivo por Facturas</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_cash_invoices ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-zinc-600">Tienda: Guardarropa</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_guardarropa ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-zinc-600">Tienda: Snacks & Golosinas</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_snacks ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-t-2 border-zinc-200 font-bold bg-zinc-50">
                                <td class="py-2 px-2 text-zinc-900">Total Ingresos Recaudados</td>
                                <td class="py-2 px-2 text-right font-mono text-zinc-900">
                                    Bs. {{ number_format(($closing->total_card_net ?? 0) + ($closing->total_qr_yasta ?? 0) + ($closing->total_qr_yape ?? 0) + ($closing->total_cash_invoices ?? 0) + ($closing->total_guardarropa ?? 0) + ($closing->total_snacks ?? 0), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-[11px] text-zinc-600 mt-2 italic">
                        * Nota: El valor de bebidas vendidas en barras fue de Bs. {{ number_format($closing->total_bar_sales ?? 0, 2) }}.
                    </p>
                </div>

                <!-- Bloque Egresos -->
                <div>
                    <h4 class="text-xs font-bold text-zinc-700 uppercase tracking-wider mb-3">2. Egresos y Pagos</h4>
                    <table class="w-full text-xs text-left">
                        <tbody class="divide-y divide-zinc-100">
                            <tr>
                                <td class="py-2 text-zinc-600">Pago a Personal (Staff / Bartenders)</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-zinc-600">Gastos Internos y Externos</td>
                                <td class="py-2 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-t-2 border-zinc-200 font-bold bg-zinc-50">
                                <td class="py-2 px-2 text-zinc-900">Total Egresos de la Noche</td>
                                <td class="py-2 px-2 text-right font-mono text-zinc-900">
                                    Bs. {{ number_format(($closing->total_staff_paid ?? 0) + ($closing->total_expenses ?? 0), 2) }}
                                </td>
                            </tr>
                            <tr class="border-t-2 border-zinc-900 font-bold text-sm bg-zinc-100">
                                <td class="py-3 px-2 text-zinc-900">BALANCE NETO FINAL</td>
                                <td class="py-3 px-2 text-right font-mono text-zinc-900">
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
            <div class="bg-white border border-zinc-200 rounded p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200 mb-4">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-700">
                                Pagos al Personal — {{ $session->day_name }}
                            </h3>
                            <p class="text-[11px] text-zinc-600">{{ $attendances->count() }} trabajadores programados para este turno.</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-zinc-900">
                            Total: Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between items-center py-2 px-3 bg-zinc-50 border border-zinc-200 rounded text-xs">
                            <span class="text-zinc-700">Total Liquidado (Pagado al Personal):</span>
                            <span class="font-mono font-bold text-zinc-900">
                                Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 px-3 bg-zinc-50 border border-zinc-200 rounded text-xs">
                            <span class="text-zinc-700">Total Planilla Estimada del Turno:</span>
                            <span class="font-mono font-semibold text-zinc-800">
                                Bs. {{ number_format($attendances->sum('pay_amount'), 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-[220px] border border-zinc-200 rounded mb-4">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                                <tr>
                                    <th class="px-3 py-2">Personal</th>
                                    <th class="px-3 py-2 text-right">Monto</th>
                                    <th class="px-3 py-2 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                @forelse($attendances as $att)
                                    <tr>
                                        <td class="px-3 py-1.5 font-medium text-zinc-900">{{ $att->staff->name }}</td>
                                        <td class="px-3 py-1.5 text-right font-mono">Bs. {{ number_format($att->pay_amount, 2) }}</td>
                                        <td class="px-3 py-1.5 text-center">
                                            @if($att->is_paid)
                                                <span class="text-[10px] font-bold text-emerald-700">Pagado</span>
                                            @else
                                                <span class="text-[10px] text-zinc-500">Pendiente</span>
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

                <div class="pt-3 border-t border-zinc-200 flex justify-end">
                    <a href="{{ route('staffPayments.index', ['session_id' => $session->id]) }}" 
                       class="px-3.5 py-2 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                        Gestionar Pagos en Módulo de Personal &rarr;
                    </a>
                </div>
            </div>

            <!-- Registro y Lista de Gastos -->
            <div class="bg-white border border-zinc-200 rounded p-5">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-700 pb-3 border-b border-zinc-200 mb-4">
                    Gastos de la Noche (Internos y Externos)
                </h3>

                <!-- Formulario Agregar Gasto -->
                <form method="POST" action="{{ route('closing.expenses.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-4 pb-4 border-b border-zinc-200">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div class="sm:col-span-2">
                        <input type="text" name="description" required placeholder="Descripción (Hielo, limón, taxi, etc.)"
                               class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    </div>

                    <div>
                        <select name="category" class="w-full text-xs border border-zinc-300 rounded px-2 py-1.5 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                            <option value="interno">Interno</option>
                            <option value="externo">Externo</option>
                        </select>
                    </div>

                    <div>
                        <input type="number" step="0.5" name="amount" required placeholder="Monto Bs."
                               class="w-full text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    </div>

                    <div class="sm:col-span-4 flex justify-end">
                        <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800">
                            + Agregar Gasto
                        </button>
                    </div>
                </form>

                <!-- Listado de Gastos -->
                <div class="overflow-x-auto max-h-[260px]">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider sticky top-0">
                            <tr>
                                <th class="px-3 py-2">Descripción</th>
                                <th class="px-3 py-2">Tipo</th>
                                <th class="px-3 py-2 text-right">Monto</th>
                                <th class="px-3 py-2 text-right w-12">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @forelse($expenses as $exp)
                                <tr class="hover:bg-zinc-50/50">
                                    <td class="px-3 py-2 font-medium text-zinc-900">{{ $exp->description }}</td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium {{ $exp->category === 'interno' ? 'bg-zinc-100 text-zinc-800 border border-zinc-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                            {{ ucfirst($exp->category) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono font-bold text-zinc-900">
                                        Bs. {{ number_format($exp->amount, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" action="{{ route('closing.expenses.destroy', $exp) }}" onsubmit="return confirm('¿Eliminar gasto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Borrar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-zinc-600">No hay gastos registrados en esta noche.</td>
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
