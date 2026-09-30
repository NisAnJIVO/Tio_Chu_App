@extends('layouts.app')

@section('title', 'Inventario de Bebidas & Bodega')

@section('content')

@php
    $totalBottles   = $products->sum('stock_warehouse');
    $totalValuation = $products->sum(fn($p) => $p->stock_warehouse * $p->sale_price);
    $lowStockCount  = $products->filter(fn($p) => $p->stock_warehouse <= 10 && $p->is_active)->count();

    function getSubcat(string $name, string $cat): string {
        $n = strtolower($name);
        if (str_contains($n, 'singani'))  return 'Singani';
        if (str_contains($n, 'whisky') || str_contains($n, 'whiskey')) return 'Whisky';
        if (str_contains($n, 'ron')  || str_contains($n, 'fernet'))    return 'Ron';
        if (str_contains($n, 'gin')  || str_contains($n, 'tanqueray') || str_contains($n, 'beefeater')) return 'Gin';
        if (str_contains($n, 'tequila') || str_contains($n, 'jager'))  return 'Tequila';
        if ($cat === 'Mixers' || str_contains($n,'coca') || str_contains($n,'ginger') || str_contains($n,'agua') || str_contains($n,'sprite')) return 'Mixer';
        return 'Otro';
    }
@endphp

