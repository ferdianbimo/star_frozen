<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLog;
use App\Models\ProductBatch;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * CashierInventoryController - Mengelola inventory untuk Kasir.
 *
 * Controller ini menangani operasi inventory oleh kasir:
 * - CRUD produk dengan multi-unit pricing
 * - Manajemen batch produk (stock in)
 * - Upload dan hapus gambar produk
 * - Pencatatan stock logs
 *
 * Fitur multi-unit:
 * - Unit dasar: pcs (satuan terkecil)
 * - Unit packaging: pack, renteng, box, karton
 * - Konversi otomatis antar unit
 * - Harga jual dan beli per unit
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class CashierInventoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - LIST & SEARCH
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan daftar produk dengan search dan filter.
     *
     * @return View
     */
    public function index(): View
    {
        $q = request()->input('q');
        $category = request()->input('category');
        $sort = request()->input('sort');

        $query = Product::query();
        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%");
            });
        }
        if ($category) {
            $query->where('category', $category);
        }

        // apply sorting based on request
        switch ($sort) {
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            default:
                $query->orderBy('name');
        }

        $products = $query->paginate(8)->appends(request()->query());

        // get categories for filter
        $categories = Product::select('category')->distinct()->whereNotNull('category')->pluck('category');

        return view('cashier.inventory.index', compact('products', 'categories', 'q', 'category'));
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - CREATE PRODUCT
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan form tambah produk baru.
     *
     * @return View
     */
    public function create(): View
    {
        return view('cashier.inventory.create');
    }

    /**
     * Menyimpan produk baru ke database.
     *
     * Memproses data produk termasuk:
     * - Multi-unit flags dan konversi
     * - Multi-unit pricing (jual dan beli)
     * - Upload gambar produk
     * - Pencatatan activity log
     *
     * @param  Request $request Request dengan data produk
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'unit' => 'nullable|string|max:50',
            // Unit sell flags
            'sell_pcs' => 'nullable',
            'sell_pack' => 'nullable',
            'sell_renteng' => 'nullable',
            'sell_box' => 'nullable',
            'sell_karton' => 'nullable',
            // Unit conversions
            'pcs_per_pack' => 'nullable|integer|min:1',
            'pcs_per_renteng' => 'nullable|integer|min:1',
            'box_contains_qty' => 'nullable|integer|min:1',
            'box_contains_unit' => 'nullable|string|in:pcs,pack,renteng',
            'karton_contains_qty' => 'nullable|integer|min:1',
            'karton_contains_unit' => 'nullable|string|in:pcs,pack,renteng,box',
            // Selling prices
            'price_pcs' => 'nullable|numeric|min:0',
            'price_pack' => 'nullable|numeric|min:0',
            'price_renteng' => 'nullable|numeric|min:0',
            'price_box' => 'nullable|numeric|min:0',
            'price_karton' => 'nullable|numeric|min:0',
            // Purchase prices
            'purchase_price_pcs' => 'nullable|numeric|min:0',
            'purchase_price_pack' => 'nullable|numeric|min:0',
            'purchase_price_renteng' => 'nullable|numeric|min:0',
            'purchase_price_box' => 'nullable|numeric|min:0',
            'purchase_price_karton' => 'nullable|numeric|min:0',
        ]);

        // Handle checkbox values
        $data['sell_pcs'] = $request->has('sell_pcs');
        $data['sell_pack'] = $request->has('sell_pack');
        $data['sell_renteng'] = $request->has('sell_renteng');
        $data['sell_box'] = $request->has('sell_box');
        $data['sell_karton'] = $request->has('sell_karton');

        // Set default price from price_pcs if main price not set
        if (empty($data['price']) && !empty($data['price_pcs'])) {
            $data['price'] = $data['price_pcs'];
        }

        // Set default purchase_price from purchase_price_pcs if not set
        if (empty($data['purchase_price']) && !empty($data['purchase_price_pcs'])) {
            $data['purchase_price'] = $data['purchase_price_pcs'];
        }

        // Default stock to 0
        $data['stock'] = $data['stock'] ?? 0;

        // handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data + ['is_active' => true]);

        // Log activity
        ActivityLogService::logCreate('product', "Menambahkan produk: {$product->name}", $product, $data);

        return redirect()->route('cashier.inventory.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'unit' => 'nullable|string|max:50',
            // Unit sell flags
            'sell_pcs' => 'nullable',
            'sell_pack' => 'nullable',
            'sell_renteng' => 'nullable',
            'sell_box' => 'nullable',
            'sell_karton' => 'nullable',
            // Unit conversions
            'pcs_per_pack' => 'nullable|integer|min:1',
            'pcs_per_renteng' => 'nullable|integer|min:1',
            'box_contains_qty' => 'nullable|integer|min:1',
            'box_contains_unit' => 'nullable|string|in:pcs,pack,renteng',
            'karton_contains_qty' => 'nullable|integer|min:1',
            'karton_contains_unit' => 'nullable|string|in:pcs,pack,renteng,box',
            // Selling prices
            'price_pcs' => 'nullable|numeric|min:0',
            'price_pack' => 'nullable|numeric|min:0',
            'price_renteng' => 'nullable|numeric|min:0',
            'price_box' => 'nullable|numeric|min:0',
            'price_karton' => 'nullable|numeric|min:0',
            // Purchase prices
            'purchase_price_pcs' => 'nullable|numeric|min:0',
            'purchase_price_pack' => 'nullable|numeric|min:0',
            'purchase_price_renteng' => 'nullable|numeric|min:0',
            'purchase_price_box' => 'nullable|numeric|min:0',
            'purchase_price_karton' => 'nullable|numeric|min:0',
        ]);

        // Handle checkbox values
        $data['sell_pcs'] = $request->has('sell_pcs');
        $data['sell_pack'] = $request->has('sell_pack');
        $data['sell_renteng'] = $request->has('sell_renteng');
        $data['sell_box'] = $request->has('sell_box');
        $data['sell_karton'] = $request->has('sell_karton');

        // Set default price from price_pcs if main price not set
        if (empty($data['price']) && !empty($data['price_pcs'])) {
            $data['price'] = $data['price_pcs'];
        }

        // Set default purchase_price from purchase_price_pcs if not set
        if (!empty($data['purchase_price_pcs'])) {
            $data['purchase_price'] = $data['purchase_price_pcs'];
        }

        // handle image replacement
        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        } elseif ($request->input('remove_image') == '1') {
            // handle image removal without replacement
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        }

        $oldValues = $product->toArray();
        $product->update($data);

        // Log activity
        ActivityLogService::logUpdate('product', "Mengubah produk: {$product->name}", $product, $oldValues, $data);

        return redirect()->route('cashier.inventory.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // delete image file if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $productName = $product->name;
        $oldValues = $product->toArray();
        $product->delete();

        // Log activity
        ActivityLogService::logDelete('product', "Menghapus produk: {$productName}", null, $oldValues);

        return redirect()->route('cashier.inventory.index')->with('success', 'Product deleted');
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

        // Determine transaction type and calculate value
        $transactionType = 'manual';
        $unitPrice = 0;
        $totalValue = 0;

        if ($validated['stock_change'] < 0) {
            // Stock out (penjualan)
            $transactionType = 'sale';
            $unitPrice = $product->price; // harga jual
            $totalValue = abs($validated['stock_change']) * $unitPrice;
        } elseif ($validated['stock_change'] > 0) {
            // Stock in (pembelian)
            $transactionType = 'purchase';
            $unitPrice = $product->purchase_price ?? $product->price;
            $totalValue = $validated['stock_change'] * $unitPrice;
        }

        // Log the stock change with transaction value
        $product->stockLogs()->create([
            'previous_stock' => $oldStock,
            'new_stock' => $product->stock,
            'change' => $validated['stock_change'],
            'unit_price' => $unitPrice,
            'total_value' => $totalValue,
            'transaction_type' => $transactionType,
            'note' => $validated['note'] ?? 'Stock updated by cashier',
            'user_id' => auth()->id(),
        ]);

        // Log activity
        $changeText = $validated['stock_change'] > 0 ? "+{$validated['stock_change']}" : $validated['stock_change'];
        $actionType = $validated['stock_change'] > 0 ? 'stock_in' : 'stock_out';
        ActivityLogService::log(
            $actionType,
            'inventory',
            "Perubahan stok {$product->name}: {$changeText} (Stok: {$oldStock} → {$product->stock})",
            $product,
            ['stock' => $oldStock],
            ['stock' => $product->stock],
            ['change' => $validated['stock_change'], 'note' => $validated['note'] ?? null]
        );

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

    /**
     * Display stock-out logs (items removed from stock).
     * Only show logs for the current logged-in cashier FROM POS transactions.
     */
    public function stockOut(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'tanggal_terbaru');

        $query = StockLog::with('product', 'user')
            ->where('change', '<', 0)
            ->where('user_id', auth()->id()) // Filter by current user
            ->where('transaction_type', 'sale'); // Only from POS transactions

        // Search filter
        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sortMapping = [
            'tanggal_terbaru' => ['created_at', 'desc'],
            'tanggal_terlama' => ['created_at', 'asc'],
            'jumlah_banyak' => ['change', 'asc'], // change is negative, so asc = most items
            'jumlah_sedikit' => ['change', 'desc'],
        ];

        if (isset($sortMapping[$sort])) {
            $query->orderBy($sortMapping[$sort][0], $sortMapping[$sort][1]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $stockLogs = $query->paginate(8)->appends(request()->query());

        return view('cashier.inventory.stock-out', [
            'logs' => $stockLogs,
            'search' => $search,
            'sort' => $sort
        ]);
    }

    /**
     * Return recent stock-out entries as JSON for polling.
     * Only POS transactions from current cashier.
     */
    public function stockOutUpdates(Request $request)
    {
        $sinceId = $request->input('since_id');

        $query = StockLog::with('product', 'user')
            ->where('change', '<', 0)
            ->where('user_id', auth()->id()) // Filter by current user session
            ->where('transaction_type', 'sale') // Only from POS transactions
            ->orderBy('created_at', 'desc');

        if ($sinceId) {
            // fetch logs newer than given id (assuming id grows with time)
            $query->where('id', '>', (int)$sinceId);
        }

        $logs = $query->limit(50)->get();

        $data = $logs->map(function($l){
            return [
                'id' => $l->id,
                'product' => $l->product ? $l->product->name : '—',
                'category' => $l->product ? $l->product->category : '-',
                'image' => $l->product && $l->product->image ? Storage::url($l->product->image) : null,
                'change' => $l->change,
                'previous_stock' => $l->previous_stock,
                'new_stock' => $l->new_stock,
                'user' => $l->user ? $l->user->name : 'system',
                'note' => $l->note,
                'created_at' => $l->created_at->toDateTimeString(),
                'created_human' => $l->created_at->diffForHumans(),
                'date' => $l->created_at->format('d/m/Y'),
                'time' => $l->created_at->format('H:i'),
            ];
        });

        return response()->json(['logs' => $data]);
    }

    /**
     * Show stock-in form with batch input.
     */
    public function stockIn()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();

        // Get recent batch entries
        $recentBatches = ProductBatch::with('product', 'receivedBy')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('cashier.inventory.stock-in', compact('products', 'recentBatches'));
    }

    /**
     * Store a new batch for stock-in.
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'incoming_unit' => 'nullable|string|in:pcs,pack,renteng,box,karton',
            'purchase_price' => 'nullable|numeric|min:0',
            'date_received' => 'required|date',
            'expiration_date' => 'nullable|date|after_or_equal:date_received',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Convert quantity to pcs (base unit)
        $incomingUnit = $validated['incoming_unit'] ?? 'pcs';
        $incomingQuantity = $validated['quantity'];
        $quantityInPcs = $product->convertToBaseUnit($incomingQuantity, $incomingUnit);

        // Generate batch code
        $batchCode = ProductBatch::generateBatchCode($product->id);

        // Create the batch (quantity stored in pcs)
        $batch = ProductBatch::create([
            'product_id' => $product->id,
            'batch_code' => $batchCode,
            'quantity' => $quantityInPcs,
            'incoming_unit' => $incomingUnit,
            'incoming_quantity' => $incomingQuantity,
            'purchase_price' => $validated['purchase_price'] ?? $product->getPurchasePriceForUnit($incomingUnit),
            'date_received' => $validated['date_received'],
            'expiration_date' => $validated['expiration_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'received_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Update product total stock (in pcs)
        $oldStock = $product->stock;
        $product->increment('stock', $quantityInPcs);

        // Create stock log for this batch
        $unitLabel = ucfirst($incomingUnit);
        StockLog::create([
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'previous_stock' => $oldStock,
            'new_stock' => $product->stock,
            'change' => $quantityInPcs,
            'unit_type' => $incomingUnit,
            'quantity_in_base_unit' => $quantityInPcs,
            'unit_price' => $batch->purchase_price ?? $product->purchase_price ?? 0,
            'total_value' => ($batch->purchase_price ?? $product->purchase_price ?? 0) * $incomingQuantity,
            'transaction_type' => 'purchase',
            'note' => "Batch {$batchCode} - {$incomingQuantity} {$unitLabel}" . ($quantityInPcs != $incomingQuantity ? " ({$quantityInPcs} Pcs)" : ''),
        ]);

        // Log activity
        ActivityLogService::logStockIn(
            "Menambahkan batch {$batchCode} untuk produk {$product->name} (+{$validated['quantity']})",
            $batch,
            [
                'product_name' => $product->name,
                'batch_code' => $batchCode,
                'quantity' => $validated['quantity'],
                'expiration_date' => $validated['expiration_date'] ?? null,
            ]
        );

        return redirect()->route('cashier.inventory.batch.stock-in')
            ->with('success', 'Batch berhasil ditambahkan! Kode Batch: ' . $batchCode);
    }

    /**
     * Show batches for a specific product.
     */
    public function productBatches(Product $product)
    {
        $batches = $product->batches()
            ->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('expiration_date', 'asc')
            ->paginate(10);

        return view('cashier.inventory.batches', compact('product', 'batches'));
    }

    /**
     * Get available batches for a product (API for POS).
     */
    public function getProductBatches(Product $product)
    {
        $batches = $product->batches()
            ->available()
            ->notExpired()
            ->fifo()
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'batch_code' => $batch->batch_code,
                    'quantity' => $batch->quantity,
                    'expiration_date' => $batch->expiration_date ? $batch->expiration_date->format('d M Y') : null,
                    'expiration_status' => $batch->expiration_status,
                    'is_expiring_soon' => $batch->isExpiringSoon(),
                    'is_expired' => $batch->isExpired(),
                    'days_until_expiration' => $batch->daysUntilExpiration(),
                    'purchase_price' => $batch->purchase_price,
                ];
            });

        // Get available units for this product
        $units = $product->available_units;

        // If no units configured, default to pcs only
        if (empty($units)) {
            $units = [
                [
                    'type' => 'pcs',
                    'label' => 'Pcs',
                    'price' => $product->price_pcs ?? $product->price ?? 0,
                ]
            ];
        }

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'total_stock' => $product->effective_stock,
            'default_price' => $product->price ?? 0,
            'units' => $units,
            'batches' => $batches,
        ]);
    }
}
