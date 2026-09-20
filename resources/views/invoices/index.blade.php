@extends('layouts.app')

@section('title', 'Facturas & POS Tarjetero')

@section('content')
<div class="space-y-6">

    <!-- Cabecera de la Vista -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Facturas Emitidas & Tarjetero</h2>
            <p class="text-xs text-zinc-600">Registro correlativo de facturas con discriminación de pago por Tarjeta (POS) y Efectivo.</p>
        </div>

        <!-- Selector de Noche -->
        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center gap-2">
                <label for="session_id" class="text-xs font-medium text-zinc-700">Noche:</label>
                <select name="session_id" id="session_id" onchange="this.form.submit()" 
                        class="text-xs border border-zinc-300 rounded bg-white px-2.5 py-1.5 text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    @foreach($allSessions as $s)
                        <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }}>
                            {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!$session)
        <div class="bg-white border border-zinc-200 rounded p-8 text-center">
            <h3 class="text-sm font-semibold text-zinc-800">No hay noche seleccionada</h3>
            <p class="text-xs text-zinc-600 mt-1 mb-4">Crea una noche para registrar facturas emitidas.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- Tarjetas de Resumen Rápido -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Total Tarjetas (Bruto)</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                    Bs. {{ number_format($totalTarjeta, 2) }}
                </div>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Comisión Bancaria ({{ number_format($session->pos_commission_rate * 100, 1) }}%)</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-red-600 font-mono">
                    - Bs. {{ number_format($totalTarjetaComision, 2) }}
                </div>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Total Tarjetas (Neto)</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                    Bs. {{ number_format($totalTarjetaNeto, 2) }}
                </div>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Total Facturas en Efectivo</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                    Bs. {{ number_format($totalEfectivo, 2) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Formulario de Entrada Rápida de Factura -->
            <div class="bg-white border border-zinc-200 rounded p-5 h-fit">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-4 pb-2 border-b border-zinc-200">
                    Registrar Factura
                </h3>
                
                <form method="POST" action="{{ route('invoices.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div>
                        <label for="correlative_num" class="block text-xs font-semibold text-zinc-700 mb-1">N° de Factura</label>
                        <input type="number" name="correlative_num" id="correlative_num" required value="{{ $nextCorrelative }}" min="1"
                               class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-semibold text-zinc-700 mb-1">Método de Pago</label>
                        <select name="payment_method" id="payment_method" required 
                                class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                            <option value="tarjeta">TARJETA (POS / Débito / Crédito)</option>
                            <option value="efectivo">EFECTIVO</option>
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-semibold text-zinc-700 mb-1">Monto de la Factura (Bs.)</label>
                        <input type="number" step="0.5" name="amount" id="amount" required placeholder="0.00"
                               class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 font-mono text-base font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-semibold text-zinc-700 mb-1">Notas / Detalle (Opcional)</label>
                        <input type="text" name="notes" id="notes" placeholder="Ej. Voucher #1234"
                               class="w-full text-xs border border-zinc-300 rounded px-3 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    </div>

                    <button type="submit" class="w-full py-2 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                        + Agregar Factura
                    </button>
                </form>
            </div>

            <!-- Tabla de Facturas Registradas en la Noche -->
            <div class="lg:col-span-2 bg-white border border-zinc-200 rounded overflow-hidden">
                <div class="px-4 py-3 border-b border-zinc-200 flex items-center justify-between">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-700">
                        Historial de Facturas Emitidas ({{ $invoices->count() }})
                    </h3>
                    <span class="text-xs font-mono font-bold text-zinc-900">
                        Total Facturado: Bs. {{ number_format($totalGeneral, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto max-h-[500px]">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider sticky top-0">
                            <tr>
                                <th class="px-3 py-2.5 w-12 text-center">N°</th>
                                <th class="px-3 py-2.5">Método</th>
                                <th class="px-3 py-2.5 text-right">Monto Bruto</th>
                                <th class="px-3 py-2.5 text-right">Comisión</th>
                                <th class="px-3 py-2.5 text-right">Neto Recibido</th>
                                <th class="px-3 py-2.5">Notas</th>
                                <th class="px-3 py-2.5 text-right w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @forelse($invoices as $inv)
                                <tr class="hover:bg-zinc-50/50">
                                    <td class="px-3 py-2 text-center font-mono font-semibold text-zinc-900">{{ $inv->correlative_num }}</td>
                                    <td class="px-3 py-2">
                                        @if($inv->payment_method === 'tarjeta')
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">TARJETA</span>
                                        @else
                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700 border border-zinc-300">EFECTIVO</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono font-medium text-zinc-900">
                                        Bs. {{ number_format($inv->amount, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono text-red-600">
                                        {{ $inv->commission_amount > 0 ? '- Bs. ' . number_format($inv->commission_amount, 2) : '—' }}
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono font-bold text-zinc-900">
                                        Bs. {{ number_format($inv->net_amount, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-zinc-600">{{ $inv->notes ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" onsubmit="return confirm('¿Eliminar factura?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Borrar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-zinc-600">
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
