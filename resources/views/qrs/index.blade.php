@extends('layouts.app')

@section('title', 'Cobros QR (Yasta / Yape)')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- ==========================================
         CABECERA DE LA VISTA (LIQUID GLASS)
         ========================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Módulo 4 — Pagos Digitales QR
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Banca Móvil Unión & BCP</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight text-white">
                Cobros por QR (Yasta & Yape)
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Conciliación por punto de venta (Barra Principal, Tienda, Subte) con verificación de comprobante.
            </p>
        </div>

        @if($allSessions->isNotEmpty())
            <form method="GET" action="{{ route('qrs.index') }}" class="glass-panel p-2 rounded-2xl border border-white/10 flex items-center gap-2">
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
            <p class="text-xs text-zinc-400 mt-1 mb-6">Crea una noche para registrar cobros QR.</p>
            <a href="{{ route('sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 transition-all shadow-md shadow-amber-500/20">
                + Crear Noche
            </a>
        </div>
    @else

        @if(!$session->isOpen())
            <div class="glass-panel p-4 rounded-2xl border border-rose-500/40 bg-rose-500/10 shadow-xl flex items-center justify-between gap-4">
                <div class="flex items-center gap-3"><span class="text-xl">🔒</span><div><h4 class="text-xs font-black text-rose-300 uppercase tracking-wider font-mono">Noche Cerrada (Modo Solo Lectura)</h4><p class="text-xs text-zinc-300 mt-0.5">Esta jornada fue finalizada en Cierre de Caja. Los cobros QR no pueden modificarse.</p></div></div>
                <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="px-3.5 py-1.5 glass-card border border-rose-400/30 text-rose-300 rounded-xl text-xs font-bold font-mono shrink-0">Ver en Cierre de Caja &rarr;</a>
            </div>
        @endif

        <!-- ==========================================
             TARJETAS DE RESUMEN EJECUTIVO (GRANDES Y LEGIBLES)
             ========================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            
            <div class="glass-panel-elevated rounded-2xl p-6 border border-amber-400/30">
                <span class="text-xs font-mono font-bold text-amber-400 uppercase tracking-wider">Total General en QRs</span>
                <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalGeneral, 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">Suma de los 3 puntos de venta</p>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Barra Principal</span>
                <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalsByPos['Barra Principal']['total'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    Yasta: {{ number_format($totalsByPos['Barra Principal']['yasta'], 0) }} · Yape: {{ number_format($totalsByPos['Barra Principal']['yape'], 0) }}
                </p>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Tienda</span>
                <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalsByPos['Tienda']['total'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    Yasta: {{ number_format($totalsByPos['Tienda']['yasta'], 0) }} · Yape: {{ number_format($totalsByPos['Tienda']['yape'], 0) }}
                </p>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <span class="text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Subte</span>
                <div class="mt-2 text-3xl font-black font-mono text-white tracking-tight">
                    <span class="text-amber-400 text-lg mr-0.5 font-sans">Bs.</span>{{ number_format($totalsByPos['Subte']['total'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    Yasta: {{ number_format($totalsByPos['Subte']['yasta'], 0) }} · Yape: {{ number_format($totalsByPos['Subte']['yape'], 0) }}
                </p>
            </div>

        </div>

        <!-- Formulario Rápido de Registro QR -->
        <div class="glass-panel rounded-2xl p-6 shadow-2xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 mb-3 pb-2 border-b border-white/10 font-mono">
                Registrar Nuevo Cobro QR
            </h3>
            
            <form method="POST" action="{{ route('qrs.store') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                @csrf
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                <div>
                    <label for="point_of_sale" class="block text-xs font-medium text-zinc-300 mb-1">Punto de Venta</label>
                    <select name="point_of_sale" id="point_of_sale" required 
                            class="glass-input w-full text-xs rounded-xl px-3 py-2 text-zinc-100 font-medium cursor-pointer">
                        <option value="Barra Principal" class="bg-[#12141c]">Barra Principal</option>
                        <option value="Tienda" class="bg-[#12141c]">Tienda</option>
                        <option value="Subte" class="bg-[#12141c]">Subte</option>
                    </select>
                </div>

                <div>
                    <label for="cobrante_name" class="block text-xs font-medium text-zinc-300 mb-1">Cobrante / Mesero</label>
                    <input type="text" name="cobrante_name" id="cobrante_name" required placeholder="Ej. Kelly, Mau, Ari..."
                           class="glass-input w-full text-xs rounded-xl px-3 py-2 text-zinc-100 uppercase placeholder-zinc-500">
                </div>

                <div>
                    <label for="bank_app" class="block text-xs font-medium text-zinc-300 mb-1">Aplicación Bancaria</label>
                    <select name="bank_app" id="bank_app" required 
                            class="glass-input w-full text-xs font-bold rounded-xl px-3 py-2 text-zinc-100 cursor-pointer">
                        <option value="YASTA" class="bg-[#12141c]">YASTA (Banco Unión)</option>
                        <option value="YAPE" class="bg-[#12141c]">YAPE (Banco BCP)</option>
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-xs font-medium text-zinc-300 mb-1">Monto Cobrado (Bs.)</label>
                    <input type="number" step="0.5" name="amount" id="amount" required placeholder="0.00"
                           class="glass-input w-full text-sm font-mono font-black rounded-xl px-3 py-2 text-amber-400">
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 text-xs font-bold rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-md shadow-amber-500/20 cursor-pointer">
                        + Registrar Cobro
                    </button>
                </div>
            </form>
        </div>

        <!-- 3 Columnas por Punto de Venta (Como en el Excel Oficial pero con Liquid Glass) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            @foreach($pointsOfSale as $posName)
                @php
                    $list = $paymentsByPos[$posName];
                    $subtotals = $totalsByPos[$posName];
                @endphp
                <div class="glass-panel rounded-2xl flex flex-col h-full overflow-hidden shadow-2xl">
                    
                    <!-- Encabezado de Columna -->
                    <div class="px-5 py-4 bg-white/[0.03] border-b border-white/10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black text-white uppercase tracking-wider font-mono">
                                {{ $posName }}
                            </h3>
                            <span class="text-sm font-mono font-black text-amber-400">
                                Bs. {{ number_format($subtotals['total'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] text-zinc-400 mt-1 font-mono">
                            <span>Yasta: Bs. {{ number_format($subtotals['yasta'], 2) }}</span>
                            <span>Yape: Bs. {{ number_format($subtotals['yape'], 2) }}</span>
                            <span class="text-zinc-500">({{ $list->count() }})</span>
                        </div>
                    </div>

                    <!-- Tabla de Transacciones -->
                    <div class="flex-1 overflow-y-auto max-h-[500px]">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-white/[0.04] border-b border-white/10 text-zinc-400 font-mono uppercase text-[10px] tracking-wider sticky top-0">
                                <tr>
                                    <th class="px-4 py-2.5">Cobrante</th>
                                    <th class="px-2 py-2.5">App</th>
                                    <th class="px-4 py-2.5 text-right">Monto</th>
                                    <th class="px-2 py-2.5 text-right w-12">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 font-mono">
                                @forelse($list as $qr)
                                    <tr class="hover:bg-white/[0.02]">
                                        <td class="px-4 py-2.5 font-bold text-zinc-200 uppercase font-sans">{{ $qr->operator_name }}</td>
                                        <td class="px-2 py-2.5">
                                            @if($qr->bank_app === 'YASTA')
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/25">
                                                    YASTA
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/25">
                                                    YAPE
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 text-right font-black text-white">
                                            Bs. {{ number_format($qr->amount, 2) }}
                                        </td>
                                        <td class="px-2 py-2.5 text-right">
                                            <form method="POST" action="{{ route('qrs.destroy', $qr) }}" onsubmit="return confirm('¿Eliminar este cobro QR?');" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold text-xs cursor-pointer">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-zinc-500 text-xs font-sans">
                                            Sin cobros QR en {{ $posName }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pie de la Columna -->
                    <div class="px-4 py-3 bg-white/[0.02] border-t border-white/10 text-right font-mono">
                        <span class="text-xs text-zinc-400 mr-2 font-sans">Subtotal {{ $posName }}:</span>
                        <span class="text-xs font-bold text-amber-400">Bs. {{ number_format($subtotals['total'], 2) }}</span>
                    </div>

                </div>
            @endforeach

        </div>

    @endif

</div>
@endsection
