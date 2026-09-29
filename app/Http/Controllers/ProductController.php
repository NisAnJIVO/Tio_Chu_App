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

        Product::create($validated);

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
        ]);

        if (isset($validated['delta'])) {
            $product->stock_warehouse = max(0, (int)$product->stock_warehouse + (int)$validated['delta']);
        } elseif (isset($validated['stock'])) {
            $product->stock_warehouse = max(0, (int)$validated['stock']);
        }

        $product->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'product_id' => $product->id,
                'stock_warehouse' => $product->stock_warehouse,
                'status' => $product->stock_warehouse > 10 ? 'in_stock' : ($product->stock_warehouse > 0 ? 'low_stock' : 'out_of_stock'),
                'message' => 'Stock de ' . $product->name . ' actualizado a ' . $product->stock_warehouse . '.',
            ]);
        }

        return back()->with('success', 'Stock actualizado a ' . $product->stock_warehouse . '.');
    }
}
