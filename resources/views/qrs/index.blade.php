@extends('layouts.app')

@section('title', 'Cobros QR (Yasta / Yape)')

@section('content')
<div class="space-y-6">

    <!-- Cabecera de la Vista -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Cobros por QR (Banca Móvil)</h2>
            <p class="text-xs text-zinc-600">Registro separado por punto de venta (Barra Principal, Tienda, Subte) según formato oficial.</p>
        </div>

        <!-- Selector de Noche -->
        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('qrs.index') }}" class="flex items-center gap-2">
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
            <p class="text-xs text-zinc-600 mt-1 mb-4">Crea una noche para registrar cobros QR.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800">
                + Crear Noche
            </a>
        </div>
    @else

        <!-- Tarjetas de Resumen General -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Total General en QRs</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                    Bs. {{ number_format($totalGeneral, 2) }}
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Suma de los 3 puntos de venta</p>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Barra Principal</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                    Bs. {{ number_format($totalsByPos['Barra Principal']['total'], 2) }}
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Yasta: {{ number_format($totalsByPos['Barra Principal']['yasta'], 2) }} | Yape: {{ number_format($totalsByPos['Barra Principal']['yape'], 2) }}</p>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Tienda</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                    Bs. {{ number_format($totalsByPos['Tienda']['total'], 2) }}
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Yasta: {{ number_format($totalsByPos['Tienda']['yasta'], 2) }} | Yape: {{ number_format($totalsByPos['Tienda']['yape'], 2) }}</p>
            </div>

            <div class="bg-white border border-zinc-200 rounded p-4">
                <span class="text-xs font-medium text-zinc-600">Subte</span>
                <div class="mt-1 text-2xl font-bold tracking-tight text-zinc-900 font-mono">
                    Bs. {{ number_format($totalsByPos['Subte']['total'], 2) }}
                </div>
                <p class="text-[10px] text-zinc-500 mt-1">Yasta: {{ number_format($totalsByPos['Subte']['yasta'], 2) }} | Yape: {{ number_format($totalsByPos['Subte']['yape'], 2) }}</p>
            </div>
        </div>

        <!-- Formulario Compacto de Registro Rápido QR -->
        <div class="bg-white border border-zinc-200 rounded p-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-700 mb-3 pb-2 border-b border-zinc-100">
                Registrar Nuevo Cobro QR
            </h3>
            
            <form method="POST" action="{{ route('qrs.store') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                @csrf
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                <div>
                    <label for="point_of_sale" class="block text-[11px] font-semibold text-zinc-700 mb-1">Punto de Venta</label>
                    <select name="point_of_sale" id="point_of_sale" required 
                            class="w-full text-xs border border-zinc-300 rounded px-2.5 py-2 text-zinc-900 font-medium focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        <option value="Barra Principal">Barra Principal</option>
                        <option value="Tienda">Tienda</option>
                        <option value="Subte">Subte</option>
                    </select>
                </div>

                <div>
                    <label for="cobrante_name" class="block text-[11px] font-semibold text-zinc-700 mb-1">Nombre del Cobrante</label>
                    <input type="text" name="cobrante_name" id="cobrante_name" required placeholder="Ej. Ari, Kelly, Mau..."
                           class="w-full text-xs border border-zinc-300 rounded px-2.5 py-2 text-zinc-900 uppercase focus:outline-none focus:ring-1 focus:ring-zinc-900">
                </div>

                <div>
                    <label for="bank_app" class="block text-[11px] font-semibold text-zinc-700 mb-1">Aplicación / Banco</label>
                    <select name="bank_app" id="bank_app" required 
                            class="w-full text-xs border border-zinc-300 rounded px-2.5 py-2 text-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
                        <option value="YASTA">YASTA (Banco Unión)</option>
                        <option value="YAPE">YAPE (Banco BCP)</option>
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-[11px] font-semibold text-zinc-700 mb-1">Monto Cobrado (Bs.)</label>
                    <input type="number" step="0.5" name="amount" id="amount" required placeholder="0.00"
                           class="w-full text-xs border border-zinc-300 rounded px-2.5 py-2 text-zinc-900 font-mono font-bold focus:outline-none focus:ring-1 focus:ring-zinc-900">
                </div>

                <div>
                    <button type="submit" class="w-full py-2 bg-zinc-900 text-white text-xs font-semibold rounded hover:bg-zinc-800 transition-colors">
                        + Registrar Cobro
                    </button>
                </div>
            </form>
        </div>

        <!-- Vista de 3 Columnas Separadas como en el Excel Oficial (Barra, Tienda, Subte) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            @foreach($pointsOfSale as $posName)
                @php
                    $list = $paymentsByPos[$posName];
                    $subtotals = $totalsByPos[$posName];
                @endphp
                <div class="bg-white border border-zinc-200 rounded flex flex-col h-full overflow-hidden">
                    
                    <!-- Encabezado de Columna -->
                    <div class="px-4 py-3 bg-zinc-50 border-b border-zinc-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">
                                {{ $posName }}
                            </h3>
                            <span class="text-xs font-mono font-bold text-zinc-900">
                                Bs. {{ number_format($subtotals['total'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-zinc-500 mt-1 font-mono">
                            <span>YASTA: Bs. {{ number_format($subtotals['yasta'], 2) }}</span>
                            <span>YAPE: Bs. {{ number_format($subtotals['yape'], 2) }}</span>
                            <span class="text-zinc-600">({{ $list->count() }} cobros)</span>
                        </div>
                    </div>

                    <!-- Tabla de Transacciones del Punto de Venta -->
                    <div class="flex-1 overflow-y-auto max-h-[500px]">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-zinc-100/70 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider sticky top-0">
                                <tr>
                                    <th class="px-3 py-2">Nombre del Cobrante</th>
                                    <th class="px-2 py-2">App</th>
                                    <th class="px-3 py-2 text-right">Monto (Bs.)</th>
                                    <th class="px-2 py-2 text-right w-12">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                @forelse($list as $qr)
                                    <tr class="hover:bg-zinc-50/50">
                                        <td class="px-3 py-2 font-medium text-zinc-900 uppercase">{{ $qr->operator_name }}</td>
                                        <td class="px-2 py-2">
                                            @if($qr->bank_app === 'YASTA')
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-800 border border-zinc-300">
                                                    YASTA
                                                </span>
                                            @else
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                                    YAPE
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-right font-mono font-bold text-zinc-900">
                                            Bs. {{ number_format($qr->amount, 2) }}
                                        </td>
                                        <td class="px-2 py-2 text-right">
                                            <form method="POST" action="{{ route('qrs.destroy', $qr) }}" onsubmit="return confirm('¿Eliminar este cobro QR?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-[11px]">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-8 text-center text-zinc-500 text-xs">
                                            Sin cobros QR en {{ $posName }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pie de la Columna -->
                    <div class="px-3 py-2 bg-zinc-50 border-t border-zinc-200 text-right">
                        <span class="text-[11px] text-zinc-600 mr-2">Total {{ $posName }}:</span>
                        <span class="text-xs font-mono font-bold text-zinc-900">Bs. {{ number_format($subtotals['total'], 2) }}</span>
                    </div>

                </div>
            @endforeach

        </div>

    @endif

</div>
@endsection
