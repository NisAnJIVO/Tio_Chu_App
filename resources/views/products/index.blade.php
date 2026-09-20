@extends('layouts.app')

@section('title', 'Inventario Bebidas')

@section('content')
<div class="space-y-6">

    <!-- Cabecera -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Catálogo de Bebidas e Insumos</h2>
            <p class="text-xs text-zinc-600">Catálogo maestro de licores, mixers y precios oficiales de venta en barra.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.create') }}" 
               class="inline-flex items-center px-3 py-1.5 bg-zinc-900 text-white text-xs font-medium rounded hover:bg-zinc-800 transition-colors">
                + Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Filtro por Categoría -->
    <div class="flex items-center gap-2 border-b border-zinc-200 pb-3 text-xs">
        <span class="font-semibold text-zinc-700">Categoría:</span>
        <a href="{{ route('products.index') }}" 
           class="px-2.5 py-1 rounded {{ !$category ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}">
            Todos ({{ $products->count() }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category' => $cat]) }}" 
               class="px-2.5 py-1 rounded {{ $category === $cat ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Tabla Minimalista de Productos -->
    <div class="bg-white border border-zinc-200 rounded overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-zinc-50 border-b border-zinc-200 text-zinc-600 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">N°</th>
                        <th class="px-4 py-3">Descripción</th>
                        <th class="px-4 py-3">Categoría</th>
                        <th class="px-4 py-3">Presentación</th>
                        <th class="px-4 py-3 text-center">Unid. x Paquete/Caja</th>
                        <th class="px-4 py-3 text-right">Precio Venta (Bs.)</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-right w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200">
                    @forelse($products as $prod)
                        <tr class="hover:bg-zinc-50/50">
                            <td class="px-4 py-2.5 text-center font-mono text-zinc-600">{{ $prod->id }}</td>
                            <td class="px-4 py-2.5 font-medium text-zinc-900">{{ $prod->name }}</td>
                            <td class="px-4 py-2.5 text-zinc-600">{{ $prod->category }}</td>
                            <td class="px-4 py-2.5 text-zinc-600">{{ $prod->unit }}</td>
                            <td class="px-4 py-2.5 text-center font-mono text-zinc-700">{{ $prod->units_per_package }}</td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-zinc-900">
                                Bs. {{ number_format($prod->sale_price, 2) }}
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if($prod->is_active)
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Activo</span>
                                @else
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-600 border border-zinc-200">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <a href="{{ route('products.edit', $prod) }}" class="text-zinc-600 hover:text-zinc-900 font-medium">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-zinc-600">
                                No se encontraron productos en esta categoría.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
