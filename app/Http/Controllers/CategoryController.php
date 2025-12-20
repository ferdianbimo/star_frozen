<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all active categories for dropdown.
     */
    public function index()
    {
        $categories = Category::active()
            ->withCount('products')
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique' => 'Kategori ini sudah ada.',
        ]);

        $category = Category::create([
            'name' => trim($request->name),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'category' => $category
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category is used by products
        $productCount = \App\Models\Product::where('category', $category->name)->count();
        
        if ($productCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kategori tidak dapat dihapus karena digunakan oleh {$productCount} produk."
            ], 400);
        }
        
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
