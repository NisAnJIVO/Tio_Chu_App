@extends('layouts.app')

@section('title', 'Facturas & Tarjetas')

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
                Facturas & Tarjetas (Tarjeteos)
            </h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                Control de facturas emitidas y cobros pasados por máquina de tarjeta
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center">
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
            <p class="text-xs text-zinc-400 mt-1 mb-4">Apertura una noche para registrar facturas y cobros con tarjeta.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all dilemo-btn inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. MÉTRICAS CLARAS Y DIRECTAS PARA DON LUDO
             ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            
            <!-- 1. Pasado por Tarjeta (Bruto) -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Pasado por Tarjeta</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjeta, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Total según vouchers pasados por máquina</p>
            </div>

            <!-- 2. Comisión del Banco -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">
                    Comisión Banco ({{ number_format(($session->pos_commission_rate ?? 0.035) * 100, 1) }}%)
                </span>
                <div class="mt-2 text-2xl font-black font-mono text-rose-400 tracking-tight">
                    - <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjetaComision, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Retención del procesador de tarjeta</p>
            </div>

            <!-- 3. Plata que Entra al Banco (Neto) -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Plata que Entra al Banco</span>
                <div class="mt-2 text-2xl font-black font-mono text-[#F5B81C] tracking-tight">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjetaNeto, 2) }}
                </div>
                <p class="text-[11px] text-zinc-400 mt-1">Dinero real que ingresa a la cuenta</p>
            </div>

            <!-- 4. Facturas en Efectivo -->
            <div class="theme-card rounded-2xl p-5 border theme-border">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Facturas en Efectivo</span>
                <div class="mt-2 text-2xl font-black font-mono text-white tracking-tight">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalEfectivo, 2) }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Dinero recibido en mano</p>
            </div>

        </div>

        <!-- ========================================================
             3. FORMULARIO DE REGISTRO & TABLA DE HISTORIAL
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            <!-- Panel Izquierdo: Formulario Rápido -->
            <div class="theme-card rounded-2xl p-5 border theme-border h-fit">
                <div class="border-b theme-border pb-3 mb-4">
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider font-sans">
                        Registrar Factura o Tarjeta
                    </h2>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        Anota el comprobante o voucher cobrado
                    </p>
                </div>
                
                <form method="POST" action="{{ route('invoices.store') }}" class="space-y-3.5 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                    @csrf
                    <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                    <div>
                        <label for="correlative_num" class="block text-xs font-semibold text-zinc-300 mb-1">N° de Factura</label>
                        <input type="number" name="correlative_num" id="correlative_num" required value="{{ $nextCorrelative }}" min="1"
                               class="w-full text-xs font-mono font-bold rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C]">
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-semibold text-zinc-300 mb-1">Forma de Pago</label>
                        <select name="payment_method" id="payment_method" required 
                                class="w-full text-xs font-semibold rounded-xl px-3 py-2.5 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                            <option value="tarjeta" class="bg-zinc-950">TARJETA (Máquina / Débito / Crédito)</option>
                            <option value="efectivo" class="bg-zinc-950">EFECTIVO (Pago en mano)</option>
                        </select>
                    </div>

                    <div>
                        <label for="bar_name" class="block text-xs font-semibold text-zinc-300 mb-1">Barra / Punto de Venta</label>
                        <select name="bar_name" id="bar_name" required 
                                class="w-full text-xs font-semibold rounded-xl px-3 py-2.5 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                            <option value="Principal" class="bg-zinc-950">Barra Kelly (Piso Principal)</option>
                            <option value="Subterraneo" class="bg-zinc-950">Barra Ariel (Subterráneo)</option>
                            <option value="Tienda" class="bg-zinc-950">Tienda</option>
                        </select>
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-semibold text-zinc-300 mb-1">Monto Cobrado (Bs.)</label>
                        <input type="number" step="0.5" name="amount" id="amount" required placeholder="0.00"
                               class="w-full text-sm font-mono font-bold rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-[#F5B81C] focus:outline-none focus:border-[#F5B81C]">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-semibold text-zinc-300 mb-1">Notas o N° Voucher (Opcional)</label>
                        <input type="text" name="notes" id="notes" placeholder="Ej. Voucher #1234, Tarjeta BCP..."
                               class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:border-[#F5B81C]">
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[#F5B81C] text-black font-bold text-xs rounded-xl hover:bg-[#e5ac18] active:scale-95 transition-all dilemo-btn cursor-pointer shadow-sm">
                        + Registrar Factura / Tarjeta
                    </button>
                </form>
            </div>

            <!-- Panel Derecho: Tabla de Historial -->
            <div class="lg:col-span-2 rounded-2xl theme-card border theme-border overflow-hidden">
                <div class="px-5 py-3.5 border-b theme-border bg-zinc-950 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-300 font-sans">
                        Historial de Facturas Emitidas ({{ $invoices->count() }})
                    </h3>
                    <span class="text-xs font-mono font-bold text-[#F5B81C]">
                        Total Facturado: Bs. {{ number_format($totalGeneral, 2) }}
                    </span>
                </div>

                <div class="overflow-x-auto max-h-[520px]">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[11px] tracking-wider sticky top-0 font-semibold">
                            <tr>
                                <th class="px-3 py-3 w-12 text-center">N°</th>
                                <th class="px-3.5 py-3">Forma Pago</th>
                                <th class="px-3.5 py-3">Barra</th>
                                <th class="px-3.5 py-3 text-right">Monto Bruto</th>
                                <th class="px-3.5 py-3 text-right">Comisión Banco</th>
                                <th class="px-3.5 py-3 text-right">Neto al Banco</th>
                                <th class="px-3.5 py-3">Notas</th>
                                <th class="px-3 py-3 text-right w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y theme-border font-sans">
                            @forelse($invoices as $inv)
                                <tr class="hover:bg-zinc-900/30 transition-colors">
                                    <td class="px-3 py-2.5 text-center font-bold font-mono text-zinc-300">#{{ $inv->correlative_num }}</td>
                                    <td class="px-3.5 py-2.5">
                                        @if($inv->payment_method === 'tarjeta')
                                            <span class="text-purple-400 font-semibold text-xs">Tarjeta</span>
                                        @else
                                            <span class="text-zinc-300 font-semibold text-xs">Efectivo</span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-2.5 text-zinc-300 font-medium">
                                        {{ $inv->bar_name ?? 'Principal' }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right font-bold font-mono text-white">
                                        Bs. {{ number_format($inv->amount, 2) }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right font-mono text-rose-400 font-semibold">
                                        {{ $inv->commission_amount > 0 ? '- Bs. ' . number_format($inv->commission_amount, 2) : '—' }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right font-black font-mono text-[#F5B81C]">
                                        Bs. {{ number_format($inv->net_amount, 2) }}
                                    </td>
                                    <td class="px-3.5 py-2.5 text-zinc-400 text-[11px] truncate max-w-[140px]">{{ $inv->notes ?? '—' }}</td>
                                    <td class="px-3 py-2.5 text-right">
                                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" onsubmit="return confirm('¿Eliminar factura?');" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs cursor-pointer">Borrar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-zinc-500 font-sans">
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
