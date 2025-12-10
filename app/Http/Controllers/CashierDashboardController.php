<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CashierDashboardController extends Controller
{
    public function index(Request $request)
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

        // Expiring Soon - produk yang kadaluarsa dalam 7 hari
        $now = now();
        $oneWeekFromNow = now()->addDays(7);
        $hasExpirationColumn = Schema::hasColumn('products', 'expiration_date');

        if ($hasExpirationColumn) {
            $expiringCount = Product::whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now->toDateString())
                ->where('expiration_date', '<=', $oneWeekFromNow->toDateString())
                ->count();

            $expiringProducts = Product::whereNotNull('expiration_date')
                ->where('expiration_date', '>=', $now->toDateString())
                ->where('expiration_date', '<=', $oneWeekFromNow->toDateString())
                ->orderBy('expiration_date', 'asc')
                ->limit(5)
                ->get()
                ->map(function($p) {
                    $now = now();
                    $expiryDate = Carbon::parse($p->expiration_date);
                    $p->expiry_date = $expiryDate;
                    $p->remaining_days = $now->diffInDays($expiryDate, false);
                    return $p;
                });
        } else {
            $expiringCount = Product::whereRaw("DATE_ADD(created_at, INTERVAL 6 MONTH) >= ?", [$now->toDateString()])
                ->whereRaw("DATE_ADD(created_at, INTERVAL 6 MONTH) <= ?", [$oneWeekFromNow->toDateString()])
                ->count();

            $expiringProducts = Product::whereRaw("DATE_ADD(created_at, INTERVAL 6 MONTH) >= ?", [$now->toDateString()])
                ->whereRaw("DATE_ADD(created_at, INTERVAL 6 MONTH) <= ?", [$oneWeekFromNow->toDateString()])
                ->orderByRaw('DATE_ADD(created_at, INTERVAL 6 MONTH) ASC')
                ->limit(5)
                ->get()
                ->map(function($p) {
                    $now = now();
                    $expiryDate = $p->created_at->copy()->addMonths(6);
                    $p->expiry_date = $expiryDate;
                    $p->remaining_days = $now->diffInDays($expiryDate, false);
                    return $p;
                });
        }

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
