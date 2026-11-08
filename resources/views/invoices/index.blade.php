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
                Control de facturas emitidas y cobros pasados por máquina de tarjeta
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
            <p class="text-xs theme-text-secondary mt-1 mb-4">Apertura una noche para registrar facturas y cobros con tarjeta.</p>
            <a href="{{ route('sessions.create') }}" class="px-4 py-2 rounded-xl bg-[#F5B81C] text-black font-bold text-xs hover:bg-[#e5ac18] transition-all inline-block">
                + Aperturar Nueva Noche
            </a>
        </div>
    @else

        <!-- ========================================================
             2. MÉTRICAS CLARAS Y DIRECTAS PARA DON LUDO (CARDS NOTORIAS)
             ======================================================== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
            
            <!-- 1. Pasado por Tarjeta (Bruto) -->
            <div class="notoria-card p-3.5 sm:p-5 shadow-sm">
                <span class="text-[11px] sm:text-xs font-black theme-text-secondary uppercase tracking-wider block truncate">Pasado por Tarjeta</span>
                <div class="mt-1 sm:mt-2 text-lg sm:text-2xl font-black font-mono theme-text-primary tracking-tight truncate">
                    <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjeta, 2) }}
                </div>
                <p class="text-[10px] sm:text-[11px] theme-text-muted mt-0.5 sm:mt-1 truncate">Vouchers pasados (Bruto)</p>
            </div>

            <!-- 2. Comisión del Banco -->
            <div class="notoria-card p-3.5 sm:p-5 shadow-sm">
                <span class="text-[11px] sm:text-xs font-black theme-text-secondary uppercase tracking-wider block truncate">
                    Comisión Banco ({{ number_format(($session->pos_commission_rate ?? 0.035) * 100, 1) }}%)
                </span>
                <div class="mt-1 sm:mt-2 text-lg sm:text-2xl font-black font-mono text-rose-500 tracking-tight truncate">
                    - <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjetaComision, 2) }}
                </div>
                <p class="text-[10px] sm:text-[11px] theme-text-muted mt-0.5 sm:mt-1 truncate">Retención banco</p>
            </div>

            <!-- 3. Plata que Entra al Banco (Neto) -->
            <div class="notoria-card p-3.5 sm:p-5 shadow-sm">
                <span class="text-[11px] sm:text-xs font-black theme-text-secondary uppercase tracking-wider block truncate">Neto al Banco</span>
                <div class="mt-1 sm:mt-2 text-lg sm:text-2xl font-black font-mono text-[#F5B81C] tracking-tight truncate">
                    <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalTarjetaNeto, 2) }}
                </div>
                <p class="text-[10px] sm:text-[11px] theme-text-muted mt-0.5 sm:mt-1 truncate">Dinero neto acreditado</p>
            </div>

            <!-- 4. Facturas en Efectivo -->
            <div class="notoria-card p-3.5 sm:p-5 shadow-sm">
                <span class="text-[11px] sm:text-xs font-black theme-text-secondary uppercase tracking-wider block truncate">Facturas en Efectivo</span>
                <div class="mt-1 sm:mt-2 text-lg sm:text-2xl font-black font-mono theme-text-primary tracking-tight truncate">
                    <span class="text-xs sm:text-sm mr-0.5 font-sans font-bold">Bs.</span>{{ number_format($totalEfectivo, 2) }}
                </div>
                <p class="text-[10px] sm:text-[11px] theme-text-muted mt-0.5 sm:mt-1 truncate">Recibido en mano</p>
            </div>

        </div>

        <!-- ========================================================
             3. CARD PROTAGONISTA: REGISTRAR VOUCHER / FACTURA POS
             Diseño dominante de Workstation POS de alto impacto
             ======================================================== -->
        <div class="notoria-card rounded-2xl shadow-lg border-2 border-[#F5B81C]/40 p-5 sm:p-6 space-y-5 transition-all">
            
            <!-- Cabecera de la Card Protagonista -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b notoria-border gap-3">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-[#F5B81C]/15 border border-[#F5B81C]/40 text-[#F5B81C] flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                            <path d="M7 15h2"/>
                            <path d="M14 15h3"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] uppercase tracking-wider font-mono font-black text-[#F5B81C] bg-[#F5B81C]/10 px-2 py-0.5 rounded border border-[#F5B81C]/25">
                                Operación Directa
                            </span>
                            <span class="text-xs theme-text-muted font-mono">• Punto de Cobro</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-black theme-text-primary uppercase tracking-wider font-sans mt-0.5">
                            Registrar Voucher / Factura POS
                        </h2>
                    </div>
                </div>

                <!-- Insignias Destacadas del Registro -->
                <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                    <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#F5B81C] text-black shadow-sm font-sans" title="Número autoincremental automático">
                        <span class="text-[11px] font-black uppercase tracking-wider">Siguiente N°</span>
                        <span class="text-base font-mono font-black">#{{ $nextCorrelative }}</span>
                    </div>
                    <div class="px-3 py-1.5 rounded-xl notoria-subtile text-xs font-mono font-bold theme-text-secondary" title="Tasa de retención por tarjeta POS configurada para esta noche">
                        Comisión: <span class="theme-text-primary font-black">{{ number_format(($session->pos_commission_rate ?? 0.035) * 100, 1) }}%</span>
                    </div>
                </div>
            </div>
            
            <!-- Formulario Principal -->
            <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form" class="space-y-4 {{ !$session->isOpen() ? 'pointer-events-none opacity-50' : '' }}">
                @csrf
                <input type="hidden" name="night_session_id" value="{{ $session->id }}">
                <input type="hidden" name="correlative_num" value="{{ $nextCorrelative }}">
                <input type="hidden" name="payment_method" id="payment_method_input" value="tarjeta">
                <input type="hidden" name="bar_name" id="bar_name_input" value="Principal">

                <!-- Grid de Controles de Entrada -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 sm:gap-4 items-end">
                    
                    <!-- 1. Punto de Venta / Barra (iOS Switch) -->
                    <div class="md:col-span-4 space-y-1.5">
                        <label class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                            Punto de Venta / Barra
                        </label>
                        <div class="grid grid-cols-3 p-1.5 rounded-xl notoria-subtile gap-1">
                            <button type="button"
                                    id="btn-bar-Principal"
                                    onclick="selectBar('Principal')"
                                    class="py-2.5 px-2 rounded-lg text-xs font-black transition-all cursor-pointer bg-[#F5B81C] text-black shadow-sm text-center truncate active:scale-95">
                                Principal
                            </button>
                            <button type="button"
                                    id="btn-bar-Subterraneo"
                                    onclick="selectBar('Subterraneo')"
                                    class="py-2.5 px-2 rounded-lg text-xs font-bold transition-all cursor-pointer theme-text-secondary hover:theme-text-primary text-center truncate active:scale-95">
                                Subterráneo
                            </button>
                            <button type="button"
                                    id="btn-bar-Tienda"
                                    onclick="selectBar('Tienda')"
                                    class="py-2.5 px-2 rounded-lg text-xs font-bold transition-all cursor-pointer theme-text-secondary hover:theme-text-primary text-center truncate active:scale-95">
                                Tienda
                            </button>
                        </div>
                    </div>

                    <!-- 2. Método de Pago (Tarjeta POS vs Efectivo) -->
                    <div class="md:col-span-3 space-y-1.5">
                        <label class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                            Método de Pago
                        </label>
                        <div class="grid grid-cols-2 p-1.5 rounded-xl notoria-subtile gap-1">
                            <button type="button"
                                    id="btn-method-tarjeta"
                                    onclick="selectPaymentMethod('tarjeta')"
                                    class="py-2.5 px-2 rounded-lg text-xs font-black transition-all bg-[#F5B81C] text-black shadow-sm flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                    <line x1="2" y1="10" x2="22" y2="10"/>
                                </svg>
                                <span>Tarjeta</span>
                            </button>
                            <button type="button"
                                    id="btn-method-efectivo"
                                    onclick="selectPaymentMethod('efectivo')"
                                    class="py-2.5 px-2 rounded-lg text-xs font-bold transition-all theme-text-secondary hover:theme-text-primary flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Efectivo</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Monto Cobrado (Display POS Grande y Destacado) -->
                    <div class="md:col-span-5 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="amount" class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                                Monto Cobrado (Bs.)
                            </label>
                            <span class="text-[11px] font-mono theme-text-muted">Valor bruto voucher</span>
                        </div>
                        <div class="flex items-center rounded-xl notoria-subtile px-4 py-2 border notoria-border focus-within:border-[#F5B81C] focus-within:ring-2 focus-within:ring-[#F5B81C]/25 transition-all shadow-inner">
                            <span class="text-xl font-mono font-black text-[#F5B81C] mr-2 shrink-0">Bs.</span>
                            <input type="number" 
                                   step="0.5" 
                                   inputmode="decimal" 
                                   name="amount" 
                                   id="amount" 
                                   required 
                                   placeholder="0.00" 
                                   autofocus
                                   oninput="updateLiveBreakdown()"
                                   class="w-full text-2xl font-mono font-black bg-transparent border-0 theme-text-primary placeholder-zinc-400 focus:outline-none p-0">
                        </div>
                    </div>

                    <!-- 4. Notas / N° Voucher -->
                    <div class="md:col-span-8 space-y-1.5">
                        <label for="notes" class="block text-xs font-black uppercase tracking-wider theme-text-secondary">
                            N° Voucher / Detalle / Notas (Opcional)
                        </label>
                        <input type="text" 
                               name="notes" 
                               id="notes" 
                               placeholder="Ej: Voucher #1042, Tarjeta Visa BCP, Lote 3..."
                               class="w-full text-xs sm:text-sm rounded-xl px-4 py-3 notoria-input focus:outline-none focus:border-[#F5B81C]">
                    </div>

                    <!-- 5. Botón de Registro Prominente -->
                    <div class="md:col-span-4">
                        <button type="submit" 
                                class="w-full py-3.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-black font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl active:scale-95 transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Guardar Factura (#{{ $nextCorrelative }})</span>
                        </button>
                    </div>

                </div>

                <!-- Barra de Cálculo en Vivo (Auditoría Inmediata) -->
                <div class="mt-3 p-3 sm:px-4 sm:py-3 rounded-xl notoria-subtile border notoria-border flex flex-wrap items-center justify-between gap-3 text-xs font-mono">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#F5B81C] animate-pulse"></span>
                        <span class="theme-text-muted font-sans font-bold">Cálculo en vivo de acreditación:</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                        <div>
                            <span class="theme-text-muted text-[11px] block">Monto Bruto:</span>
                            <span id="live-bruto" class="font-bold theme-text-primary text-sm">Bs. 0.00</span>
                        </div>
                        <div>
                            <span class="theme-text-muted text-[11px] block">Retención Banco:</span>
                            <span id="live-comision" class="font-bold text-rose-500 text-sm">- Bs. 0.00</span>
                        </div>
                        <div class="pl-2 border-l notoria-border">
                            <span class="theme-text-muted text-[11px] block font-sans">Neto Estimado al Banco:</span>
                            <span id="live-neto" class="font-black text-[#F5B81C] text-sm">Bs. 0.00</span>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <!-- ========================================================
             4. TABLA DE HISTORIAL DE FACTURAS EMITIDAS
             Ubicada abajo en ancho completo para consulta y auditoría limpia
             ======================================================== -->
        <div class="notoria-card rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 notoria-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b notoria-border">
                <div class="flex items-center gap-3">
                    <h3 class="text-xs font-black uppercase tracking-wider text-white font-mono">
                        Historial de Facturas Emitidas ({{ $invoices->count() }})
                    </h3>
                </div>
                <div class="flex items-center gap-3 text-xs font-mono">
                    <span class="theme-text-muted text-zinc-300">Total Bruto: <strong class="text-white font-black">Bs. {{ number_format($totalGeneral, 2) }}</strong></span>
                    <span class="text-zinc-400">•</span>
                    <span class="text-[#F5B81C] font-bold">Neto Banco: <strong class="font-black">Bs. {{ number_format($totalTarjetaNeto, 2) }}</strong></span>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[560px]">
                <table class="w-full min-w-[700px] text-xs text-left border-collapse">
                    <thead class="bg-slate-950 text-white border-b notoria-border uppercase text-[11px] tracking-wider sticky top-0 font-mono font-bold z-10">
                        <tr>
                            <th class="px-4 py-3 w-14 text-center">N°</th>
                            <th class="px-4 py-3">Forma Pago</th>
                            <th class="px-4 py-3">Barra</th>
                            <th class="px-4 py-3 text-right">Monto Bruto</th>
                            <th class="px-4 py-3 text-right">Comisión Banco</th>
                            <th class="px-4 py-3 text-right">Neto al Banco</th>
                            <th class="px-4 py-3">N° Voucher / Detalle</th>
                            <th class="px-4 py-3 text-right w-20">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y notoria-border font-sans" id="invoices-table-body">
                        @forelse($invoices as $inv)
                            <tr id="invoice-row-{{ $inv->id }}" class="hover:bg-slate-500/10 transition-colors">
                                <td class="px-4 py-3 text-center font-bold font-mono theme-text-secondary text-xs">
                                    #{{ $inv->correlative_num }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($inv->payment_method === 'tarjeta')
                                        <span class="inline-flex items-center gap-1.5 text-[#F5B81C] font-bold text-xs font-mono">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                                <line x1="2" y1="10" x2="22" y2="10"/>
                                            </svg>
                                            <span>Tarjeta POS</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 theme-text-secondary font-semibold text-xs font-mono">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <span>Efectivo</span>
                                        </span>
                                    @endif
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
                                <td class="px-4 py-3 theme-text-muted text-xs truncate max-w-[200px]">{{ $inv->notes ?? '—' }}</td>
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
                                <td colspan="8" class="px-4 py-10 text-center theme-text-muted font-sans">
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
const posCommissionRate = {{ ($session && $session->pos_commission_rate !== null) ? (float)$session->pos_commission_rate : 0.035 }};

/* --- CONTROL DE BARRA (PÍLDORAS MODO IPHONE) --- */
function selectBar(bar) {
    document.getElementById('bar_name_input').value = bar;
    const bars = ['Principal', 'Subterraneo', 'Tienda'];
    bars.forEach(b => {
        const btn = document.getElementById('btn-bar-' + b);
        if (btn) {
            if (b === bar) {
                btn.className = 'py-2.5 px-2 rounded-lg text-xs font-black transition-all bg-[#F5B81C] text-black shadow-sm text-center truncate cursor-pointer active:scale-95';
            } else {
                btn.className = 'py-2.5 px-2 rounded-lg text-xs font-bold transition-all theme-text-secondary hover:theme-text-primary text-center truncate cursor-pointer active:scale-95';
            }
        }
    });
}

/* --- CONTROL DE MÉTODO DE PAGO --- */
function selectPaymentMethod(method) {
    document.getElementById('payment_method_input').value = method;
    const isCard = method === 'tarjeta';
    const btnCard = document.getElementById('btn-method-tarjeta');
    const btnCash = document.getElementById('btn-method-efectivo');

    if (btnCard && btnCash) {
        if (isCard) {
            btnCard.className = 'py-2.5 px-2 rounded-lg text-xs font-black transition-all bg-[#F5B81C] text-black shadow-sm flex items-center justify-center gap-1.5 cursor-pointer active:scale-95';
            btnCash.className = 'py-2.5 px-2 rounded-lg text-xs font-bold transition-all theme-text-secondary hover:theme-text-primary flex items-center justify-center gap-1.5 cursor-pointer active:scale-95';
        } else {
            btnCash.className = 'py-2.5 px-2 rounded-lg text-xs font-black transition-all bg-[#F5B81C] text-black shadow-sm flex items-center justify-center gap-1.5 cursor-pointer active:scale-95';
            btnCard.className = 'py-2.5 px-2 rounded-lg text-xs font-bold transition-all theme-text-secondary hover:theme-text-primary flex items-center justify-center gap-1.5 cursor-pointer active:scale-95';
        }
    }
    updateLiveBreakdown();
}

/* --- CÁLCULO EN VIVO DE COMISIÓN & NETO --- */
function updateLiveBreakdown() {
    const amountInput = document.getElementById('amount');
    const methodInput = document.getElementById('payment_method_input');
    const liveBruto = document.getElementById('live-bruto');
    const liveComision = document.getElementById('live-comision');
    const liveNeto = document.getElementById('live-neto');

    if (!amountInput || !liveBruto) return;

    const amount = parseFloat(amountInput.value) || 0;
    const isCard = (methodInput ? methodInput.value : 'tarjeta') === 'tarjeta';

    liveBruto.textContent = 'Bs. ' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    if (isCard) {
        const comision = amount * posCommissionRate;
        const neto = amount - comision;
        if (liveComision) liveComision.textContent = '- Bs. ' + comision.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (liveNeto) liveNeto.textContent = 'Bs. ' + neto.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        if (liveComision) liveComision.textContent = '— (Sin retención)';
        if (liveNeto) liveNeto.textContent = 'Bs. ' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
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
    updateLiveBreakdown();
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.focus();
    }
});
</script>
@endsection
