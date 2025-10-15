<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CashierInventoryController extends Controller
{
    /**
     * Display a listing of inventory items.
     */
    public function index()
    {
        $products = Product::orderBy('name')->paginate(10);
        return view('cashier.inventory.index', compact('products'));
    }

    /**
     * Display the specified product details.
     */
    public function show(Product $product)
    {
        return view('cashier.inventory.show', compact('product'));
    }

    /**
     * Update product stock quantity.
     */
    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_change' => 'required|integer',
            'note' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $oldStock = $product->stock;
        $product->stock += $validated['stock_change'];
        $product->save();

        // Log the stock change
        $product->stockLogs()->create([
            'previous_stock' => $oldStock,
            'new_stock' => $product->stock,
            'change' => $validated['stock_change'],
            'note' => $validated['note'] ?? 'Stock updated by cashier',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('cashier.inventory.show', $product)
            ->with('success', 'Stock updated successfully.');
    }

    /**
     * Show products with low stock.
     */
    public function lowStock()
    {
        $products = Product::where('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->paginate(10);
        
        return view('cashier.inventory.low-stock', compact('products'));
    }
}
