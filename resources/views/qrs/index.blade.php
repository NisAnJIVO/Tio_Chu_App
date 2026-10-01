@extends('layouts.app')

@section('title', 'Cobros por QR')

@section('content')

<style>
    /* Ocultar flechas de números nativas */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
        appearance: textfield !important;
    }
</style>

<div class="space-y-4 max-w-7xl mx-auto w-full pb-8">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: TÍTULO & SELECTOR DE NOCHE
         ======================================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-3.5 rounded-2xl theme-card border theme-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-sans">
                Cobros por QR
            </h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                Transferencias bancarias recibidas por Yasta (Banco Unión) y Yape (Banco BCP)
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('qrs.index') }}" class="flex items-center">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs hover:border-[#F5B81C] transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-semibold text-white focus:outline-none cursor-pointer pr-1">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-zinc-950 text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'En Vivo' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

            <!-- Estado Noche Cerrada (Discreto y sin alarmas) -->
            @if($session && !$session->isOpen())
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs text-zinc-400">
                    <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Modo lectura</span>
                    <a href="{{ route('closing.index', ['session_id' => $session->id]) }}" class="text-[#F5B81C] hover:underline ml-1 font-semibold">Cierre &rarr;</a>
                </div>
            @endif
        </div>
    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-2xl theme-card border theme-border">
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-4">Apertura una noche para registrar cobros por QR.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all dilemo-btn inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. MÉTRICAS CLARAS Y DIRECTAS PARA DON LUDO
             ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            
            <!-- Total General en QRs -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Total Cobrado por QR</span>
                <div class="mt-2 text-2xl font-black font-mono text-[#F5B81C] tracking-tight">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalGeneral, 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1 font-mono">
                    Yasta: Bs. {{ number_format($totalYasta, 0) }} • Yape: Bs. {{ number_format($totalYape, 0) }}
                </p>
            </div>

            <!-- Barra Principal (Kelly) -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Barra Kelly (Principal)</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalsByPos['Barra Principal']['total'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                    Yasta: Bs. {{ number_format($totalsByPos['Barra Principal']['yasta'], 0) }} • Yape: Bs. {{ number_format($totalsByPos['Barra Principal']['yape'], 0) }}
                </p>
            </div>

            <!-- Tienda -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Tienda (Entrada)</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalsByPos['Tienda']['total'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                    Yasta: Bs. {{ number_format($totalsByPos['Tienda']['yasta'], 0) }} • Yape: Bs. {{ number_format($totalsByPos['Tienda']['yape'], 0) }}
                </p>
            </div>

            <!-- Subte (Ariel) -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Barra Ariel (Subte)</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalsByPos['Subte']['total'], 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1 font-mono">
                    Yasta: Bs. {{ number_format($totalsByPos['Subte']['yasta'], 0) }} • Yape: Bs. {{ number_format($totalsByPos['Subte']['yape'], 0) }}
                </p>
            </div>

        </div>

        <!-- ========================================================
             3. FORMULARIO RÁPIDO DE REGISTRO QR
             ======================================================== -->
        <div class="theme-card rounded-2xl p-5 border theme-border">
            <div class="border-b theme-border pb-3 mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider font-sans">
                        Registrar Nuevo Cobro QR
                    </h2>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        Anota la transferencia verificando el comprobante en la app
                    </p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('qrs.store') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3.5 items-end {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                @csrf
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                <div>
                    <label for="point_of_sale" class="block text-xs font-semibold text-zinc-300 mb-1">Punto de Venta</label>
                    <select name="point_of_sale" id="point_of_sale" required 
                            class="w-full text-xs font-semibold rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                        <option value="Barra Principal" class="bg-zinc-950">Barra Kelly (Principal)</option>
                        <option value="Tienda" class="bg-zinc-950">Tienda</option>
                        <option value="Subte" class="bg-zinc-950">Barra Ariel (Subte)</option>
                    </select>
                </div>

                <div>
                    <label for="cobrante_name" class="block text-xs font-semibold text-zinc-300 mb-1">Cobrante / Mesero</label>
                    <select name="cobrante_name" id="cobrante_name" required 
                            class="w-full text-xs font-semibold rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer uppercase">
                        <option value="" disabled selected class="bg-zinc-950 text-zinc-400">-- Seleccionar Personal --</option>
                        @php
                            $bartenders = $cobrantesStaff->filter(fn($s) => str_contains(mb_strtolower($s->role), 'bartender') || str_contains(mb_strtolower($s->role), 'barra'));
                            $refuerzos  = $cobrantesStaff->filter(fn($s) => str_contains(mb_strtolower($s->role), 'refuerzo'));
                            $meseros    = $cobrantesStaff->reject(fn($s) =>
                                str_contains(mb_strtolower($s->role), 'bartender') ||
                                str_contains(mb_strtolower($s->role), 'barra') ||
                                str_contains(mb_strtolower($s->role), 'refuerzo')
                            );
                        @endphp

                        @if($bartenders->isNotEmpty())
                            <optgroup label="BARTENDERS" class="bg-zinc-950 text-[#F5B81C] font-bold">
                                @foreach($bartenders as $b)
                                    <option value="{{ $b->name }}" class="bg-zinc-950 text-white">
                                        {{ $b->name }} ({{ $b->role }}{{ $b->assigned_bar ? ' — ' . $b->assigned_bar : '' }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($meseros->isNotEmpty())
                            <optgroup label="MESEROS" class="bg-zinc-950 text-[#F5B81C] font-bold">
                                @foreach($meseros as $m)
                                    <option value="{{ $m->name }}" class="bg-zinc-950 text-white">
                                        {{ $m->name }} ({{ $m->role }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($refuerzos->isNotEmpty())
                            <optgroup label="REFUERZOS" class="bg-zinc-950 text-[#F5B81C] font-bold">
                                @foreach($refuerzos as $r)
                                    <option value="{{ $r->name }}" class="bg-zinc-950 text-white">
                                        {{ $r->name }} ({{ $r->role }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <div>
                    <label for="bank_app" class="block text-xs font-semibold text-zinc-300 mb-1">Aplicación Bancaria</label>
                    <select name="bank_app" id="bank_app" required 
                            class="w-full text-xs font-semibold rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                        <option value="YASTA" class="bg-zinc-950">YASTA (Banco Unión)</option>
                        <option value="YAPE" class="bg-zinc-950">YAPE (Banco BCP)</option>
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-xs font-semibold text-zinc-300 mb-1">Monto Cobrado (Bs.)</label>
                    <input type="number" step="0.5" name="amount" id="amount" required placeholder="0.00"
                           class="w-full text-sm font-mono font-bold rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-[#F5B81C] focus:outline-none focus:border-[#F5B81C]">
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 bg-[#F5B81C] text-black font-bold text-xs rounded-xl hover:bg-[#e5ac18] active:scale-95 transition-all dilemo-btn cursor-pointer shadow-sm">
                        + Registrar Cobro
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================================
             4. 3 COLUMNAS POR PUNTO DE VENTA (TABLAS DE AUDITORÍA)
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            @foreach($pointsOfSale as $posName)
                @php
                    $list = $paymentsByPos[$posName];
                    $subtotals = $totalsByPos[$posName];
                    $displayPosName = match($posName) {
                        'Barra Principal' => 'Barra Kelly (Principal)',
                        'Subte' => 'Barra Ariel (Subte)',
                        default => $posName,
                    };
                @endphp
                <div class="rounded-2xl theme-card border theme-border flex flex-col h-full overflow-hidden">
                    
                    <!-- Encabezado de Columna -->
                    <div class="px-4 py-3.5 bg-zinc-950 border-b theme-border">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider font-sans">
                                {{ $displayPosName }}
                            </h3>
                            <span class="text-sm font-mono font-bold text-[#F5B81C]">
                                Bs. {{ number_format($subtotals['total'], 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] text-zinc-400 mt-1 font-mono">
                            <span>Yasta: Bs. {{ number_format($subtotals['yasta'], 0) }}</span>
                            <span>Yape: Bs. {{ number_format($subtotals['yape'], 0) }}</span>
                            <span class="text-zinc-500 font-sans">({{ $list->count() }} cobros)</span>
                        </div>
                    </div>

                    <!-- Tabla de Transacciones -->
                    <div class="flex-1 overflow-y-auto max-h-[460px]">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[10px] tracking-wider sticky top-0 font-semibold">
                                <tr>
                                    <th class="px-3.5 py-2.5">Cobrante</th>
                                    <th class="px-2 py-2.5 text-center">App</th>
                                    <th class="px-3.5 py-2.5 text-right">Monto</th>
                                    <th class="px-2 py-2.5 text-right w-12">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y theme-border font-sans">
                                @forelse($list as $qr)
                                    <tr class="hover:bg-zinc-900/30 transition-colors">
                                        <td class="px-3.5 py-2 font-medium text-zinc-200 uppercase">{{ $qr->operator_name }}</td>
                                        <td class="px-2 py-2 text-center">
                                            @if($qr->bank_app === 'YASTA')
                                                <span class="text-blue-400 font-bold text-[11px] font-mono">YASTA</span>
                                            @else
                                                <span class="text-purple-400 font-bold text-[11px] font-mono">YAPE</span>
                                            @endif
                                        </td>
                                        <td class="px-3.5 py-2 text-right font-bold font-mono text-white">
                                            Bs. {{ number_format($qr->amount, 2) }}
                                        </td>
                                        <td class="px-2 py-2 text-right">
                                            <form method="POST" action="{{ route('qrs.destroy', $qr) }}" onsubmit="return confirm('¿Eliminar este cobro QR?');" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs cursor-pointer">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-zinc-500 text-xs font-sans">
                                            Sin cobros QR en {{ $displayPosName }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pie de la Columna -->
                    <div class="px-4 py-3 bg-zinc-950 border-t theme-border flex items-center justify-between font-sans">
                        <span class="text-xs text-zinc-400 font-normal">Subtotal {{ $displayPosName }}:</span>
                        <span class="text-xs font-bold font-mono text-[#F5B81C]">Bs. {{ number_format($subtotals['total'], 2) }}</span>
                    </div>

                </div>
            @endforeach

        </div>

    @endif

</div>
@endsection