<div class="space-y-6 max-w-7xl mx-auto" id="inventory-app">

    {{-- CABECERA --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 pb-4 border-b border-white/10">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/30">
                    Deposito Central & Bodega
                </span>
                <span class="text-zinc-600 text-xs">•</span>
                <span class="text-xs text-zinc-400 font-mono">Stock en tiempo real — Sin recargar</span>
            </div>
            <h2 class="text-2xl lg:text-3xl font-black tracking-tight text-white">
                Catalogo Maestro & Inventario de Bebidas
            </h2>
            <p class="text-xs text-zinc-400 mt-1">
                Escribe el numero exacto en el recuadro central o usa los botones
                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-[10px] font-mono">-10</kbd>
                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-[10px] font-mono">-1</kbd>
                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-[10px] font-mono">+1</kbd>
                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-[10px] font-mono">+10</kbd> — se guarda automaticamente.
            </p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <button type="button" onclick="openCreateDrawer()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-400 to-amber-500 text-zinc-950 font-black text-xs uppercase tracking-wider rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-amber-500/25 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                + Nuevo Producto
            </button>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-white/5 rounded-2xl overflow-hidden border border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.5)]">
        <div class="bg-[#080a0f] px-6 py-5">
            <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">Total Bodega</span>
            <div class="mt-1.5 text-3xl font-black font-mono text-white" id="kpi-total-bottles">
                {{ number_format($totalBottles) }} <span class="text-xs text-zinc-400 font-normal font-sans">unid.</span>
            </div>
        </div>
        <div class="bg-[#080a0f] px-6 py-5">
            <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">Valorizacion</span>
            <div class="mt-1.5 text-3xl font-black font-mono text-amber-400" id="kpi-valuation">
                <span class="text-sm font-sans mr-0.5 text-amber-400/70">Bs.</span>{{ number_format($totalValuation, 0) }}
            </div>
        </div>
        <div class="bg-[#080a0f] px-6 py-5">
            <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">Refs. Activas</span>
            <div class="mt-1.5 text-3xl font-black font-mono text-white">
                {{ $products->where('is_active', true)->count() }} <span class="text-xs text-zinc-400 font-normal font-sans">SKUs</span>
            </div>
        </div>
        <div class="bg-[#080a0f] px-6 py-5">
            <span class="text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">Stock Bajo</span>
            <div class="mt-1.5 text-3xl font-black font-mono {{ $lowStockCount > 0 ? 'text-rose-400' : 'text-zinc-500' }}" id="kpi-low-stock">
                {{ $lowStockCount }} <span class="text-xs font-normal font-sans">alertas</span>
            </div>
        </div>
    </div>

    {{-- FILTROS + BUSCADOR --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 backdrop-blur-xl bg-white/[0.02] border border-white/10 rounded-2xl p-3">
        <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-mono" id="filter-pills">
            @php
            $cats = [
                'all'     => 'Todos (' . $products->count() . ')',
                'Singani' => 'Singanis',
                'Ron'     => 'Fernet & Rones',
                'Whisky'  => 'Whiskys',
                'Gin'     => 'Gins',
                'Tequila' => 'Tequilas',
                'Mixer'   => 'Mixers',
            ];
            @endphp
            @foreach($cats as $key => $label)
                <button type="button" data-cat="{{ $key }}"
                        onclick="filterCat('{{ $key }}', this)"
                        class="category-btn px-3.5 py-2 rounded-xl font-bold transition-all cursor-pointer whitespace-nowrap {{ $key === 'all' ? 'bg-amber-400 text-zinc-950 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-white/5' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <div class="relative w-full sm:w-56 flex-shrink-0">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-zinc-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </span>
            <input type="text" id="product-search" placeholder="Buscar botella..." oninput="searchProducts(this.value)"
                   class="w-full pl-8 pr-3 py-2 text-xs bg-white/[0.04] border border-white/10 rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:border-amber-400/60 focus:ring-1 focus:ring-amber-400/30 transition-all font-mono">
        </div>
    </div>

    {{-- GRID DE TARJETAS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4" id="products-grid">

        @foreach($products as $prod)
        @php
            $sub   = getSubcat($prod->name, $prod->category);
            $stock = (int) $prod->stock_warehouse;
            $unitsPerPackage = max(1, (int) $prod->units_per_package);
            $stockPackages = intdiv($stock, $unitsPerPackage);
            $stockUnits = $stock % $unitsPerPackage;

            $iconColor = match($sub) {
                'Singani' => 'text-amber-400',
                'Whisky'  => 'text-yellow-300',
                'Ron'     => 'text-orange-400',
                'Gin'     => 'text-emerald-400',
                'Tequila' => 'text-lime-400',
                'Mixer'   => 'text-cyan-400',
                default   => 'text-zinc-300',
            };
            $glowRgb = match($sub) {
                'Singani' => '245,158,11',
                'Whisky'  => '253,224,71',
                'Ron'     => '251,146,60',
                'Gin'     => '52,211,153',
                'Tequila' => '163,230,53',
                'Mixer'   => '34,211,238',
                default   => '161,161,170',
            };
            $stockClass = $stock <= 0 ? 'text-zinc-600' : ($stock <= 10 ? 'text-rose-400' : 'text-emerald-400');
        @endphp

        <div class="product-card group relative flex flex-col rounded-2xl overflow-hidden
                    border border-white/[0.08] bg-white/[0.02] backdrop-blur-xl
                    shadow-[0_4px_24px_rgba(0,0,0,0.5)]
                    hover:border-white/20 hover:bg-white/[0.04] transition-all duration-200"
             data-id="{{ $prod->id }}"
             data-name="{{ strtolower($prod->name) }}"
             data-subcat="{{ $sub }}"
             data-price="{{ $prod->sale_price }}"
             data-stock="{{ $stock }}"
             data-units-per-package="{{ $unitsPerPackage }}">

            {{-- Aura de color al hover --}}
            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none rounded-2xl"
                 style="background: radial-gradient(circle at 50% 0%, rgba({{ $glowRgb }},0.18) 0%, transparent 70%);"></div>

            {{-- TOP: Badge + Icono + Nombre + Precio --}}
            <div class="relative p-4 pb-2 flex flex-col items-center gap-2">

                <span class="self-start px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase tracking-wider
                             bg-black/40 border border-white/10 {{ $iconColor }}">
                    {{ $sub }}
                </span>

                <div class="w-14 h-14 flex items-center justify-center rounded-xl
                            bg-white/[0.03] border border-white/10
                            group-hover:scale-105 transition-transform duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/[0.06] to-transparent pointer-events-none"></div>
                    @if($sub === 'Mixer')
                        <svg class="w-8 h-8 {{ $iconColor }} drop-shadow-[0_0_8px_currentColor]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M7 2h10"/><path d="M9 2v3a4 4 0 0 1-1 3v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V8a4 4 0 0 1-1-3V2"/><line x1="8" y1="14" x2="16" y2="14"/>
                        </svg>
                    @elseif($sub === 'Gin')
                        <svg class="w-8 h-8 {{ $iconColor }} drop-shadow-[0_0_8px_currentColor]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M10 2h4"/><path d="M10 2v3a2 2 0 0 1-.5 1.3L8 8v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V8l-1.5-1.7A2 2 0 0 1 14 5V2"/>
                            <circle cx="12" cy="13" r="2"/>
                        </svg>
                    @else
                        <svg class="w-8 h-8 {{ $iconColor }} drop-shadow-[0_0_8px_currentColor]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M10 2h4"/><path d="M10 2v3a2 2 0 0 1-.5 1.3L8 8v12a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2V8l-1.5-1.7A2 2 0 0 1 14 5V2"/>
                            <rect x="9" y="11" width="6" height="4" rx="0.5"/>
                        </svg>
                    @endif
                </div>

                <h3 class="text-[11px] font-bold text-white text-center leading-tight line-clamp-2 w-full px-1">{{ $prod->name }}</h3>
                <span class="text-[10px] text-zinc-500 font-mono">Bs. {{ number_format($prod->sale_price, 0) }}</span>
            </div>

            <div class="mx-3 border-t border-white/[0.07]"></div>

            {{-- INPUT CENTRAL DE STOCK --}}
            <div class="px-3 pt-3 pb-1 flex flex-col items-center gap-1">
                <span class="text-[9px] font-mono font-bold text-zinc-500 uppercase tracking-wider">Stock Bodega</span>
                <div class="relative flex items-center justify-center w-full">
                    <input
                        type="number"
                        min="0"
                        id="stock-input-{{ $prod->id }}"
                        value="{{ $stock }}"
                        class="stock-input w-full text-center text-2xl font-black font-mono rounded-xl py-2 px-2 border transition-all duration-150 cursor-text
                               bg-white/[0.04] border-white/10
                               focus:bg-white/[0.07] focus:border-amber-400/60 focus:ring-2 focus:ring-amber-400/20 focus:outline-none
                               {{ $stockClass }}"
                        data-product-id="{{ $prod->id }}"
                        data-sale-price="{{ $prod->sale_price }}"
                        data-last-saved="{{ $stock }}"
                        oninput="onStockInput(this)"
                        onchange="onStockChange(this)"
                        onkeydown="if(event.key==='Enter'){this.blur();event.preventDefault();}">
                    <span class="save-indicator absolute -top-1 -right-1 text-[9px] font-mono opacity-0 transition-opacity duration-300 pointer-events-none bg-[#080a0f] px-1 rounded"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 w-full mt-1">
                    <label class="text-[9px] text-zinc-500 font-mono text-center">
                        Paquetes
                        <input type="number" min="0" value="{{ $stockPackages }}"
                               id="stock-packages-{{ $prod->id }}"
                               data-product-id="{{ $prod->id }}"
                               onchange="onStockPartChange({{ $prod->id }})"
                               class="stock-part-input mt-1 w-full text-center text-xs font-black font-mono rounded-lg py-1.5 px-1 bg-white/[0.04] border border-white/10 text-zinc-300 focus:outline-none focus:border-amber-400/60">
                    </label>
                    <label class="text-[9px] text-zinc-500 font-mono text-center">
                        Unidades
                        <input type="number" min="0" value="{{ $stockUnits }}"
                               id="stock-units-{{ $prod->id }}"
                               data-product-id="{{ $prod->id }}"
                               onchange="onStockPartChange({{ $prod->id }})"
                               class="stock-part-input mt-1 w-full text-center text-xs font-black font-mono rounded-lg py-1.5 px-1 bg-white/[0.04] border border-white/10 text-zinc-300 focus:outline-none focus:border-amber-400/60">
                    </label>
                </div>
            </div>

            {{-- BOTONES -10 / -1 / +1 / +10 --}}
            <div class="px-3 pb-4 pt-2 grid grid-cols-4 gap-1">
                <button type="button" onclick="stepStock({{ $prod->id }}, -10)"
                        class="step-btn py-2 rounded-lg text-[10px] font-mono font-black active:scale-95 transition-all cursor-pointer
                               bg-white/[0.03] hover:bg-rose-500/10 text-zinc-500 hover:text-rose-400 border border-white/[0.07] hover:border-rose-500/20">
                    -10
                </button>
                <button type="button" onclick="stepStock({{ $prod->id }}, -1)"
                        class="step-btn py-2 rounded-lg text-[10px] font-mono font-black active:scale-95 transition-all cursor-pointer
                               bg-white/[0.03] hover:bg-rose-500/10 text-zinc-400 hover:text-rose-400 border border-white/[0.07] hover:border-rose-500/20">
                    -1
                </button>
                <button type="button" onclick="stepStock({{ $prod->id }}, +1)"
                        class="step-btn py-2 rounded-lg text-[10px] font-mono font-black active:scale-95 transition-all cursor-pointer
                               bg-white/[0.03] hover:bg-emerald-500/10 text-zinc-400 hover:text-emerald-400 border border-white/[0.07] hover:border-emerald-500/20">
                    +1
                </button>
                <button type="button" onclick="stepStock({{ $prod->id }}, +10)"
                        class="step-btn py-2 rounded-lg text-[10px] font-mono font-black active:scale-95 transition-all cursor-pointer
                               bg-white/[0.03] hover:bg-emerald-500/10 text-zinc-300 hover:text-emerald-400 border border-white/[0.07] hover:border-emerald-500/20">
                    +10
                </button>
            </div>

            {{-- FOOTER: Presentacion + Editar --}}
            <div class="px-3 pb-3 flex items-center justify-between border-t border-white/[0.06] pt-2 mt-auto">
                <span class="text-[9px] font-mono text-zinc-600">{{ $prod->unit }}</span>
                <button type="button"
                        onclick="openEditDrawer({{ $prod->id }}, {{ json_encode($prod->name) }}, {{ json_encode($prod->category) }}, {{ json_encode($prod->unit) }}, {{ $prod->sale_price }}, {{ $prod->cost_price ?? 0 }}, {{ $prod->units_per_package }}, {{ $prod->stock_warehouse }}, {{ $prod->is_active ? 1 : 0 }})"
                        class="text-[10px] font-mono text-zinc-500 hover:text-amber-400 transition-colors cursor-pointer">
                    Editar &rarr;
                </button>
            </div>

        </div>
        @endforeach

    </div>

</div>{{-- /inventory-app --}}


{{-- ── OVERLAY ────────────────────────────────────────────── --}}
<div class="drawer-overlay" id="product-drawer-overlay" onclick="closeDrawer()"></div>

{{-- ── DRAWER CREAR ────────────────────────────────────────── --}}
<div class="drawer-panel" id="drawer-create-product">
    <div class="drawer-header">
        <div>
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1.5">Catalogo Maestro</span>
            <h2 class="text-lg font-black text-white tracking-tight">Agregar Bebida o Insumo</h2>
            <p class="text-xs text-zinc-400 mt-0.5">Nuevo item para inventario de barras y almacen central.</p>
        </div>
        <button onclick="closeDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <form method="POST" action="{{ route('products.store') }}" class="drawer-body space-y-5">
        @csrf
        <div>
            <label for="d_name" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Nombre <span class="text-amber-400">*</span></label>
            <input type="text" name="name" id="d_name" required placeholder="Ej. Fernet Branca 750ml"
                   class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="d_category" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Categoria <span class="text-amber-400">*</span></label>
                <select name="category" id="d_category" required class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Licores" class="bg-zinc-900">Licores (Botellas)</option>
                    <option value="Mixers" class="bg-zinc-900">Mixers / Gaseosas</option>
                    <option value="Cervezas" class="bg-zinc-900">Cervezas</option>
                    <option value="Cigarros" class="bg-zinc-900">Cigarros</option>
                    <option value="Otros" class="bg-zinc-900">Otros / Insumos</option>
                </select>
            </div>
            <div>
                <label for="d_unit" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Presentacion <span class="text-amber-400">*</span></label>
                <input type="text" name="unit" id="d_unit" required value="Botella 750ml"
                       class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="d_sale_price" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Precio Venta <span class="text-amber-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-xs font-mono font-bold text-amber-400">Bs.</span>
                    <input type="number" step="0.5" name="sale_price" id="d_sale_price" required value="0"
                           class="glass-input w-full pl-10 pr-3 py-3 rounded-xl text-sm font-mono font-bold text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
            </div>
            <div>
                <label for="d_cost_price" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Costo Compra</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-xs font-mono font-bold text-zinc-500">Bs.</span>
                    <input type="number" step="0.5" name="cost_price" id="d_cost_price" value="0"
                           class="glass-input w-full pl-10 pr-3 py-3 rounded-xl text-sm font-mono text-zinc-300 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
            </div>
            <div>
                <label for="d_units_per_package" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Unid. x Caja</label>
                <input type="number" name="units_per_package" id="d_units_per_package" required value="6"
                       class="glass-input w-full px-4 py-3 rounded-xl text-sm font-mono text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
        </div>
        <div>
            <label for="d_stock_warehouse" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Stock Inicial en Almacen</label>
            <input type="number" name="stock_warehouse" id="d_stock_warehouse" required value="0"
                   class="glass-input w-full px-4 py-3 rounded-xl text-sm font-mono font-bold text-amber-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>
        <div class="drawer-footer" style="margin: 0 -1.75rem -1.75rem; padding: 1.25rem 1.75rem;">
            <button type="button" onclick="closeDrawer()" class="px-5 py-2.5 glass-card hover:bg-white/10 text-zinc-400 hover:text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">Cancelar</button>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-extrabold tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all cursor-pointer">Guardar Producto</button>
        </div>
    </form>
</div>

{{-- ── DRAWER EDITAR ───────────────────────────────────────── --}}
<div class="drawer-panel" id="drawer-edit-product">
    <div class="drawer-header">
        <div>
            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-1.5" id="edit-prod-badge">Edicion de Catalogo</span>
            <h2 class="text-lg font-black text-white tracking-tight" id="edit-prod-title">Editar Producto</h2>
            <p class="text-xs text-zinc-400 mt-0.5">Actualiza precios, categoria o stock en almacen central.</p>
        </div>
        <button onclick="closeDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 text-zinc-400 hover:text-white transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <form method="POST" id="edit-prod-form" action="" class="drawer-body space-y-5">
        @csrf
        @method('PUT')
        <div>
            <label for="ed_name" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Nombre <span class="text-amber-400">*</span></label>
            <input type="text" name="name" id="ed_name" required class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="ed_category" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Categoria <span class="text-amber-400">*</span></label>
                <select name="category" id="ed_category" required class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="Licores" class="bg-zinc-900">Licores (Botellas)</option>
                    <option value="Mixers" class="bg-zinc-900">Mixers / Gaseosas</option>
                    <option value="Cervezas" class="bg-zinc-900">Cervezas</option>
                    <option value="Cigarros" class="bg-zinc-900">Cigarros</option>
                    <option value="Otros" class="bg-zinc-900">Otros / Insumos</option>
                </select>
            </div>
            <div>
                <label for="ed_unit" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Presentacion <span class="text-amber-400">*</span></label>
                <input type="text" name="unit" id="ed_unit" required class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label for="ed_sale_price" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Precio Venta <span class="text-amber-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-xs font-mono font-bold text-amber-400">Bs.</span>
                    <input type="number" step="0.5" name="sale_price" id="ed_sale_price" required class="glass-input w-full pl-10 pr-3 py-3 rounded-xl text-sm font-mono font-bold text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
            </div>
            <div>
                <label for="ed_cost_price" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Costo Compra</label>
                <div class="relative">
                    <span class="absolute left-3 top-3 text-xs font-mono font-bold text-zinc-500">Bs.</span>
                    <input type="number" step="0.5" name="cost_price" id="ed_cost_price" class="glass-input w-full pl-10 pr-3 py-3 rounded-xl text-sm font-mono text-zinc-300 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                </div>
            </div>
            <div>
                <label for="ed_units_per_package" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Unid. x Caja</label>
                <input type="number" name="units_per_package" id="ed_units_per_package" required class="glass-input w-full px-4 py-3 rounded-xl text-sm font-mono text-white focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="ed_stock_warehouse" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Stock Almacen</label>
                <input type="number" name="stock_warehouse" id="ed_stock_warehouse" required class="glass-input w-full px-4 py-3 rounded-xl text-sm font-mono font-bold text-amber-400 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
            </div>
            <div>
                <label for="ed_is_active" class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1.5">Estado</label>
                <select name="is_active" id="ed_is_active" class="glass-input w-full px-4 py-3 rounded-xl text-sm text-white bg-zinc-900/90 focus:outline-none focus:border-amber-500/50 focus:ring-1 focus:ring-amber-500/50 transition-all">
                    <option value="1" class="bg-zinc-900">Activo (En Barras)</option>
                    <option value="0" class="bg-zinc-900">Inactivo / Descontinuado</option>
                </select>
            </div>
        </div>
        <div class="drawer-footer" style="margin: 0 -1.75rem -1.75rem; padding: 1.25rem 1.75rem;">
            <button type="button" onclick="confirmDeleteProduct()" class="text-xs text-rose-400 hover:text-rose-300 font-bold uppercase tracking-wider hover:underline transition-all cursor-pointer">Eliminar Producto</button>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeDrawer()" class="px-5 py-2.5 glass-card hover:bg-white/10 text-zinc-400 hover:text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">Cancelar</button>
                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-zinc-950 text-xs font-extrabold tracking-wider uppercase rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all cursor-pointer">Guardar Cambios</button>
            </div>
        </div>
    </form>
    <form id="delete-prod-inline-form" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

{{-- ── JAVASCRIPT REACTIVO ─────────────────────────────────── --}}
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const BASE = '{{ url("/products") }}';
let debounceTimers = {};

/* --- DOM helpers --- */
function getInput(id) { return document.getElementById('stock-input-' + id); }
function getPartInput(id, part) { return document.getElementById('stock-' + part + '-' + id); }
function getSaveEl(id) { return getInput(id)?.parentElement?.querySelector('.save-indicator'); }

function syncPartInputs(id, total) {
    const card = document.querySelector('.product-card[data-id="' + id + '"]');
    const unitsPerPackage = parseInt(card?.dataset.unitsPerPackage) || 1;
    const packages = Math.floor(Math.max(0, total) / unitsPerPackage);
    const units = Math.max(0, total) % unitsPerPackage;
    const packagesInput = getPartInput(id, 'packages');
    const unitsInput = getPartInput(id, 'units');
    if (packagesInput) packagesInput.value = packages;
    if (unitsInput) unitsInput.value = units;
}

/* --- Color semantico del numero --- */
function applyColor(inp, val) {
    inp.classList.remove('text-zinc-600', 'text-rose-400', 'text-emerald-400');
    if (val <= 0)       inp.classList.add('text-zinc-600');
    else if (val <= 10) inp.classList.add('text-rose-400');
    else                inp.classList.add('text-emerald-400');
}

/* --- Indicador por tarjeta --- */
function showSaving(id) {
    const el = getSaveEl(id); if (!el) return;
    el.textContent = 'Guardando...'; el.style.color = '#71717a'; el.style.opacity = '1';
}
function showSaved(id) {
    const el = getSaveEl(id); if (!el) return;
    el.textContent = 'Guardado'; el.style.color = '#34d399'; el.style.opacity = '1';
    clearTimeout(el._t); el._t = setTimeout(() => el.style.opacity = '0', 2200);
}
function showError(id) {
    const el = getSaveEl(id); if (!el) return;
    el.textContent = 'Error!'; el.style.color = '#f87171'; el.style.opacity = '1';
    clearTimeout(el._t); el._t = setTimeout(() => el.style.opacity = '0', 3000);
}

/* --- Actualizar KPIs en tiempo real --- */
function refreshKPIs() {
    let total = 0, val = 0, low = 0;
    document.querySelectorAll('.product-card').forEach(c => {
        const inp = getInput(c.dataset.id);
        const s = inp ? (parseInt(inp.value) || 0) : (parseInt(c.dataset.stock) || 0);
        c.dataset.stock = s;
        total += s;
        val   += s * (parseFloat(c.dataset.price) || 0);
        if (s <= 10) low++;
    });
    const kb = document.getElementById('kpi-total-bottles');
    const kv = document.getElementById('kpi-valuation');
    const kl = document.getElementById('kpi-low-stock');
    if (kb) kb.innerHTML = total.toLocaleString() + ' <span class="text-xs text-zinc-400 font-normal font-sans">unid.</span>';
    if (kv) kv.innerHTML = '<span class="text-sm font-sans mr-0.5 text-amber-400/70">Bs.</span>' + Math.round(val).toLocaleString();
    if (kl) kl.innerHTML = low + ' <span class="text-xs font-normal font-sans">alertas</span>';
}

/* --- Enviar PATCH al servidor --- */
function pushStock(productId, newStock, oldStock) {
    showSaving(productId);
    fetch(BASE + '/' + productId + '/quick-stock', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ stock: newStock }),
    })
    .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
    .then(data => {
        const inp = getInput(productId);
        if (inp && parseInt(inp.value) !== data.stock_warehouse) {
            inp.value = data.stock_warehouse;
            applyColor(inp, data.stock_warehouse);
        }
        syncPartInputs(productId, data.stock_warehouse);
        showSaved(productId);
        refreshKPIs();
    })
    .catch(() => {
        const inp = getInput(productId);
        if (inp) { inp.value = oldStock; applyColor(inp, oldStock); }
        showError(productId);
    });
}

/* --- Stepper: -10 / -1 / +1 / +10 --- */
function stepStock(productId, delta) {
    const inp = getInput(productId); if (!inp) return;
    const old = parseInt(inp.value) || 0;
    const nv  = Math.max(0, old + delta);
    inp.value = nv;
    applyColor(inp, nv);
    /* Debounce 400ms: agrupa multiples clics rapidos */
    clearTimeout(debounceTimers[productId]);
    debounceTimers[productId] = setTimeout(() => pushStock(productId, nv, old), 400);
    refreshKPIs();
}

/* --- Input directo: color mientras tipea --- */
function onStockInput(inp) {
    const total = parseInt(inp.value) || 0;
    applyColor(inp, total);
    syncPartInputs(inp.dataset.productId, total);
    refreshKPIs();
}

/* --- Input directo: guardar al salir del campo o presionar Enter --- */
function onStockChange(inp) {
    const productId = inp.dataset.productId;
    const old = parseInt(inp.dataset.lastSaved) || 0;
    const nv  = Math.max(0, parseInt(inp.value) || 0);
    inp.value = nv;
    applyColor(inp, nv);
    inp.dataset.lastSaved = nv;
    pushStock(productId, nv, old);
    refreshKPIs();
}

function onStockPartChange(productId) {
    const packagesInput = getPartInput(productId, 'packages');
    const unitsInput = getPartInput(productId, 'units');
    const totalInput = getInput(productId);
    const packages = Math.max(0, parseInt(packagesInput?.value) || 0);
    const units = Math.max(0, parseInt(unitsInput?.value) || 0);
    const unitsPerPackage = parseInt(document.querySelector('.product-card[data-id="' + productId + '"]')?.dataset.unitsPerPackage) || 1;
    const old = parseInt(totalInput?.value) || 0;
    const total = (packages * unitsPerPackage) + units;

    if (totalInput) {
        totalInput.value = total;
        applyColor(totalInput, total);
        totalInput.dataset.lastSaved = total;
    }
    pushStockParts(productId, packages, units, old);
    refreshKPIs();
}

function pushStockParts(productId, packages, units, oldStock) {
    showSaving(productId);
    fetch(BASE + '/' + productId + '/quick-stock', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ stock_packages: packages, stock_units: units }),
    })
    .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
    .then(data => {
        const inp = getInput(productId);
        if (inp) {
            inp.value = data.stock_warehouse;
            inp.dataset.lastSaved = data.stock_warehouse;
            applyColor(inp, data.stock_warehouse);
        }
        syncPartInputs(productId, data.stock_warehouse);
        showSaved(productId);
        refreshKPIs();
    })
    .catch(() => {
        const inp = getInput(productId);
        if (inp) { inp.value = oldStock; applyColor(inp, oldStock); }
        syncPartInputs(productId, oldStock);
        showError(productId);
    });
}

