@extends('layouts.app')

@section('title', 'Resumen Consolidado — ' . $summary['date_range'])

@section('content')
@php
    $totals = $summary['totals'] ?? [];
    $nights = $summary['nights'] ?? [];
@endphp

<style>
    .excel-sheet {
        background-color: #ffffff;
        color: #0f172a;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .excel-night-card {
        border: 1.5px solid #0f172a;
        background-color: #ffffff;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .excel-header-top {
        background-color: #fef08a;
        color: #0f172a;
        font-weight: 800;
        text-align: center;
        border-bottom: 1px solid #0f172a;
    }

    .excel-header-day {
        background-color: #f59e0b;
        color: #0f172a;
        font-weight: 800;
        text-align: center;
        border-bottom: 1.5px solid #0f172a;
    }

    .excel-section-title {
        background-color: #e2e8f0;
        color: #0f172a;
        font-weight: 800;
        text-transform: uppercase;
        border-bottom: 1px solid #475569;
    }

    .excel-total-label {
        background-color: #fef3c7;
        color: #0f172a;
        font-weight: 800;
    }

    .excel-total-val {
        background-color: #ffedd5;
        color: #0f172a;
        font-weight: 800;
    }

    .excel-grand-label {
        background-color: #f87171;
        color: #0f172a;
        font-weight: 900;
    }

    .excel-grand-val {
        background-color: #0f172a;
        color: #ffffff !important;
        font-weight: 900;
    }

    @media print {
        @page {
            size: letter portrait;
            margin: 10mm 12mm;
        }

        html, body {
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif !important;
            font-size: 8.5pt !important;
            height: auto !important;
            min-height: 0 !important;
            overflow: visible !important;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        #main-sidebar, aside, nav, header, footer, .no-print, #flash-success-toast, button, a[href] {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        main {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            background: #ffffff !important;
        }

        .excel-sheet {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .excel-nights-grid {
            display: grid !important;
            grid-template-columns: repeat({{ count($nights) > 1 ? 2 : 1 }}, minmax(0, 1fr)) !important;
            gap: 10px !important;
        }

        .avoid-break {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    }
</style>

<div class="space-y-4 max-w-6xl mx-auto pb-14">

    <!-- Barra de acciones superior (no imprimible) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 rounded-xl bg-white border border-slate-200 shadow-sm text-slate-900">
        <div class="flex items-center gap-3">
            <a href="{{ route('sessions.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold border border-slate-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                <span>Volver a Historial</span>
            </a>
            <div>
                <h2 class="text-sm font-black text-slate-900 tracking-tight">Resumen Consolidado</h2>
                <p class="text-[11px] text-slate-500 font-mono">
                    {{ $summary['date_range'] }} • {{ $summary['count'] }} {{ $summary['count'] === 1 ? 'noche' : 'noches' }}
                </p>
            </div>
        </div>

        <button type="button" onclick="window.print()"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#F5B81C] hover:bg-[#e5ac18] text-slate-950 text-xs font-black tracking-wider uppercase rounded-lg transition-all cursor-pointer shadow-sm border border-amber-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Imprimir Resumen</span>
        </button>
    </div>

    <!-- Hoja Blanca Limpia (Estilo Excel Don Ludo) -->
    <div class="excel-sheet p-5 sm:p-8 rounded-2xl shadow-lg border border-slate-200">

        <!-- Cabecera Compacta -->
        <div class="flex items-end justify-between border-b-2 border-slate-900 pb-2.5 mb-5">
            <div>
                <h1 class="text-lg sm:text-xl font-black uppercase tracking-tight text-slate-900">
                    RESUMEN CONSOLIDADO — TÍO CHU
                </h1>
                <p class="text-xs font-bold font-mono text-slate-600 mt-0.5">
                    {{ $summary['date_range'] }} • {{ $summary['count'] }} {{ $summary['count'] === 1 ? 'noche' : 'noches' }}
                </p>
            </div>
            <div class="text-right font-mono text-[11px] text-slate-500">
                <span class="block font-bold text-slate-800">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
                <span>Moneda: Bs.</span>
            </div>
        </div>

        <!-- Bloques por Noche (Formato Excel "RESUMEN CIERRE TIO CHU") -->
        @if(count($nights) > 0)
            <div class="excel-nights-grid grid grid-cols-1 {{ count($nights) > 1 ? 'lg:grid-cols-2' : '' }} gap-4 mb-6">
                @foreach($nights as $n)
                    @php
                        $p = $n['bars']['principal'] ?? ['card' => 0, 'qr' => 0, 'personal' => 0, 'efectivo' => 0, 'total' => 0];
                        $s = $n['bars']['subte'] ?? ['card' => 0, 'qr' => 0, 'efectivo' => 0, 'total' => 0];
                        $t = $n['bars']['tienda'] ?? ['card' => 0, 'qr' => 0, 'efectivo' => 0, 'total' => 0];
                    @endphp

                    <div class="excel-night-card text-[11px]">
                        <div class="excel-header-top py-1 text-[11px] tracking-wide uppercase">
                            RESUMEN CIERRE &ldquo;TÍO CHU&rdquo;
                        </div>
                        <div class="excel-header-day py-1 text-xs uppercase font-mono">
                            {{ strtoupper($n['day_name']) }}: {{ $n['date'] }}
                        </div>

                        <!-- Fila superior: Barra Subterráneo | Tienda Oficial -->
                        <div class="grid grid-cols-2 border-b border-slate-800">
                            <!-- Barra Subterráneo -->
                            <div class="border-r border-slate-800">
                                <div class="excel-section-title px-2 py-1 text-center text-[10px]">Barra Subterráneo</div>
                                <table class="w-full font-mono text-[11px] border-collapse">
                                    <tbody>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Tarjetero</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($s['card'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">QRs</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($s['qr'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Efectivo</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($s['efectivo'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="excel-total-label px-2 py-1 font-sans uppercase text-[10px]">Total:</td>
                                            <td class="excel-total-val px-2 py-1 text-right">{{ number_format($s['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tienda Oficial -->
                            <div>
                                <div class="excel-section-title px-2 py-1 text-center text-[10px]">Tienda Oficial</div>
                                <table class="w-full font-mono text-[11px] border-collapse">
                                    <tbody>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Tarjetero</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($t['card'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">QRs</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($t['qr'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Efectivo</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($t['efectivo'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="excel-total-label px-2 py-1 font-sans uppercase text-[10px]">Total:</td>
                                            <td class="excel-total-val px-2 py-1 text-right">{{ number_format($t['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Fila inferior: Barra Principal | Resumen Noche & Cerveza -->
                        <div class="grid grid-cols-2">
                            <!-- Barra Principal -->
                            <div class="border-r border-slate-800">
                                <div class="excel-section-title px-2 py-1 text-center text-[10px]">Barra Principal</div>
                                <table class="w-full font-mono text-[11px] border-collapse">
                                    <tbody>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Tarjeteros</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($p['card'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">QRs</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($p['qr'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Personal</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($p['personal'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Efectivo</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($p['efectivo'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="excel-total-label px-2 py-1 font-sans uppercase text-[10px]">Total:</td>
                                            <td class="excel-total-val px-2 py-1 text-right">{{ number_format($p['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Totales de Noche -->
                            <div class="flex flex-col justify-between bg-slate-50">
                                <div class="p-2 border-b border-slate-300">
                                    <div class="flex items-center justify-between border border-slate-800 bg-amber-300 px-2 py-1">
                                        <span class="font-sans font-black text-[10px] uppercase text-slate-900">Total Efectivo:</span>
                                        <span class="font-mono font-black text-slate-900">{{ number_format($n['efectivo'], 2) }}</span>
                                    </div>
                                </div>

                                <table class="w-full font-mono text-[11px] border-collapse">
                                    <tbody>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Principal</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($p['total'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Subterráneo</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($s['total'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-300">
                                            <td class="px-2 py-0.5 font-sans font-semibold text-slate-700">Tienda</td>
                                            <td class="px-2 py-0.5 text-right text-slate-900">{{ number_format($t['total'], 2) }}</td>
                                        </tr>
                                        <tr class="border-b border-slate-800">
                                            <td class="excel-grand-label px-2 py-1 font-sans uppercase text-[10px]">TOTAL:</td>
                                            <td class="excel-grand-val px-2 py-1 text-right">{{ number_format($n['ventas'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-2 py-1 font-sans font-extrabold uppercase text-[10px] text-slate-800 bg-white">Cerveza:</td>
                                            <td class="px-2 py-1 text-right font-extrabold bg-orange-100 text-slate-900">{{ number_format($n['cerveza'] ?? 0, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Totales Consolidados & Bancos (Limpio y Directo) -->
        <div class="avoid-break grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <!-- Cuadro 1: Totales de Caja -->
            <div class="border border-slate-900">
                <div class="bg-slate-900 text-white px-3 py-1.5 font-black uppercase text-[11px] tracking-wider">
                    Totales Generales ({{ $summary['count'] }} {{ $summary['count'] === 1 ? 'noche' : 'noches' }})
                </div>
                <table class="w-full font-mono text-xs border-collapse">
                    <tbody>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">Total Ventas (Barras + Tienda)</td>
                            <td class="px-3 py-1.5 text-right font-bold text-slate-900">Bs. {{ number_format($totals['ventas'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">Total Ingresos</td>
                            <td class="px-3 py-1.5 text-right font-bold text-slate-900">Bs. {{ number_format($totals['ingresos'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">Total Efectivo en Mano</td>
                            <td class="px-3 py-1.5 text-right font-bold text-emerald-700">Bs. {{ number_format($totals['efectivo'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">Personal Pagado</td>
                            <td class="px-3 py-1.5 text-right font-bold text-rose-700">- Bs. {{ number_format($totals['personal'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">Cerveza y Otros Gastos</td>
                            <td class="px-3 py-1.5 text-right font-bold text-rose-700">- Bs. {{ number_format(($totals['beer_total'] ?? 0) + ($totals['gastos'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="bg-emerald-50 border-t-2 border-slate-900">
                            <td class="px-3 py-2 font-sans font-black uppercase text-[11px] text-slate-900">Ganancia Neta en Caja</td>
                            <td class="px-3 py-2 text-right font-black text-emerald-800">Bs. {{ number_format($totals['neto'] ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cuadro 2: Bancos (QR y Tarjetero POS con Descuento 1.30%) -->
            <div class="border border-slate-900">
                <div class="bg-slate-900 text-white px-3 py-1.5 font-black uppercase text-[11px] tracking-wider">
                    Bancos — QRs y Tarjetero POS
                </div>
                <table class="w-full font-mono text-xs border-collapse">
                    <tbody>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">QR Banco Unión (YASTA)</td>
                            <td class="px-3 py-1.5 text-right font-semibold text-slate-900">Bs. {{ number_format($totals['qr_yasta'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">QR Banco BCP (YAPE)</td>
                            <td class="px-3 py-1.5 text-right font-semibold text-slate-900">Bs. {{ number_format($totals['qr_yape'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-rose-700">(-) Cerveza pagada por QR</td>
                            <td class="px-3 py-1.5 text-right font-semibold text-rose-700">- Bs. {{ number_format($totals['beer_qr'] ?? ($totals['qr_spent'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-800 bg-blue-50">
                            <td class="px-3 py-1.5 font-sans font-black text-blue-900">QR Líquido en Banco</td>
                            <td class="px-3 py-1.5 text-right font-black text-blue-900">Bs. {{ number_format($totals['qr_remaining'] ?? ($totals['qr_total'] ?? 0), 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-slate-700">Tarjetero POS (Bruto)</td>
                            <td class="px-3 py-1.5 text-right font-semibold text-slate-900">Bs. {{ number_format($totals['card_gross'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="px-3 py-1.5 font-sans font-semibold text-rose-700">(-) Descuento POS (1.30%)</td>
                            <td class="px-3 py-1.5 text-right font-semibold text-rose-700">- Bs. {{ number_format($totals['card_commission'] ?? 0, 2) }}</td>
                        </tr>
                        <tr class="bg-amber-100 border-t-2 border-slate-900">
                            <td class="px-3 py-2 font-sans font-black uppercase text-[11px] text-slate-900">Tarjetero Neto a Cobrar</td>
                            <td class="px-3 py-2 text-right font-black text-slate-900">Bs. {{ number_format($totals['card_net'] ?? 0, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@if(request()->has('auto_print'))
<script>
    window.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            window.print();
        }, 400);
    });
</script>
@endif
@endsection
