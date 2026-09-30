<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $query = Product::query();

        if ($category) {
            $query->where('category', $category);
        }

        $products = $query->orderBy('id')->get();
        $categories = Product::select('category')->distinct()->pluck('category');

        return view('products.index', compact('products', 'categories', 'category'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'sale_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'units_per_package' => 'required|integer|min:1',
            'stock_warehouse' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);
        $this->syncStockBreakdown($product);
        $product->save();

        return redirect()->route('products.index')->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'sale_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'units_per_package' => 'required|integer|min:1',
            'stock_warehouse' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $product->update($validated);
        $this->syncStockBreakdown($product);
        $product->save();

        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado.');
    }

    /**
     * Actualización ágil de stock con 1 solo toque (Fetch/AJAX o Form)
     */
    public function updateQuickStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'delta' => 'nullable|integer',
            'stock' => 'nullable|integer|min:0',
            'stock_packages' => 'nullable|integer|min:0',
            'stock_units' => 'nullable|integer|min:0',
        ]);

        if (isset($validated['stock_packages']) || isset($validated['stock_units'])) {
            $unitsPerPackage = max(1, (int) $product->units_per_package);
            $packages = max(0, (int)($validated['stock_packages'] ?? $product->stock_packages));
            $units = max(0, (int)($validated['stock_units'] ?? $product->stock_units));
            $packages += intdiv($units, $unitsPerPackage);
            $units %= $unitsPerPackage;
            $product->stock_packages = $packages;
            $product->stock_units = $units;
            $product->stock_warehouse = ($packages * $unitsPerPackage) + $units;
        } elseif (isset($validated['delta'])) {
            $product->stock_warehouse = max(0, (int)$product->stock_warehouse + (int)$validated['delta']);
        } elseif (isset($validated['stock'])) {
            $product->stock_warehouse = max(0, (int)$validated['stock']);
        }

        $this->syncStockBreakdown($product);
        $product->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'product_id' => $product->id,
                'stock_warehouse' => $product->stock_warehouse,
                'stock_packages' => $product->stock_packages,
                'stock_units' => $product->stock_units,
                'status' => $product->stock_warehouse > 10 ? 'in_stock' : ($product->stock_warehouse > 0 ? 'low_stock' : 'out_of_stock'),
                'message' => 'Stock de ' . $product->name . ' actualizado a ' . $product->stock_warehouse . '.',
            ]);
        }

        return back()->with('success', 'Stock actualizado a ' . $product->stock_warehouse . '.');
    }

    private function syncStockBreakdown(Product $product): void
    {
        $unitsPerPackage = max(1, (int) $product->units_per_package);
        $stock = max(0, (int) $product->stock_warehouse);
        $product->stock_packages = intdiv($stock, $unitsPerPackage);
        $product->stock_units = $stock % $unitsPerPackage;
    }
}
