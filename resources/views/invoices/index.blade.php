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

<div class="space-y-4 max-w-7xl mx-auto w-full pb-10">

    <!-- ========================================================
         1. CABECERA PRINCIPAL: TÍTULO & SELECTOR DE NOCHE
         ======================================================== -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-3.5 rounded-2xl notoria-card shadow-sm">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight theme-text-primary font-sans">
                Facturas & Tarjetas (Tarjeteos)
            </h1>
            <p class="text-xs theme-text-secondary mt-0.5">
                Control de comprobantes y cobros pasados por máquina de tarjeta
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Selector de Noche -->
            @if($allSessions->isNotEmpty())
                <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl notoria-subtile text-xs hover:border-[#F5B81C] transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#F5B81C] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                        </svg>
                        <select name="session_id" id="session_id" onchange="this.form.submit()" 
                                class="bg-transparent border-0 text-xs font-semibold theme-text-primary focus:outline-none cursor-pointer pr-1">
                            @foreach($allSessions as $s)
                                <option value="{{ $s->id }}" {{ $session && $session->id === $s->id ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $s->day_name }} {{ \Carbon\Carbon::parse($s->session_date)->format('d/m/Y') }} ({{ $s->isOpen() ? 'En Vivo' : 'Cerrada' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif

            <!-- Estado Noche Cerrada -->
            @if($session && !$session->isOpen())
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl notoria-subtile text-xs theme-text-muted">
                    <svg class="w-3.5 h-3.5 theme-text-muted shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
        <div class="p-12 text-center rounded-2xl notoria-card shadow-sm">
            <h3 class="text-sm font-bold uppercase tracking-wider theme-text-primary">No hay ninguna noche abierta o seleccionada</h3>
            <p class="text-xs theme-text-secondary mt-1 mb-4">Apertura una noche para registrar cobros con tarjeta.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. KPIs RESUMEN TARJETAS (CARDS NOTORIAS)
             ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            
            <!-- 1. Pasado por Tarjeta (Bruto) -->
            <div class="notoria-card p-4 sm:p-5 shadow-sm">
                <span class="text-xs font-black theme-text-secondary uppercase tracking-wider block truncate">Pasado por Tarjeta</span>
                <div class="mt-2 text-2xl sm:text-3xl font-black font-mono theme-text-primary tracking-tight truncate">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjeta, 2) }}
                </div>
                <p class="text-[11px] theme-text-muted mt-1 truncate">{{ $invoices->count() }} vouchers registrados</p>
            </div>

            <!-- 2. Comisión del Banco -->
            <div class="notoria-card p-4 sm:p-5 shadow-sm">
                <span class="text-xs font-black theme-text-secondary uppercase tracking-wider block truncate">
                    Comisión Banco ({{ number_format(($session->pos_commission_rate ?? 0.035) * 100, 1) }}%)
                </span>
                <div class="mt-2 text-2xl sm:text-3xl font-black font-mono text-rose-500 tracking-tight truncate">
                    - <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjetaComision, 2) }}
                </div>
                <p class="text-[11px] theme-text-muted mt-1 truncate">Retención del tarjetero</p>
            </div>

            <!-- 3. Neto al Banco -->
            <div class="notoria-card p-4 sm:p-5 shadow-sm">
                <span class="text-xs font-black theme-text-secondary uppercase tracking-wider block truncate">Neto al Banco</span>
                <div class="mt-2 text-2xl sm:text-3xl font-black font-mono text-[#F5B81C] tracking-tight truncate">
                    <span class="text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjetaNeto, 2) }}
                </div>
                <p class="text-[11px] theme-text-muted mt-1 truncate">Dinero neto acreditado</p>
            </div>

        </div>

        <!-- ========================================================
             3. CARD PROTAGONISTA: REGISTRAR TARJETA (POS)
             Simple, limpia, al grano y protagonista
             ======================================================== -->
        <div class="notoria-card rounded-2xl shadow-md border notoria-border p-5 sm:p-6 space-y-4">
            
            <!-- Cabecera de la Card -->
            <div class="flex items-center justify-between pb-3.5 border-b notoria-border">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-[#F5B81C]/15 border border-[#F5B81C]/50 flex items-center justify-center text-[#F5B81C] shrink-0 shadow-sm">
                        <svg class="w-7 h-7 text-[#F5B81C]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <rect width="20" height="14" x="2" y="5" rx="2.5"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                            <circle cx="7" cy="15" r="1.25" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black theme-text-primary uppercase tracking-wider font-sans">
                            Registrar Tarjeta (POS)
                        </h2>
                        <p class="text-xs theme-text-secondary mt-0.5">
                            Captura rápida de vouchers pasados por el tarjetero
                        </p>
                    </div>
                </div>

                <!-- Insignia del Siguiente N° de Factura -->
                <div class="flex items-center gap-2">
                    <span class="text-xs sm:text-sm font-mono font-black text-[#F5B81C] bg-[#F5B81C]/10 border border-[#F5B81C]/30 px-3.5 py-1.5 rounded-xl shadow-xs" title="Número autoincremental de factura">
                        Factura #{{ $nextCorrelative }}
                    </span>
                </div>
            </div>
            
            <!-- Formulario Simple y Al Grano -->
            <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form" class="space-y-4 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                @csrf
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                <input type="hidden" name="correlative_num" value="{{ $nextCorrelative }}">
                <input type="hidden" name="payment_method" value="tarjeta">
                <input type="hidden" name="bar_name" id="bar_name_input" value="Principal">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 sm:gap-4 items-end">
                    
                    <!-- 1. Punto de Venta / Barra (Píldoras iOS con texto destacado) -->
                    <div class="md:col-span-5 space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider theme-text-secondary">
                            Punto de Venta / Barra
                        </label>
                        <div class="grid grid-cols-3 p-1 rounded-xl notoria-subtile gap-1">
                            <button type="button"
                                    id="btn-bar-Principal"
                                    onclick="selectBar('Principal')"
                                    class="py-2.5 px-2 rounded-lg text-sm sm:text-base font-black transition-all cursor-pointer bg-[#F5B81C] text-black shadow-sm text-center truncate active:scale-95">
                                Principal
                            </button>
                            <button type="button"
                                    id="btn-bar-Subterraneo"
                                    onclick="selectBar('Subterraneo')"
                                    class="py-2.5 px-2 rounded-lg text-sm sm:text-base font-bold transition-all cursor-pointer theme-text-secondary hover:theme-text-primary text-center truncate active:scale-95">
                                Subterráneo
                            </button>
                            <button type="button"
                                    id="btn-bar-Tienda"
                                    onclick="selectBar('Tienda')"
                                    class="py-2.5 px-2 rounded-lg text-sm sm:text-base font-bold transition-all cursor-pointer theme-text-secondary hover:theme-text-primary text-center truncate active:scale-95">
                                Tienda
                            </button>
                        </div>
                    </div>

                    <!-- 2. Monto Cobrado (Bs.) -->
                    <div class="md:col-span-4 space-y-1.5">
                        <label for="amount" class="block text-xs font-bold uppercase tracking-wider theme-text-secondary">
                            Monto Cobrado (Bs.)
                        </label>
                        <div class="flex items-center rounded-xl notoria-subtile px-3.5 py-2 border notoria-border focus-within:border-[#F5B81C] transition-all">
                            <span class="text-sm font-mono font-bold text-[#F5B81C] mr-2 shrink-0">Bs.</span>
                            <input type="number" 
                                   step="0.5" 
                                   inputmode="decimal" 
                                   name="amount" 
                                   id="amount" 
                                   required 
                                   placeholder="0.00" 
                                   autofocus
                                   class="w-full text-lg font-mono font-black bg-transparent border-0 theme-text-primary placeholder-zinc-400 focus:outline-none p-0">
                        </div>
                    </div>

                    <!-- 3. Botón Registrar -->
                    <div class="md:col-span-3">
                        <button type="submit" 
                                class="w-full py-2.5 sm:py-3 bg-[#F5B81C] hover:bg-[#e5ac18] text-black font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl shadow-sm active:scale-95 transition-all cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Registrar Tarjeta</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <!-- ========================================================
             4. TABLA DE HISTORIAL DE FACTURAS EMITIDAS
             Ubicada abajo en ancho completo para consulta limpia
             ======================================================== -->
        <div class="notoria-card rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 notoria-card-header flex items-center justify-between border-b notoria-border">
                <h3 class="text-xs font-black uppercase tracking-wider text-white font-mono">
                    Historial de Facturas Emitidas ({{ $invoices->count() }})
                </h3>
                <span class="text-xs font-mono font-bold text-[#F5B81C]">
                    Total Facturado: Bs. {{ number_format($totalTarjeta, 2) }}
                </span>
            </div>

            <div class="overflow-x-auto max-h-[560px]">
                <table class="w-full min-w-[650px] text-xs text-left border-collapse">
                    <thead class="bg-slate-950 text-white border-b notoria-border uppercase text-[11px] tracking-wider sticky top-0 font-mono font-bold z-10">
                        <tr>
                            <th class="px-4 py-3 w-14 text-center">N°</th>
                            <th class="px-4 py-3">Barra</th>
                            <th class="px-4 py-3 text-right">Monto Bruto</th>
                            <th class="px-4 py-3 text-right">Comisión Banco</th>
                            <th class="px-4 py-3 text-right">Neto al Banco</th>
                            <th class="px-4 py-3 text-right w-20">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y notoria-border font-sans" id="invoices-table-body">
                        @forelse($invoices as $inv)
                            <tr id="invoice-row-{{ $inv->id }}" class="hover:bg-slate-500/10 transition-colors">
                                <td class="px-4 py-3 text-center font-bold font-mono theme-text-secondary text-xs">
                                    #{{ $inv->correlative_num }}
                                </td>
                                <td class="px-4 py-3 theme-text-secondary font-medium">
                                    {{ $inv->bar_name ?? 'Principal' }}
                                </td>
                                <td class="px-4 py-3 text-right font-black font-mono theme-text-primary text-sm">
                                    Bs. {{ number_format($inv->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-rose-500 font-bold text-xs">
                                    {{ $inv->commission_amount > 0 ? '- Bs. ' . number_format($inv->commission_amount, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-black font-mono text-[#F5B81C] text-sm">
                                    Bs. {{ number_format($inv->net_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" 
                                            onclick="deleteInvoice({{ $inv->id }})" 
                                            class="text-rose-500 hover:text-rose-400 font-bold text-xs cursor-pointer transition-colors active:scale-95 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                                        Borrar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-invoices-row">
                                <td colspan="6" class="px-4 py-10 text-center theme-text-muted font-sans">
                                    No hay facturas registradas en esta noche.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @endif

</div>

<script>
/* --- CONTROL DE BARRA (PÍLDORAS MODO IPHONE) --- */
function selectBar(bar) {
    document.getElementById('bar_name_input').value = bar;
    const bars = ['Principal', 'Subterraneo', 'Tienda'];
    bars.forEach(b => {
        const btn = document.getElementById('btn-bar-' + b);
        if (btn) {
            if (b === bar) {
                btn.className = 'py-2.5 px-2 rounded-lg text-sm sm:text-base font-black transition-all bg-[#F5B81C] text-black shadow-sm text-center truncate cursor-pointer active:scale-95';
            } else {
                btn.className = 'py-2.5 px-2 rounded-lg text-sm sm:text-base font-bold transition-all theme-text-secondary hover:theme-text-primary text-center truncate cursor-pointer active:scale-95';
            }
        }
    });
}

/* --- ELIMINACIÓN ASINCRÓNICA SIN PARPADEO --- */
async function deleteInvoice(id) {
    if (!confirm('¿Eliminar esta factura definitivamente?')) return;
    const row = document.getElementById('invoice-row-' + id);
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const res = await fetch('{{ url("/invoices") }}/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (res.ok) {
            if (row) {
                row.style.transition = 'all 0.25s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(14px)';
                setTimeout(() => {
                    row.remove();
                    window.location.reload();
                }, 220);
            } else {
                window.location.reload();
            }
        } else {
            window.location.reload();
        }
    } catch (err) {
        window.location.reload();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.focus();
    }
});
</script>
@endsection
