<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Carbon\Carbon;

/**
 * CashierDashboardController - Dashboard utama untuk Kasir.
 *
 * Controller ini menyediakan data dashboard untuk kasir:
 * - Ringkasan penjualan harian dengan perbandingan kemarin
 * - Jumlah transaksi dan item terjual hari ini
 * - Trend penjualan (7 atau 30 hari)
 * - Produk dengan stok rendah dan kadaluarsa
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class CashierDashboardController extends Controller
{
    /**
     * Menampilkan dashboard kasir.
     *
     * Mengambil data:
     * - Penjualan harian dengan perbandingan kemarin
     * - Jumlah transaksi dan item terjual
     * - Penjualan bulanan (30 hari terakhir)
     * - Trend penjualan berdasarkan periode (7/30 hari)
     * - Produk low stock dan expiring soon
     *
     * @param  Request $request Request dengan parameter period
     * @return View
     */
    public function index(Request $request): View
    {
        // Get period from request (default: 7 days)
        $period = $request->input('period', 7);
        $period = in_array($period, [7, 30]) ? (int)$period : 7;

        // Daily Sales (Today) - dari stock_logs
        $dailySales = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', today())
            ->sum('total_value');
        
        // Fallback ke transactions jika stock_logs kosong
        if ($dailySales == 0) {
            $dailySales = Transaction::whereDate('created_at', today())->sum('total_amount');
        }

        // Yesterday sales for comparison
        $yesterdaySales = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', now()->subDay()->toDateString())
            ->sum('total_value');
        
        if ($yesterdaySales == 0) {
            $yesterdaySales = Transaction::whereDate('created_at', now()->subDay()->toDateString())
                ->sum('total_amount');
        }

        $dailyPercentage = $yesterdaySales > 0
            ? round((($dailySales - $yesterdaySales) / $yesterdaySales) * 100, 2)
            : ($dailySales > 0 ? 100 : 0);

        // Transactions count today
        $transactionsCount = Transaction::whereDate('created_at', today())->count();

        // Items sold today (sum of quantities from stock_logs)
        $itemsSold = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', today())
            ->selectRaw('SUM(ABS(`change`)) as total')
            ->value('total') ?? 0;
        
        if ($itemsSold == 0) {
            $itemsSold = TransactionItem::whereHas('transaction', function ($q) {
                $q->whereDate('created_at', today());
            })->sum('quantity');
        }

        // Monthly Sales - PERIODE 30 HARI TERAKHIR (bukan bulan kalender)
        $monthlySales = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
            ->whereDate('created_at', '<=', now()->toDateString())
            ->sum('total_value');
        
        // Fallback ke transactions jika stock_logs kosong
        if ($monthlySales == 0) {
            $monthlySales = Transaction::whereDate('created_at', '>=', now()->subDays(30))
                ->whereDate('created_at', '<=', now())
                ->sum('total_amount');
        }

        // Last 30 days (untuk comparison) - periode 31-60 hari yang lalu
        $lastMonthSales = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', '>=', now()->subDays(60)->toDateString())
            ->whereDate('created_at', '<', now()->subDays(30)->toDateString())
            ->sum('total_value');
        
        if ($lastMonthSales == 0) {
            $lastMonthSales = Transaction::whereDate('created_at', '>=', now()->subDays(60))
                ->whereDate('created_at', '<', now()->subDays(30))
                ->sum('total_amount');
        }

        $monthlyPercentage = $lastMonthSales > 0
            ? round((($monthlySales - $lastMonthSales) / $lastMonthSales) * 100, 2)
            : ($monthlySales > 0 ? 100 : 0);

        // Sales Trend (Last X days) - dari stock_logs dengan fallback
        $salesTrend = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            if ($period == 7) {
                $dayName = $date->locale('id')->isoFormat('ddd');
            } else {
                $dayName = $date->locale('id')->isoFormat('DD MMM');
            }
            
            // Coba dari stock_logs dulu
            $sales = StockLog::where('transaction_type', 'sale')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_value');
            
            // Fallback ke transactions jika kosong
            if ($sales == 0) {
                $sales = Transaction::whereDate('created_at', $date->format('Y-m-d'))
                    ->sum('total_amount');
            }

            $salesTrend[] = [
                'day' => $dayName,
                'sales' => $sales
            ];
        }

        // Low Stock Products
        $lowStockCount = Product::where('stock', '>', 0)
            ->where(function($q) {
                $q->whereColumn('stock', '<=', 'low_stock_threshold')
                  ->orWhereRaw('stock <= ?', [10]);
            })->count();

        $lowStockProducts = Product::where('stock', '>', 0)
            ->where(function($q) {
                $q->whereColumn('stock', '<=', 'low_stock_threshold')
                  ->orWhereRaw('stock <= ?', [10]);
            })
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        // Expiring Soon - ambil dari product_batches yang akan kadaluarsa dalam 7 hari
        $now = now();
        $oneWeekFromNow = now()->addDays(7);
        
        // Ambil batch yang akan kadaluarsa dalam 7 hari (tidak termasuk yang sudah kadaluarsa)
        $expiringBatches = \App\Models\ProductBatch::with('product')
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '>=', $now->toDateString())
            ->where('expiration_date', '<=', $oneWeekFromNow->toDateString())
            ->where('quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('expiration_date', 'asc')
            ->get();
        
        $expiringCount = $expiringBatches->count();
        
        // Group by product dan ambil batch terdekat per produk
        $expiringProducts = $expiringBatches->groupBy('product_id')
            ->map(function($batches) use ($now) {
                $batch = $batches->first(); // Batch terdekat
                $product = $batch->product;
                $expiryDate = Carbon::parse($batch->expiration_date);
                
                $product->expiry_date = $expiryDate;
                $product->remaining_days = $now->diffInDays($expiryDate, false);
                $product->batch_code = $batch->batch_code;
                $product->batch_quantity = $batch->quantity;
                
                return $product;
            })
            ->sortBy('expiry_date')
            ->take(5)
            ->values();

        // Stock almost out list
        $stockAlmostOut = Product::where('stock', '>=', 0)
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->get();

        // Recent Transactions
        $recentTransactions = Transaction::withCount(['items as items_count' => function ($q) {
            $q->select(DB::raw('coalesce(sum(quantity),0)'));
        }])->orderBy('created_at', 'desc')->limit(5)->get();

        // Monthly totals for chart (optional)
        $year = now()->year;
        $rows = StockLog::where('transaction_type', 'sale')
            ->selectRaw("MONTH(created_at) as month, SUM(total_value) as total")
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $monthlyTotals = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyTotals[$m] = isset($rows[$m]) ? (float) $rows[$m] : 0.0;
        }

        $cumulative = [];
        $running = 0;
        for ($m = 1; $m <= 12; $m++) {
            $running += $monthlyTotals[$m];
            $cumulative[$m] = $running;
        }

        $monthlyTotals = ['monthly' => $monthlyTotals, 'cumulative' => $cumulative];

        return view('cashier.dashboard', compact(
            'dailySales',
            'transactionsCount',
            'itemsSold',
            'lowStockProducts',
            'expiringProducts',
            'salesTrend',
            'recentTransactions',
            'monthlySales',
            'monthlyPercentage',
            'monthlyTotals',
            'dailyPercentage',
            'lowStockCount',
            'expiringCount',
            'stockAlmostOut',
            'period'
        ));
    }
}
