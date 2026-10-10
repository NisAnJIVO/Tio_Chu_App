@extends('layouts.app')

@section('title', 'Resumen Consolidado de Noches — ' . $summary['date_range'])

@section('content')
<style>
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-size: 11pt !important;
        }
        nav, aside, header, #sidebar, .no-print, button, a[href]:not(.print-include) {
            display: none !important;
        }
        .theme-card {
            background: #ffffff !important;
            border: 1px solid #000000 !important;
            color: #000000 !important;
            box-shadow: none !important;
        }
        .text-white, .text-zinc-200, .text-zinc-300, .text-zinc-400 {
            color: #000000 !important;
        }
        .text-[#F5B81C], .text-amber-400, .text-emerald-400, .text-blue-400, .text-purple-400 {
            color: #000000 !important;
            font-weight: bold !important;
        }
        .print-signature-block {
            display: flex !important;
        }
        table {
            border: 1px solid #000000 !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }
        th, td {
            border: 1px solid #000000 !important;
            padding: 5px 8px !important;
            color: #000000 !important;
        }
        th {
            background-color: #f3f4f6 !important;
            color: #000000 !important;
        }
    }
</style>

<div class="space-y-6 max-w-7xl mx-auto pb-12">

    <!-- ==========================================
         CABECERA / ACCIONES DE IMPRESIÓN
         ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-zinc-800/80 no-print">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('sessions.index') }}" class="text-xs font-mono font-bold text-[#F5B81C] hover:underline flex items-center gap-1">
                    &larr; Volver a Historial
                </a>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Consolidado Multisesión</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white font-sans">
                Resumen Consolidado de Fin de Semana / Jornadas
            </h1>
            <p class="text-xs text-zinc-400 mt-0.5">
                {{ $summary['date_range'] }} • <span class="font-bold text-white">{{ $summary['count'] }} noches</span> consolidadas
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-black text-xs font-black tracking-wider uppercase rounded-xl transition-all cursor-pointer shadow-md shadow-[#F5B81C]/10 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Imprimir Reporte Oficial</span>
            </button>
        </div>
    </div>

    <!-- Membrete exclusivo para impresión física -->
    <div class="hidden print:block text-center pb-4 mb-4 border-b-2 border-black">
        <h1 class="text-2xl font-black uppercase tracking-wider">DISCOTECA TÍO CHU</h1>
        <h2 class="text-base font-bold uppercase tracking-tight mt-1">INFORME CONSOLIDADO DE RECAUDACIÓN Y CIERRE DE CAJA</h2>
        <p class="text-xs mt-1">Período: <strong>{{ $summary['date_range'] }}</strong> ({{ $summary['count'] }} Jornadas Nocturnas)</p>
        <p class="text-[10px] text-zinc-600 mt-0.5">Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- ==========================================
         1. 4 KPIs GLOBALES (TOTALES CONSOLIDADOS)
         ========================================== -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Total Ingresos Brutos -->
        <div class="theme-card rounded-2xl p-4 sm:p-5 border border-zinc-800 bg-[#09090b]">
            <span class="text-[10px] sm:text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Total Ingresos</span>
            <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-[#F5B81C] tracking-tight">
                <span class="text-xs sm:text-sm font-sans mr-0.5">Bs.</span>{{ number_format($summary['totals']['ingresos'], 2) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1 font-mono truncate">
                Efectivo + QR + Tarjetas
            </p>
        </div>

        <!-- Total Egresos -->
        <div class="theme-card rounded-2xl p-4 sm:p-5 border border-zinc-800 bg-[#09090b]">
            <span class="text-[10px] sm:text-xs font-mono font-semibold text-rose-400 uppercase tracking-wider">Total Egresos</span>
            <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-rose-400 tracking-tight">
                <span class="text-xs sm:text-sm font-sans mr-0.5">Bs.</span>{{ number_format($summary['totals']['egresos'], 2) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1 font-mono truncate">
                Personal: Bs. {{ number_format($summary['totals']['personal'], 0) }} • Gastos: Bs. {{ number_format($summary['totals']['gastos'], 0) }}
            </p>
        </div>

        <!-- Saldo Neto en Caja -->
        <div class="theme-card rounded-2xl p-4 sm:p-5 border border-emerald-500/30 bg-[#09090b]">
            <span class="text-[10px] sm:text-xs font-mono font-semibold text-emerald-400 uppercase tracking-wider">Ganancia Neta en Caja</span>
            <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-emerald-400 tracking-tight">
                <span class="text-xs sm:text-sm font-sans mr-0.5">Bs.</span>{{ number_format($summary['totals']['neto'], 2) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1 font-mono truncate">
                Ingresos menos Egresos
            </p>
        </div>

        <!-- Total Ventas Barras -->
        <div class="theme-card rounded-2xl p-4 sm:p-5 border border-zinc-800 bg-[#09090b]">
            <span class="text-[10px] sm:text-xs font-mono font-semibold text-zinc-400 uppercase tracking-wider">Ventas de Bebidas</span>
            <div class="mt-2 text-xl sm:text-2xl font-black font-mono text-white tracking-tight">
                <span class="text-xs sm:text-sm font-sans mr-0.5">Bs.</span>{{ number_format($summary['totals']['ventas'], 2) }}
            </div>
            <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-1 font-mono truncate">
                Barras + Tienda Sueltas
            </p>
        </div>

    </div>

    <!-- ==========================================
         2. DESGLOSE POR FORMAS DE PAGO (3 TARJETAS)
         ========================================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Efectivo Consolidado -->
        <div class="theme-card rounded-2xl p-5 border border-zinc-800 bg-[#09090b] space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono">Efectivo en Caja</h3>
                </div>
                <span class="text-sm font-black font-mono text-emerald-400">
                    Bs. {{ number_format($summary['totals']['efectivo'], 2) }}
                </span>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-zinc-400">
                    <span>Barra Principal:</span>
                    <span class="font-mono text-zinc-200">Bs. {{ number_format($summary['bars']['principal']['efectivo'], 2) }}</span>
                </div>
                <div class="flex justify-between text-zinc-400">
                    <span>Barra Subterráneo:</span>
                    <span class="font-mono text-zinc-200">Bs. {{ number_format($summary['bars']['subte']['efectivo'], 2) }}</span>
                </div>
                <div class="flex justify-between text-zinc-400">
                    <span>Tienda (Sueltas):</span>
                    <span class="font-mono text-zinc-200">Bs. {{ number_format($summary['bars']['tienda']['efectivo'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Pagos QR Consolidados -->
        <div class="theme-card rounded-2xl p-5 border border-zinc-800 bg-[#09090b] space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono">Cobros QR</h3>
                </div>
                <span class="text-sm font-black font-mono text-blue-400">
                    Bs. {{ number_format($summary['totals']['qr_total'], 2) }}
                </span>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-zinc-400">
                    <span>YASTA (Banco Unión):</span>
                    <span class="font-mono text-zinc-200">Bs. {{ number_format($summary['totals']['qr_yasta'], 2) }}</span>
                </div>
                <div class="flex justify-between text-zinc-400">
                    <span>YAPE (Banco BCP):</span>
                    <span class="font-mono text-zinc-200">Bs. {{ number_format($summary['totals']['qr_yape'], 2) }}</span>
                </div>
                <div class="flex justify-between text-zinc-500 pt-1 border-t border-zinc-800/60 text-[11px]">
                    <span>Total Transferencias:</span>
                    <span class="font-mono font-bold text-blue-300">Bs. {{ number_format($summary['totals']['qr_total'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Tarjetas POS Consolidadas -->
        <div class="theme-card rounded-2xl p-5 border border-zinc-800 bg-[#09090b] space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono">Tarjetas POS</h3>
                </div>
                <span class="text-sm font-black font-mono text-purple-400">
                    Bs. {{ number_format($summary['totals']['card_gross'], 2) }}
                </span>
            </div>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-zinc-400">
                    <span>Monto Bruto Facturado:</span>
                    <span class="font-mono text-zinc-200">Bs. {{ number_format($summary['totals']['card_gross'], 2) }}</span>
                </div>
                <div class="flex justify-between text-rose-400/80">
                    <span>Comisión POS Descontada:</span>
                    <span class="font-mono">- Bs. {{ number_format($summary['totals']['card_commission'], 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-400 pt-1 border-t border-zinc-800/60 font-bold">
                    <span>Neto Acreditado en Banco:</span>
                    <span class="font-mono">Bs. {{ number_format($summary['totals']['card_net'], 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ==========================================
         3. DESGLOSE POR PUNTOS DE VENTA (BARRAS & TIENDA)
         ========================================= -->
    <div class="theme-card rounded-2xl border border-zinc-800 bg-[#09090b] overflow-hidden">
        <div class="px-5 py-3.5 bg-zinc-950/60 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-mono">
                Rendimiento por Punto de Venta (Barras & Tienda)
            </h3>
            <span class="text-xs font-mono text-zinc-500">Período: {{ $summary['date_range'] }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-zinc-950/40 border-b border-zinc-800 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3">Punto de Venta</th>
                        <th class="px-5 py-3 text-right">Ventas Totales</th>
                        <th class="px-5 py-3 text-right">Efectivo Cobrado</th>
                        <th class="px-5 py-3 text-right">Cobros QR</th>
                        <th class="px-5 py-3 text-right">Cobros Tarjeta POS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 font-mono">
                    <tr class="hover:bg-zinc-900/40">
                        <td class="px-5 py-3.5 font-bold font-sans text-white">
                            <span>Barra Principal</span>
                            <span class="text-[10px] text-zinc-500 block font-normal">Piso Principal</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-[#F5B81C]">Bs. {{ number_format($summary['bars']['principal']['ventas'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-400">Bs. {{ number_format($summary['bars']['principal']['efectivo'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-blue-400">Bs. {{ number_format($summary['bars']['principal']['qr'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-purple-400">Bs. {{ number_format($summary['bars']['principal']['card'], 2) }}</td>
                    </tr>
                    <tr class="hover:bg-zinc-900/40">
                        <td class="px-5 py-3.5 font-bold font-sans text-white">
                            <span>Barra Subterráneo</span>
                            <span class="text-[10px] text-zinc-500 block font-normal">Subterráneo</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-[#F5B81C]">Bs. {{ number_format($summary['bars']['subte']['ventas'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-400">Bs. {{ number_format($summary['bars']['subte']['efectivo'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-blue-400">Bs. {{ number_format($summary['bars']['subte']['qr'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-purple-400">Bs. {{ number_format($summary['bars']['subte']['card'], 2) }}</td>
                    </tr>
                    <tr class="hover:bg-zinc-900/40">
                        <td class="px-5 py-3.5 font-bold font-sans text-white">
                            <span>Tienda Oficial</span>
                            <span class="text-[10px] text-zinc-500 block font-normal">{{ $summary['bars']['tienda']['store_units'] }} unidades sueltas despachadas</span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-[#F5B81C]">Bs. {{ number_format($summary['bars']['tienda']['ventas'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-400">Bs. {{ number_format($summary['bars']['tienda']['efectivo'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-blue-400">Bs. {{ number_format($summary['bars']['tienda']['qr'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-purple-400">Bs. {{ number_format($summary['bars']['tienda']['card'], 2) }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-zinc-950 font-bold border-t border-zinc-800 text-xs font-mono">
                    <tr>
                        <td class="px-5 py-3.5 uppercase tracking-wider text-zinc-400 font-sans">Total Puntos de Venta:</td>
                        <td class="px-5 py-3.5 text-right text-[#F5B81C]">Bs. {{ number_format($summary['totals']['ventas'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-emerald-400">Bs. {{ number_format($summary['totals']['efectivo'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-blue-400">Bs. {{ number_format($summary['totals']['qr_total'], 2) }}</td>
                        <td class="px-5 py-3.5 text-right text-purple-400">Bs. {{ number_format($summary['totals']['card_gross'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ==========================================
         4. TABLA COMPARATIVA NOCHE POR NOCHE
         ========================================== -->
    <div class="theme-card rounded-2xl border border-zinc-800 bg-[#09090b] overflow-hidden">
        <div class="px-5 py-3.5 bg-zinc-950/60 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-xs font-bold text-zinc-200 uppercase tracking-wider font-mono">
                Detalle Comparativo Noche a Noche
            </h3>
            <span class="text-xs font-mono text-zinc-500">Desglose por Jornada</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-zinc-950/40 border-b border-zinc-800 text-zinc-400 font-mono uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Día</th>
                        <th class="px-4 py-3 text-right">Ventas</th>
                        <th class="px-4 py-3 text-right">Efectivo</th>
                        <th class="px-4 py-3 text-right">QR</th>
                        <th class="px-4 py-3 text-right">POS Bruto</th>
                        <th class="px-4 py-3 text-right">Personal</th>
                        <th class="px-4 py-3 text-right">Gastos</th>
                        <th class="px-4 py-3 text-right">Ingresos</th>
                        <th class="px-4 py-3 text-right">Egresos</th>
                        <th class="px-4 py-3 text-right">Neto Caja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 font-mono">
                    @foreach($summary['nights'] as $n)
                        <tr class="hover:bg-zinc-900/40">
                            <td class="px-4 py-3.5 font-bold text-white">{{ $n['date'] }}</td>
                            <td class="px-4 py-3.5 text-zinc-300 font-sans font-semibold">{{ $n['day_name'] }}</td>
                            <td class="px-4 py-3.5 text-right font-bold text-[#F5B81C]">Bs. {{ number_format($n['ventas'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-emerald-400">Bs. {{ number_format($n['efectivo'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-blue-400">Bs. {{ number_format($n['qr'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-purple-400">Bs. {{ number_format($n['tarjeta_bruto'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-rose-400/80">Bs. {{ number_format($n['personal'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-rose-400/80">Bs. {{ number_format($n['gastos'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-zinc-200">Bs. {{ number_format($n['ingresos'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right text-rose-400">Bs. {{ number_format($n['egresos'], 2) }}</td>
                            <td class="px-4 py-3.5 text-right font-bold text-emerald-400 text-sm">Bs. {{ number_format($n['neto'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-zinc-950 font-bold border-t border-zinc-800 text-xs font-mono">
                    <tr>
                        <td colspan="2" class="px-4 py-3.5 uppercase tracking-wider text-zinc-400 font-sans">TOTAL CONSOLIDADO:</td>
                        <td class="px-4 py-3.5 text-right text-[#F5B81C]">Bs. {{ number_format($summary['totals']['ventas'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-emerald-400">Bs. {{ number_format($summary['totals']['efectivo'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-blue-400">Bs. {{ number_format($summary['totals']['qr_total'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-purple-400">Bs. {{ number_format($summary['totals']['card_gross'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-rose-400">Bs. {{ number_format($summary['totals']['personal'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-rose-400">Bs. {{ number_format($summary['totals']['gastos'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-zinc-200">Bs. {{ number_format($summary['totals']['ingresos'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-rose-400">Bs. {{ number_format($summary['totals']['egresos'], 2) }}</td>
                        <td class="px-4 py-3.5 text-right text-emerald-400 text-sm font-black">Bs. {{ number_format($summary['totals']['neto'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ==========================================
         5. BLOQUE DE FIRMAS OFICIALES (IMPRESIÓN)
         ========================================== -->
    <div class="print-signature-block pt-16 pb-8 border-t border-zinc-800 mt-12 grid grid-cols-2 gap-12 text-center text-xs">
        <div>
            <div class="w-56 mx-auto border-b border-zinc-600 mb-2"></div>
            <p class="font-bold text-white uppercase">Cajero / Administrador</p>
            <p class="text-zinc-500 text-[10px]">Responsable de Cierre de Caja</p>
        </div>
        <div>
            <div class="w-56 mx-auto border-b border-zinc-600 mb-2"></div>
            <p class="font-bold text-white uppercase">Don Ludo</p>
            <p class="text-zinc-500 text-[10px]">Gerencia General — Discoteca Tío Chu</p>
        </div>
    </div>

</div>
@endsection
