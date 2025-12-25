<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ManagerInventoryController - Mengelola inventory untuk Manager.
 *
 * Controller ini menangani:
 * - Daftar produk dengan search, sort, dan pagination
 * - Manajemen stock out (pengeluaran stok)
 * - Overview dan manajemen batch produk
 * - Riwayat activity logs
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class ManagerInventoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - INVENTORY MANAGEMENT
    |--------------------------------------------------------------------------
    |
    | Method untuk menampilkan dan mengelola data inventory.
    |
    */

    /**
     * Menampilkan halaman utama inventory.
     *
     * Menampilkan daftar produk dengan fitur:
     * - Search berdasarkan nama, kategori, atau barcode
     * - Sorting (nama, stok, harga, tanggal, kadaluarsa)
     * - Pagination 10 item per halaman
     * - Statistik ringkasan inventory
     *
     * @param  Request $request Request dengan parameter search dan sort
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Product::query()->with('batches');
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sort mapping
        $sort = $request->get('sort', 'name_asc');
        $sortMapping = [
            'name_asc' => ['name', 'asc'],
            'name_desc' => ['name', 'desc'],
            'stock_low' => ['stock', 'asc'],
            'stock_high' => ['stock', 'desc'],
            'price_low' => ['price', 'asc'],
            'price_high' => ['price', 'desc'],
            'newest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'expiration_asc' => ['expiration_date', 'asc'],
            'expiration_desc' => ['expiration_date', 'desc'],
        ];
        
        if (isset($sortMapping[$sort])) {
            [$column, $direction] = $sortMapping[$sort];
            if ($column === 'expiration_date') {
                $query->orderByRaw("(expiration_date IS NULL), expiration_date $direction");
            } else {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('name', 'asc');
        }
        
        $products = $query->paginate(10)->withQueryString();
        
        // Get recent batch entries
        $recentBatches = ProductBatch::with('product', 'receivedBy')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get stock in logs (positive changes)
        $stockInLogs = StockLog::where('change', '>', 0)
            ->with('product', 'user', 'batch')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Summary statistics
        $stats = [
            'total_products' => Product::count(),
            'total_stock' => Product::sum('stock'),
            'low_stock_count' => Product::whereRaw('stock <= low_stock_threshold')->count(),
            'total_batches' => ProductBatch::where('is_active', true)->count(),
            'expiring_soon' => ProductBatch::where('is_active', true)
                                ->where('quantity', '>', 0)
                                ->whereNotNull('expiration_date')
                                ->where('expiration_date', '<=', now()->addDays(7))
                                ->where('expiration_date', '>=', now())
                                ->count(),
            'expired' => ProductBatch::where('is_active', true)
                        ->where('quantity', '>', 0)
                        ->whereNotNull('expiration_date')
                        ->where('expiration_date', '<', now())
                        ->count(),
        ];
        
        return view('manager.inventory.index', compact('products', 'stockInLogs', 'recentBatches', 'stats'));
    }

    /**
     * Menampilkan halaman stock out (pengeluaran stok).
     *
     * Menampilkan log pengeluaran stok dengan fitur:
     * - Search berdasarkan nama produk, kategori, atau barcode
     * - Filter berdasarkan user, tanggal dari, dan tanggal sampai
     * - Sorting (terbaru, terlama, qty tinggi/rendah)
     * - Statistik pengeluaran harian, mingguan, dan bulanan
     *
     * @param  Request $request Request dengan parameter filter
     * @return View
     */
    public function stockOut(Request $request): View
    {
        // Get stock out logs (negative changes) with pagination
        $query = StockLog::where('change', '<', 0)
            ->with('product', 'user', 'batch');
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'qty_high':
                $query->orderByRaw('ABS(`change`) DESC');
                break;
            case 'qty_low':
                $query->orderByRaw('ABS(`change`) ASC');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $stockLogs = $query->paginate(15)->withQueryString();
        
        // Stats for stock out
        $stats = [
            'total_out_today' => StockLog::where('change', '<', 0)->whereDate('created_at', today())->sum(\DB::raw('ABS(`change`)')),
            'total_out_week' => StockLog::where('change', '<', 0)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum(\DB::raw('ABS(`change`)')),
            'total_out_month' => StockLog::where('change', '<', 0)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum(\DB::raw('ABS(`change`)')),
            'transactions_today' => StockLog::where('change', '<', 0)->where('transaction_type', 'sale')->whereDate('created_at', today())->count(),
            'total_value_today' => StockLog::where('change', '<', 0)->whereDate('created_at', today())->sum('total_value'),
            'total_value_month' => StockLog::where('change', '<', 0)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_value'),
        ];
        
        // Get users for filter
        $users = \App\Models\User::select('id', 'name')->orderBy('name')->get();
        
        return view('manager.inventory.stock-out', compact('stockLogs', 'stats', 'users'));
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - BATCH MANAGEMENT
    |--------------------------------------------------------------------------
    |
    | Method untuk menampilkan dan mengelola batch produk.
    |
    */

    /**
     * Menampilkan overview batch produk.
     *
     * Menampilkan daftar batch dengan fitur:
     * - Filter status: all, expiring, expired, available, empty
     * - Search berdasarkan batch code atau nama produk
     * - Statistik jumlah batch per status
     * - Sorted by expiration date (terdekat duluan)
     *
     * @param  Request $request Request dengan parameter status dan search
     * @return View
     */
    public function batches(Request $request): View
    {
        $query = ProductBatch::with('product', 'receivedBy')->where('is_active', true);
        
        // Filter by status
        $status = $request->get('status', 'all');
        switch ($status) {
            case 'expiring':
                $query->where('quantity', '>', 0)
                      ->whereNotNull('expiration_date')
                      ->where('expiration_date', '<=', now()->addDays(7))
                      ->where('expiration_date', '>=', now());
                break;
            case 'expired':
                $query->whereNotNull('expiration_date')
                      ->where('expiration_date', '<', now());
                break;
            case 'available':
                $query->where('quantity', '>', 0)
                      ->where(function($q) {
                          $q->whereNull('expiration_date')
                            ->orWhere('expiration_date', '>=', now());
                      });
                break;
            case 'empty':
                $query->where('quantity', '<=', 0);
                break;
        }
        
        // Search by product name or batch code
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_code', 'like', '%' . $search . '%')
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $batches = $query->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END')
                         ->orderBy('expiration_date', 'asc')
                         ->paginate(15)
                         ->withQueryString();
        
        // Stats
        $stats = [
            'total' => ProductBatch::where('is_active', true)->count(),
            'available' => ProductBatch::where('is_active', true)
                            ->where('quantity', '>', 0)
                            ->where(function($q) {
                                $q->whereNull('expiration_date')
                                  ->orWhere('expiration_date', '>=', now());
                            })->count(),
            'expiring' => ProductBatch::where('is_active', true)
                            ->where('quantity', '>', 0)
                            ->whereNotNull('expiration_date')
                            ->where('expiration_date', '<=', now()->addDays(7))
                            ->where('expiration_date', '>=', now())
                            ->count(),
            'expired' => ProductBatch::where('is_active', true)
                          ->whereNotNull('expiration_date')
                          ->where('expiration_date', '<', now())
                          ->count(),
        ];
        
        return view('manager.inventory.batches', compact('batches', 'stats', 'status'));
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - ACTIVITY LOGS
    |--------------------------------------------------------------------------
    |
    | Method untuk menampilkan riwayat aktivitas sistem.
    |
    */

    /**
     * Menampilkan halaman activity logs.
     *
     * Menampilkan riwayat aktivitas dengan fitur:
     * - Filter berdasarkan module (inventory, pos, finance, etc)
     * - Filter berdasarkan action (create, update, delete, etc)
     * - Filter berdasarkan user
     * - Filter berdasarkan rentang tanggal
     * - Pagination 20 item per halaman
     *
     * @param  Request $request Request dengan parameter filter
     * @return View
     */
    public function activityLogs(Request $request): View
    {
        $query = ActivityLog::with('user');
        
        // Filter by module
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }
        
        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')
                      ->paginate(20)
                      ->withQueryString();
        
        // Get unique modules and actions for filters
        $modules = ActivityLog::select('module')->distinct()->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $users = \App\Models\User::select('id', 'name')->orderBy('name')->get();
        
        return view('manager.activity-logs.index', compact('logs', 'modules', 'actions', 'users'));
    }

    /**
     * Menampilkan history penghapusan batch.
     *
     * Menampilkan daftar batch yang telah dihapus oleh kasir dengan informasi:
     * - Produk yang dihapus
     * - Kode batch
     * - Jumlah stok yang dihapus
     * - User yang menghapus
     * - Waktu penghapusan
     * - Alasan/catatan
     *
     * @param  Request $request Request dengan parameter filter dan search
     * @return View
     */
    public function batchDeletionHistory(Request $request): View
    {
        $query = ActivityLog::with(['user'])
                    ->where('action', 'batch_deleted')
                    ->where('module', 'batch');
        
        // Search by batch code or product name
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by user (kasir)
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $deletions = $query->orderBy('created_at', 'desc')
                          ->paginate(20)
                          ->withQueryString();
        
        // Get list of users (kasir) for filter
        $users = \App\Models\User::select('id', 'name')
                    ->orderBy('name')
                    ->get();
        
        // Get statistics
        $stats = [
            'total_deletions' => ActivityLog::where('action', 'batch_deleted')
                                            ->where('module', 'batch')
                                            ->count(),
            'today_deletions' => ActivityLog::where('action', 'batch_deleted')
                                            ->where('module', 'batch')
                                            ->whereDate('created_at', today())
                                            ->count(),
            'this_week_deletions' => ActivityLog::where('action', 'batch_deleted')
                                                ->where('module', 'batch')
                                                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                                ->count(),
        ];
        
        return view('manager.inventory.batch-deletion-history', compact('deletions', 'users', 'stats'));
    }
}
