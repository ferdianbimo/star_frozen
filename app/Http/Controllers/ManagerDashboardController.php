<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ManagerDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get period from request (default: 7 days)
        $period = $request->input('period', 7);
        $period = in_array($period, [7, 30]) ? (int)$period : 7;

        // Daily Sales (Today) - dari StockLog untuk data realtime
        $dailySales = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', today())
            ->sum('total_value');
        
        // Fallback to transactions if no stock_logs data
        if ($dailySales == 0) {
            $dailySales = Transaction::whereDate('created_at', today())->sum('total_amount');
        }
        
        // Daily Sales Yesterday for comparison
        $yesterdaySales = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', today()->subDay())
            ->sum('total_value');
        
        if ($yesterdaySales == 0) {
            $yesterdaySales = Transaction::whereDate('created_at', today()->subDay())->sum('total_amount');
        }
        
        $dailyPercentage = $yesterdaySales > 0 
            ? round((($dailySales - $yesterdaySales) / $yesterdaySales) * 100, 2)
            : ($dailySales > 0 ? 100 : 0);
        
        // Monthly Sales - PERIODE 30 HARI TERAKHIR (bukan bulan kalender)
        $monthlySales = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', '>=', now()->subDays(30)->toDateString())
            ->whereDate('created_at', '<=', now()->toDateString())
            ->sum('total_value');
        
        // Fallback ke transactions jika kosong
        if ($monthlySales == 0) {
            $monthlySales = Transaction::whereDate('created_at', '>=', now()->subDays(30))
                ->whereDate('created_at', '<=', now())
                ->sum('total_amount');
        }
        
        // Last 30 days (periode 31-60 hari yang lalu) untuk comparison
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
        
        // Low Stock Products - use per-product low_stock_threshold when available, fallback to 10
        $lowStockQuery = Product::where('stock', '>', 0)
            ->where(function($q) {
                $q->whereColumn('stock', '<=', 'low_stock_threshold')
                  ->orWhereRaw('stock <= ?', [10]);
            });

        $lowStockCount = (clone $lowStockQuery)->count();

        $lowStockProducts = (clone $lowStockQuery)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
        
        // Expiring Soon: prefer `expiration_date` when available, otherwise fallback to created_at + 6 months
        $now = now();
        $oneWeekFromNow = now()->addDays(7);
        $hasExpirationColumn = Schema::hasColumn('products', 'expiration_date');

        if ($hasExpirationColumn) {
            // Produk yang kadaluarsa dalam 7 hari ke depan
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
            // Fallback: gunakan created_at + 6 bulan
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
        
        // Sales Trend (Last X days based on period) - dari StockLog
        $salesTrend = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            // For 7 days: show day name (Sen, Sel, etc)
            // For 30 days: show date (01 Des, 02 Des, etc)
            if ($period == 7) {
                $dayName = $date->locale('id')->isoFormat('ddd');
            } else {
                $dayName = $date->locale('id')->isoFormat('DD MMM');
            }
            
            // Get sales from StockLog first
            $sales = StockLog::where('transaction_type', 'sale')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_value');
            
            // Fallback to transactions if needed
            if ($sales == 0) {
                $sales = Transaction::whereDate('created_at', $date->format('Y-m-d'))
                    ->sum('total_amount');
            }
            
            $salesTrend[] = [
                'day' => $dayName,
                'sales' => $sales
            ];
        }
        
        // Stock Almost Out (Stok Hampir Habis) - include stock 0
        $stockAlmostOut = Product::where('stock', '>=', 0)
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->get();
        
        // Financial Data - Real-time dari Stock Logs
        $todayIncome = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', today())
            ->sum('total_value');
        
        $monthlyIncome = StockLog::where('transaction_type', 'sale')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_value');
        
        $monthlyExpenses = Expense::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
        
        $monthlyProfit = $monthlyIncome - $monthlyExpenses;
        $profitMargin = $monthlyIncome > 0 ? round(($monthlyProfit / $monthlyIncome) * 100, 2) : 0;
        
        // Top 5 Products by Revenue this month
        $topProducts = DB::table('stock_logs')
            ->join('products', 'stock_logs.product_id', '=', 'products.id')
            ->where('stock_logs.transaction_type', 'sale')
            ->whereMonth('stock_logs.created_at', now()->month)
            ->whereYear('stock_logs.created_at', now()->year)
            ->select('products.name', 'products.image',
                     DB::raw('SUM(ABS(stock_logs.change)) as total_quantity'),
                     DB::raw('SUM(stock_logs.total_value) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        // Recent Sales Transactions (last 5)
        $recentSales = StockLog::with(['product', 'user'])
            ->where('transaction_type', 'sale')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('manager.dashboard', compact(
            'dailySales',
            'dailyPercentage',
            'monthlySales',
            'monthlyPercentage',
            'lowStockCount',
            'lowStockProducts',
            'expiringCount',
            'expiringProducts',
            'salesTrend',
            'stockAlmostOut',
            'period',
            'todayIncome',
            'monthlyIncome',
            'monthlyExpenses',
            'monthlyProfit',
            'profitMargin',
            'topProducts',
            'recentSales'
        ));
    }
}
