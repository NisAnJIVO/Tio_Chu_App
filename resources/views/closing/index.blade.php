@extends('layouts.app')

@section('title', 'Cierre de Caja')

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
         1. CABECERA PRINCIPAL: TÍTULO, SELECTOR DE NOCHE & ACCIONES
         ======================================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-3.5 rounded-2xl theme-card border theme-border">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-sans">
                Cierre de Caja
            </h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                Arqueo final de la noche: entradas, salidas y dinero que debe quedar en caja
            </p>
        </div>

        <!-- Selector de Noche y Botón de Estado -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('closing.index') }}" class="flex items-center">
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

                @if($session)
                    @if($session->isOpen())
                        <form method="POST" action="{{ route('closing.close', $session) }}" onsubmit="return confirm('¿Seguro que deseas dar por cerrada esta noche de atención?');">
                            @csrf
                            <button type="submit" class="px-4 py-1.5 rounded-xl bg-rose-500/15 border border-rose-500/40 text-rose-300 hover:bg-rose-500 hover:text-white font-bold text-xs transition-all cursor-pointer dilemo-btn">
                                Cerrar Noche Definitiva
                            </button>
                        </form>
                    @else
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs text-zinc-400">
                                <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <span>Noche cerrada</span>
                            </div>
                            <form method="POST" action="{{ route('closing.reopen', $session) }}" onsubmit="return confirm('¿Deseas reabrir esta noche para editar inventario o ventas?');">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-zinc-950 border border-zinc-800 text-zinc-300 hover:text-white font-semibold text-xs transition-all cursor-pointer">
                                    Reabrir Noche
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>

    @if(!$session)
        <!-- Estado Vacío -->
        <div class="p-12 text-center rounded-2xl theme-card border theme-border">
            <h3 class="text-sm font-bold uppercase tracking-wider text-white">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs text-zinc-400 mt-1 mb-4">Apertura una noche para ver el resumen del cierre de caja.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all dilemo-btn inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. CUENTAS CLARAS: ENTRADAS, SALIDAS Y DINERO EN CAJA
             ======================================================== -->
        <div class="rounded-2xl theme-card border theme-border p-6 shadow-sm">
            
            <!-- Cabecera del Cuadre -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-5 border-b theme-border">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight font-sans">
                        Cuentas Claras: Entradas y Salidas
                    </h2>
                    <p class="text-xs text-zinc-400 mt-0.5">
                        {{ $session->day_name }} {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }} — Liquidación consolidada de la jornada
                    </p>
                </div>
                
                <div class="text-left md:text-right px-4 py-2.5 rounded-xl bg-zinc-950 border border-zinc-800">
                    <span class="text-xs text-zinc-400 block font-normal">Plata que debe quedar en caja:</span>
                    <span class="text-2xl font-black font-mono tracking-tight {{ ($closing->net_cash_closing ?? 0) >= 0 ? 'text-[#F5B81C]' : 'text-rose-400' }}">
                        <span class="text-sm font-sans mr-0.5 font-bold">Bs.</span>{{ number_format($closing->net_cash_closing ?? 0, 2) }}
                    </span>
                </div>
            </div>

            <!-- Columnas Entradas vs Salidas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-5">
                
                <!-- 1. TODO EL DINERO QUE ENTRÓ -->
                <div>
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b theme-border">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <h3 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-sans">1. Todo el Dinero que Entró</h3>
                    </div>
                    
                    <table class="w-full text-xs text-left font-mono border-collapse">
                        <tbody class="divide-y theme-border">
                            <tr class="hover:bg-zinc-900/30">
                                <td class="py-2.5 text-zinc-300 font-sans">Tarjetas Netas al Banco</td>
                                <td class="py-2.5 text-right font-bold text-white">Bs. {{ number_format($closing->total_card_net ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-zinc-900/30">
                                <td class="py-1 text-zinc-500 font-sans text-[11px] pl-3">↳ Vouchers pasados: Bs. {{ number_format($closing->total_card ?? 0, 2) }}</td>
                                <td class="py-1 text-right text-zinc-500 text-[11px]">- Bs. {{ number_format($closing->total_card_commission ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-zinc-900/30">
                                <td class="py-2.5 text-zinc-300 font-sans">Cobros QR YASTA (Banco Unión)</td>
                                <td class="py-2.5 text-right font-bold text-white">Bs. {{ number_format($closing->total_qr_yasta ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-zinc-900/30">
                                <td class="py-2.5 text-zinc-300 font-sans">Cobros QR YAPE (Banco BCP)</td>
                                <td class="py-2.5 text-right font-bold text-white">Bs. {{ number_format($closing->total_qr_yape ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-zinc-900/30">
                                <td class="py-2.5 font-sans">
                                    <span class="text-zinc-300 block">Efectivo en Mano</span>
                                    <span class="text-[10px] text-zinc-500 font-normal">Suma de efectivos de Barra Kelly, Ariel y Tienda</span>
                                </td>
                                <td class="py-2.5 text-right font-bold text-white align-top">Bs. {{ number_format($closing->total_cash_invoices ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-t theme-border font-bold bg-zinc-950">
                                <td class="py-3 px-3 text-white font-sans">Total Dinero Ingresado</td>
                                <td class="py-3 px-3 text-right text-emerald-400 font-black text-sm">
                                    Bs. {{ number_format(($closing->total_card_net ?? 0) + ($closing->total_qr_yasta ?? 0) + ($closing->total_qr_yape ?? 0) + ($closing->total_cash_invoices ?? 0), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-[11px] text-zinc-500 mt-2 font-sans">
                        * Valor referencial de bebidas consumidas en carta: Bs. {{ number_format($closing->total_bar_sales ?? 0, 2) }}
                    </p>
                </div>

                <!-- 2. TODO LO QUE SE PAGÓ -->
                <div>
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b theme-border">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <h3 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-sans">2. Todo lo que se Pagó en la Noche</h3>
                    </div>
                    
                    <table class="w-full text-xs text-left font-mono border-collapse">
                        <tbody class="divide-y theme-border">
                            <tr class="hover:bg-zinc-900/30">
                                <td class="py-3 text-zinc-300 font-sans">Pago a Personal (Staff, Bartenders, Seguridad)</td>
                                <td class="py-3 text-right text-rose-400 font-bold">Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}</td>
                            </tr>
                            <tr class="hover:bg-zinc-900/30">
                                <td class="py-3 text-zinc-300 font-sans">Gastos Operativos (Hielo, insumos de urgencia, DJ)</td>
                                <td class="py-3 text-right text-rose-400 font-bold">Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}</td>
                            </tr>
                            <tr class="border-t theme-border font-bold bg-zinc-950">
                                <td class="py-3 px-3 text-white font-sans">Total Pagado en el Turno</td>
                                <td class="py-3 px-3 text-right text-rose-400 font-black text-sm">
                                    - Bs. {{ number_format(($closing->total_staff_paid ?? 0) + ($closing->total_expenses ?? 0), 2) }}
                                </td>
                            </tr>
                            <tr class="border-t theme-border font-bold bg-zinc-950">
                                <td class="py-3 px-3 text-[#F5B81C] font-sans">PLATA QUE DEBE QUEDAR EN CAJA</td>
                                <td class="py-3 px-3 text-right text-[#F5B81C] font-black text-sm">
                                    Bs. {{ number_format($closing->net_cash_closing ?? 0, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- ========================================================
             3. PAGO A PERSONAL & GASTOS DE LA NOCHE (LADO A LADO)
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <!-- Resumen de Pagos al Personal -->
            <div class="rounded-2xl theme-card border theme-border p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b theme-border mb-3">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 font-sans">
                                Pagos al Personal — {{ $session->day_name }}
                            </h3>
                            <p class="text-[11px] text-zinc-400">{{ $attendances->count() }} trabajadores asignados para esta noche</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-[#F5B81C]">
                            Total: Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="p-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs">
                            <span class="text-zinc-400 block font-normal">Total Pagado:</span>
                            <span class="font-bold font-mono text-emerald-400 text-sm">
                                Bs. {{ number_format($closing->total_staff_paid ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-zinc-950 border border-zinc-800 text-xs">
                            <span class="text-zinc-400 block font-normal">Planilla Estimada:</span>
                            <span class="font-bold font-mono text-white text-sm">
                                Bs. {{ number_format($attendances->sum('pay_amount'), 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto max-h-[220px] rounded-xl border border-zinc-800 mb-3">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[10px] tracking-wider font-semibold sticky top-0">
                                <tr>
                                    <th class="px-3 py-2">Personal</th>
                                    <th class="px-3 py-2 text-right">Monto</th>
                                    <th class="px-3 py-2 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y theme-border font-sans">
                                @forelse($attendances as $att)
                                    <tr class="hover:bg-zinc-900/30">
                                        <td class="px-3 py-2 font-medium text-white">{{ $att->staff->name }}</td>
                                        <td class="px-3 py-2 text-right font-mono text-zinc-200">Bs. {{ number_format($att->pay_amount, 2) }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($att->is_paid)
                                                <span class="text-emerald-400 font-semibold text-xs">Pagado</span>
                                            @else
                                                <span class="text-zinc-400 font-medium text-xs">Pendiente</span>
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

                <div class="pt-3 border-t theme-border flex justify-end">
                    <a href="{{ route('staffPayments.index', ['session_id' => $session->id]) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#F5B81C] hover:underline transition-colors flex items-center gap-1">
                        <span>Gestionar Pagos en Personal</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Registro y Lista de Gastos Operativos -->
            <div class="rounded-2xl theme-card border theme-border p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b theme-border mb-3">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-200 font-sans">
                                Gastos de la Noche (Compras e Imprevistos)
                            </h3>
                            <p class="text-[11px] text-zinc-400">{{ $expenses->count() }} gastos registrados en el turno</p>
                        </div>
                        <span class="text-xs font-mono font-bold text-rose-400">
                            Total: Bs. {{ number_format($closing->total_expenses ?? 0, 2) }}
                        </span>
                    </div>

                    <!-- Formulario de Inserción Rápida -->
                    <form method="POST" action="{{ route('closing.expenses.store') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-3 pb-3 border-b theme-border {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                        @csrf
                        <input type="hidden" name="night_session_id" value="{{ $session->id }}">

                        <div class="sm:col-span-2">
                            <input type="text" name="description" required placeholder="Descripción (Hielo, taxis, insumos...)"
                                   class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white placeholder-zinc-500 focus:outline-none focus:border-[#F5B81C]">
                        </div>

                        <div>
                            <select name="category" class="w-full text-xs rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C] cursor-pointer">
                                <option value="interno" class="bg-zinc-950">Interno</option>
                                <option value="externo" class="bg-zinc-950">Externo</option>
                            </select>
                        </div>

                        <div>
                            <input type="number" step="0.5" name="amount" required placeholder="Bs. Monto"
                                   class="w-full text-xs font-mono font-bold rounded-xl px-3 py-2 bg-zinc-950 border border-zinc-800 text-white focus:outline-none focus:border-[#F5B81C]">
                        </div>

                        <div class="sm:col-span-4 flex justify-end">
                            <button type="submit" class="px-4 py-1.5 bg-[#F5B81C] text-black font-bold text-xs rounded-xl hover:bg-[#e5ac18] transition-all dilemo-btn cursor-pointer shadow-sm">
                                + Agregar Gasto
                            </button>
                        </div>
                    </form>

                    <!-- Listado de Gastos Registrados -->
                    <div class="overflow-x-auto max-h-[200px] rounded-xl border border-zinc-800">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead class="bg-zinc-950 border-b theme-border text-zinc-400 uppercase text-[10px] tracking-wider font-semibold sticky top-0">
                                <tr>
                                    <th class="px-3 py-2">Descripción</th>
                                    <th class="px-3 py-2 text-center">Tipo</th>
                                    <th class="px-3 py-2 text-right">Monto</th>
                                    <th class="px-3 py-2 text-right w-12">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y theme-border font-sans">
                                @forelse($expenses as $exp)
                                    <tr class="hover:bg-zinc-900/30">
                                        <td class="px-3 py-2 font-medium text-white">{{ $exp->description }}</td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="text-zinc-400 text-[11px] font-medium">{{ ucfirst($exp->category) }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-right font-bold font-mono text-rose-400">
                                            Bs. {{ number_format($exp->amount, 2) }}
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <form method="POST" action="{{ route('closing.expenses.destroy', $exp) }}" onsubmit="return confirm('¿Eliminar gasto?');" class="{{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs cursor-pointer">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-zinc-500">No hay gastos registrados en esta noche.</td>
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
