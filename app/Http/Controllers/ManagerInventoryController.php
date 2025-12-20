<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ManagerInventoryController extends Controller
{
    public function index(Request $request)
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
    
    public function stockOut(Request $request)
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

    /**
     * Show batches overview.
     */
    public function batches(Request $request)
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

    /**
     * Show activity logs.
     */
    public function activityLogs(Request $request)
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
}