/* --- Filtro por subcategoria --- */
function filterCat(cat, btn) {
    document.querySelectorAll('.category-btn').forEach(b => {
        b.classList.remove('bg-amber-400', 'text-zinc-950', 'shadow-sm');
        b.classList.add('text-zinc-400', 'hover:text-white', 'hover:bg-white/5');
    });
    btn.classList.add('bg-amber-400', 'text-zinc-950', 'shadow-sm');
    btn.classList.remove('text-zinc-400', 'hover:text-white', 'hover:bg-white/5');
    document.querySelectorAll('.product-card').forEach(c => {
        c.style.display = (cat === 'all' || c.dataset.subcat === cat) ? '' : 'none';
    });
}

/* --- Buscador en vivo --- */
function searchProducts(q) {
    const query = q.toLowerCase().trim();
    document.querySelectorAll('.product-card').forEach(c => {
        c.style.display = (!query || c.dataset.name.includes(query)) ? '' : 'none';
    });
}

/* --- Drawers --- */
function openCreateDrawer() {
    document.getElementById('product-drawer-overlay').classList.add('is-open');
    document.getElementById('drawer-create-product').classList.add('is-open');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('d_name').focus(), 350);
}

function openEditDrawer(id, name, cat, unit, saleP, costP, uPkg, stock, active) {
    document.getElementById('edit-prod-form').action = BASE + '/' + id;
    document.getElementById('delete-prod-inline-form').action = BASE + '/' + id;
    document.getElementById('edit-prod-title').textContent = name;
    document.getElementById('edit-prod-badge').textContent = 'Edicion #' + id;
    document.getElementById('ed_name').value = name;
    document.getElementById('ed_unit').value = unit;
    document.getElementById('ed_sale_price').value = saleP;
    document.getElementById('ed_cost_price').value = costP;
    document.getElementById('ed_units_per_package').value = uPkg;
    document.getElementById('ed_stock_warehouse').value = stock;
    document.getElementById('ed_is_active').value = active;
    for (let o of document.getElementById('ed_category').options) o.selected = o.value === cat;
    document.getElementById('product-drawer-overlay').classList.add('is-open');
    document.getElementById('drawer-edit-product').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeDrawer() {
    document.getElementById('product-drawer-overlay').classList.remove('is-open');
    document.getElementById('drawer-create-product').classList.remove('is-open');
    document.getElementById('drawer-edit-product').classList.remove('is-open');
    document.body.style.overflow = '';
}

function confirmDeleteProduct() {
    if (confirm('Eliminar definitivamente este producto? Esta accion no se puede deshacer.')) {
        document.getElementById('delete-prod-inline-form').submit();
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>

@endsection
