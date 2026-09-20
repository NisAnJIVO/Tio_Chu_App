@extends('layouts.app')

@section('title', 'Resumen General')

@section('content')
<div class="space-y-6">

    <!-- Encabezado de la Vista -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Resumen de Operación</h2>
            <p class="text-xs text-zinc-600">Consolidado general de ventas, cobros y gastos por noche de atención.</p>
        </div>

        <!-- Selector de Noche -->
        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <label for="session_id" class="text-xs font-medium text-zinc-700">Cambiar noche:</label>
                <select name="session_id" id="session_id" onchange="this.form.submit()" 
                        class="text-xs border border-zinc-300 rounded bg-white px-2.5 py-1.5 text-zinc-800 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                    @foreach($allSessions as $s)
                        <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }}>
                            {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->status == 'open' ? 'Abierta' : 'Cerrada' }})
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if(!$session)
        <div class="bg-white border border-zinc-200 rounded p-8 text-center">
            <h3 class="text-sm font-semibold text-zinc-800">No hay noches de evento registradas</h3>
            <p class="text-xs text-zinc-600 mt-1 mb-4">Crea una nueva noche de atención para comenzar a registrar operaciones.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800">
                + Crear Primera Noche
            </a>
        </div>
    @else

        <!-- Cuadrícula de Métricas Principales (Minimalista) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Ventas en Barras -->
            <div class="bg-white border border-zinc-200 rounded p-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-600">Ventas en Barras</span>
                    <a href="{{ route('sales.index', ['session_id' => $session->id]) }}" class="text-[11px] text-zinc-700 hover:underline">Ver detalle &rarr;</a>
                </div>
                <div class="mt-2 text-2xl font-bold tracking-tight text-zinc-900">
                    Bs. {{ number_format($closing->total_bar_sales ?? 0, 2) }}
                </div>
                <p class="mt-1 text-[11px] text-zinc-600">Inventario liquidado en barras</p>
            </div>

            <!-- 2. Tarjetas / POS -->
            <div class="bg-white border border-zinc-200 rounded p-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-600">Tarjetero / POS (Neto)</span>
                    <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" class="text-[11px] text-zinc-700 hover:underline">Ver facturas &rarr;</a>
                </div>
                <div class="mt-2 text-2xl font-bold tracking-tight text-zinc-900">
                    Bs. {{ number_format($closing->total_card_net ?? 0, 2) }}
                </div>
                <p class="mt-1 text-[11px] text-zinc-600">
                    Bruto: Bs. {{ number_format($closing->total_card ?? 0, 2) }} (Com.: {{ number_format($session->pos_commission_rate * 100, 1) }}%)
                </p>
            </div>

            <!-- 3. Cobros QR -->
            <div class="bg-white border border-zinc-200 rounded p-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-600">Cobros por QR</span>
                    <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" class="text-[11px] text-zinc-700 hover:underline">Ver QRs &rarr;</a>
                </div>
                <div class="mt-2 text-2xl font-bold tracking-tight text-zinc-900">
                    Bs. {{ number_format(($closing->total_qr_yasta ?? 0) + ($closing->total_qr_yape ?? 0), 2) }}
                </div>
                <p class="mt-1 text-[11px] text-zinc-600">
                    Yasta: Bs. {{ number_format($closing->total_qr_yasta ?? 0, 2) }} | Yape: Bs. {{ number_format($closing->total_qr_yape ?? 0, 2) }}
                </p>
            </div>

            <!-- 4. Balance Neto en Caja -->
            <div class="bg-white border border-zinc-200 rounded p-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-zinc-600">Balance Neto en Caja</span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="text-[11px] text-zinc-700 hover:underline">Cuadre &rarr;</a>
                </div>
                <div class="mt-2 text-2xl font-bold tracking-tight {{ ($closing->net_cash_closing ?? 0) >= 0 ? 'text-zinc-900' : 'text-red-600' }}">
                    Bs. {{ number_format($closing->net_cash_closing ?? 0, 2) }}
                </div>
                <p class="mt-1 text-[11px] text-zinc-600">Ingresos netos menos personal y gastos</p>
            </div>
        </div>

        <!-- Detalle Tabular Desglosado del Cierre -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Columna Ingresos -->
            <div class="bg-white border border-zinc-200 rounded">
                <div class="px-4 py-3 border-b border-zinc-200 flex items-center justify-between">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-700">Ingresos Registrados</h3>
                    <span class="text-xs font-bold text-zinc-900">
                        Bs. {{ number_format(($closing->total_card_net ?? 0) + ($closing->total_qr_yasta ?? 0) + ($closing->total_qr_yape ?? 0) + ($closing->total_cash_invoices ?? 0), 2) }}
                    </span>
                </div>
                <table class="w-full text-xs text-left">
                    <tbody class="divide-y divide-zinc-100">
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">Tarjetas POS (Bruto)</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_card ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">Comisión POS ({{ number_format($session->pos_commission_rate * 100, 1) }}%)</td>
                            <td class="px-4 py-2.5 text-right font-mono text-red-600">- Bs. {{ number_format($closing->total_card_commission ?? 0, 2) }}</td>
                        </tr>
                        <tr class="bg-zinc-50 font-medium">
                            <td class="px-4 py-2 text-zinc-800">Tarjetas Neto Recibido</td>
                            <td class="px-4 py-2 text-right font-mono text-zinc-900">Bs. {{ number_format($closing->total_card_net ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">QR Yasta (Banco Unión)</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_qr_yasta ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">QR Yape (Banco BCP)</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_qr_yape ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">Efectivo por Facturas</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_cash_invoices ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">Tienda: Guardarropa</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_guardarropa ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">Tienda: Snacks & Golosinas</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_snacks ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Columna Egresos -->
            <div class="bg-white border border-zinc-200 rounded">
                <div class="px-4 py-3 border-b border-zinc-200 flex items-center justify-between">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-700">Egresos Registrados</h3>
                    <span class="text-xs font-bold text-zinc-900">
                        Bs. {{ number_format(($closing->total_staff_paid ?? 0) + ($closing->total_expenses ?? 0), 2) }}
                    </span>
                </div>
                <table class="w-full text-xs text-left">
                    <tbody class="divide-y divide-zinc-100">
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">Pagos al Personal (Staff, Bartenders, Seguridad)</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2.5 text-zinc-600">Gastos Operativos (Internos y Externos)</td>
                            <td class="px-4 py-2.5 text-right font-mono text-zinc-800">Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}</td>
                        </tr>
                        <tr class="bg-zinc-50 font-semibold">
                            <td class="px-4 py-2 text-zinc-800">Total Egresos de la Noche</td>
                            <td class="px-4 py-2 text-right font-mono text-zinc-900">
                                Bs. {{ number_format(($closing->total_staff_paid ?? 0) + ($closing->total_expenses ?? 0), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="p-4 border-t border-zinc-200 bg-zinc-50 flex items-center justify-between">
                    <span class="text-xs font-bold text-zinc-900">Balance Final de Caja:</span>
                    <span class="text-sm font-mono font-bold text-zinc-900">
                        Bs. {{ number_format($closing->net_cash_closing ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Acceso Rápido a los 6 Módulos -->
        <div class="bg-white border border-zinc-200 rounded p-4">
            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-700 mb-3">Accesos Directos a Módulos</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="{{ route('products.index') }}" class="p-3 border border-zinc-200 rounded hover:bg-zinc-50 text-center">
                    <div class="text-xs font-medium text-zinc-800">Inventario</div>
                    <div class="text-[11px] text-zinc-600">{{ $totalProducts }} productos</div>
                </a>
                <a href="{{ route('staff.index') }}" class="p-3 border border-zinc-200 rounded hover:bg-zinc-50 text-center">
                    <div class="text-xs font-medium text-zinc-800">Personal</div>
                    <div class="text-[11px] text-zinc-600">{{ $totalStaff }} registrados</div>
                </a>
                <a href="{{ route('sales.index', ['session_id' => $session->id]) }}" class="p-3 border border-zinc-200 rounded hover:bg-zinc-50 text-center">
                    <div class="text-xs font-medium text-zinc-800">Ventas Barra</div>
                    <div class="text-[11px] text-zinc-600">Principal / Subte</div>
                </a>
                <a href="{{ route('invoices.index', ['session_id' => $session->id]) }}" class="p-3 border border-zinc-200 rounded hover:bg-zinc-50 text-center">
                    <div class="text-xs font-medium text-zinc-800">Facturas POS</div>
                    <div class="text-[11px] text-zinc-600">Tarjetas y Efectivo</div>
                </a>
                <a href="{{ route('qrs.index', ['session_id' => $session->id]) }}" class="p-3 border border-zinc-200 rounded hover:bg-zinc-50 text-center">
                    <div class="text-xs font-medium text-zinc-800">Cobros QR</div>
                    <div class="text-[11px] text-zinc-600">Yasta / Yape</div>
                </a>
                <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="p-3 border border-zinc-200 rounded hover:bg-zinc-50 text-center">
                    <div class="text-xs font-medium text-zinc-800">Cierre de Caja</div>
                    <div class="text-[11px] text-zinc-600">Cuadre y Gastos</div>
                </a>
            </div>
        </div>

    @endif

</div>
@endsection
