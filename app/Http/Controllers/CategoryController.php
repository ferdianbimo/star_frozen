<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CategoryController - Mengelola data kategori produk (API).
 *
 * Controller ini menangani CRUD kategori via JSON response:
 * - Mendapatkan daftar kategori untuk dropdown
 * - Membuat kategori baru
 * - Menghapus kategori (dengan proteksi jika digunakan produk)
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class CategoryController extends Controller
{
    /**
     * Mendapatkan semua kategori aktif untuk dropdown.
     *
     * @return JsonResponse Daftar kategori dengan product count
     */
    public function index(): JsonResponse
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
     * Menyimpan kategori baru.
     *
     * @param  Request $request Request dengan nama dan deskripsi kategori
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
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
     * Menghapus kategori.
     *
     * Tidak dapat menghapus kategori yang masih digunakan oleh produk.
     *
     * @param  int $id ID kategori yang akan dihapus
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $category = Category::findOrFail($id);
        
        // Check if category is used by products
        $productCount = Product::where('category', $category->name)->count();
        
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
