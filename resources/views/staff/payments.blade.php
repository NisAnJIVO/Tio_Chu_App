@extends('layouts.app')

@section('title', 'Pagos a Personal')

@section('content')
<div class="space-y-6">

    <!-- Cabecera -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Pagos a Personal</h2>
            <p class="text-xs text-zinc-600">Registro de liquidación por turno nocturno según formato oficial de Don Ludo.</p>
        </div>

        <!-- Selector de Noche -->
        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('staffPayments.index') }}" class="flex items-center gap-2">
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
            <p class="text-xs text-zinc-600 mt-1 mb-4">Crea una noche para registrar la planilla de pagos.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- Resumen de Pagos de la Noche -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Total Planilla ({{ $session->day_name }})</span>
                <div class="mt-1 text-2xl font-bold font-mono text-zinc-900">
                    Bs. {{ number_format($totalPlanilla, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">{{ $attendances->count() }} trabajadores programados</p>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Total Liquidado (Pagado)</span>
                <div class="mt-1 text-2xl font-bold font-mono text-zinc-900">
                    Bs. {{ number_format($totalPagado, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Efectivo entregado al personal</p>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Pendiente por Pagar</span>
                <div class="mt-1 text-2xl font-bold font-mono text-zinc-900">
                    Bs. {{ number_format($totalPendiente, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Saldo restante en caja</p>
            </div>
        </div>

        <!-- Desglose por Tipo de Personal -->
        <div class="bg-white border border-zinc-200 rounded p-4">
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-700 block mb-2">Desglose por Tipo de Cargo</span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="border border-zinc-200 rounded p-3 bg-zinc-50">
                    <span class="font-bold text-zinc-900 block">Meseros / Limpieza</span>
                    <span class="text-zinc-600 block mt-1">Tarifa regular: Bs. 100 (Bs. 110 domingos)</span>
                    <div class="mt-2 font-mono font-bold text-zinc-900 text-sm">
                        {{ $summaryByType['meseros']['count'] }} pers. — Bs. {{ number_format($summaryByType['meseros']['total'], 2) }}
                    </div>
                </div>

                <div class="border border-zinc-200 rounded p-3 bg-zinc-50">
                    <span class="font-bold text-zinc-900 block">Bartenders (Kelly / Ariel)</span>
                    <span class="text-zinc-600 block mt-1">Tarifa: Bs. 100 - 110 por turno</span>
                    <div class="mt-2 font-mono font-bold text-zinc-900 text-sm">
                        {{ $summaryByType['bartenders']['count'] }} pers. — Bs. {{ number_format($summaryByType['bartenders']['total'], 2) }}
                    </div>
                </div>

                <div class="border border-zinc-200 rounded p-3 bg-zinc-50">
                    <span class="font-bold text-zinc-900 block">Guardias / Seguridad (S)</span>
                    <span class="text-zinc-600 block mt-1">Tarifa: Bs. 120 (Bs. 130 algunos sábados)</span>
                    <div class="mt-2 font-mono font-bold text-zinc-900 text-sm">
                        {{ $summaryByType['seguridad']['count'] }} pers. — Bs. {{ number_format($summaryByType['seguridad']['total'], 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Acciones Rápidas -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white border border-zinc-200 rounded p-4">
            <!-- Agregar Refuerzo / Extra -->
            @if($availableStaff->isNotEmpty())
                <form method="POST" action="{{ route('staffPayments.store') }}" class="flex items-center gap-2 flex-1">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                    <select name="staff_id" required class="text-xs border border-zinc-300 rounded px-2.5 py-1.5 text-zinc-900 bg-white focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        <option value="" disabled selected>+ Agregar Trabajador a esta noche...</option>
                        @foreach($availableStaff as $as)
                            <option value="{{ $as->id }}">
                                {{ $as->name }} ({{ $as->role }}) — Tarifa: Bs. {{ number_format($as->getPayForDay($session->day_name), 2) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800 whitespace-nowrap">
                        + Agregar
                    </button>
                </form>
            @else
                <span class="text-xs text-zinc-500">Todo el personal activo está incluido en esta noche.</span>
            @endif

            <!-- Marcar Todos como Pagados -->
            <form method="POST" action="{{ route('staffPayments.markAllPaid', $session) }}" onsubmit="return confirm('¿Marcar a todo el personal como PAGADO?');">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 border border-zinc-300 text-xs font-semibold text-zinc-800 rounded">
                    ✓ Marcar Todos como Pagados
                </button>
            </form>
        </div>

        <!-- Tabla de Planilla Oficial (Igual al Excel) -->
        <form method="POST" action="{{ route('staffPayments.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="night_session_id" value="{{ $session->id }}">

            <div class="bg-white border border-zinc-200 rounded overflow-hidden">
                <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                        PERSONAL: {{ mb_strtoupper($session->day_name) }} {{ \Carbon\Carbon::parse($session->session_date)->format('d-m-Y') }}
                    </h3>
                    <span class="text-xs font-mono font-bold text-zinc-900">
                        TOTAL: Bs. {{ number_format($totalPlanilla, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-zinc-100/70 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2.5 w-12 text-center">N°</th>
                                <th class="px-4 py-2.5">NOMBRE</th>
                                <th class="px-4 py-2.5">CARGO / ÁREA</th>
                                <th class="px-4 py-2.5 w-32 text-right">MONTO (Bs.)</th>
                                <th class="px-4 py-2.5 w-24 text-center">PAGADO</th>
                                <th class="px-4 py-2.5 w-16 text-right">ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @forelse($attendances as $index => $att)
                                <tr class="hover:bg-zinc-50/50">
                                    <td class="px-4 py-2 text-center font-mono text-zinc-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 font-bold text-zinc-900 uppercase">
                                        {{ $att->staff->name }}
                                    </td>
                                    <td class="px-4 py-2 text-zinc-600">
                                        {{ $att->staff->role }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <input type="number" step="5" name="attendances[{{ $att->id }}][pay_amount]" value="{{ $att->pay_amount }}" min="0"
                                               class="w-24 text-right border border-zinc-300 rounded px-2.5 py-1 text-xs font-mono font-bold text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" name="attendances[{{ $att->id }}][is_paid]" value="1" {{ $att->is_paid ? 'checked' : '' }}
                                               class="rounded border-zinc-300 text-zinc-900 focus:ring-0 w-4 h-4 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <button type="submit" 
                                                formaction="{{ route('staffPayments.destroy', $att) }}" 
                                                formmethod="POST"
                                                onclick="return confirm('¿Quitar a {{ $att->staff->name }} de esta noche?');"
                                                class="text-red-600 hover:text-red-800 text-xs font-medium">
                                            Quitar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
                                        No hay personal asignado a esta noche.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-zinc-100 border-t-2 border-zinc-300 font-bold text-xs text-zinc-900">
                            <tr>
                                <td colspan="3" class="px-4 py-2.5 text-right uppercase tracking-wider">TOTAL PLANILLA:</td>
                                <td class="px-4 py-2.5 text-right font-mono text-sm">Bs. {{ number_format($totalPlanilla, 2) }}</td>
                                <td class="px-4 py-2.5 text-center font-mono">
                                    Pagado: Bs. {{ number_format($totalPagado, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-4 bg-zinc-50 border-t border-zinc-200 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                        Guardar Cambios de Pagos
                    </button>
                </div>
            </div>
        </form>

    @endif

</div>
@endsection
