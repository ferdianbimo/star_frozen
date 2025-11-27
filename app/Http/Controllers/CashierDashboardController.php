<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CashierDashboardController extends Controller
{
    public function index()
    {
        // Daily Sales (Today)
        $dailySales = Transaction::whereDate('created_at', now()->toDateString())->sum('total_amount');

        // Yesterday sales for comparison
        $yesterdaySales = Transaction::whereRaw('DATE(COALESCE(checkout_time, created_at)) = ?', [now()->subDay()->toDateString()])->sum('total_amount');

        $dailyPercentage = $yesterdaySales > 0
            ? round((($dailySales - $yesterdaySales) / $yesterdaySales) * 100, 2)
            : 0;

        // Transactions count today
        $transactionsCount = Transaction::whereDate('created_at', now()->toDateString())->count();

        // Items sold today (sum of quantities)
        $itemsSold = TransactionItem::whereHas('transaction', function ($q) {
            $q->whereDate('created_at', now()->toDateString());
        })->sum('quantity');

        // Low Stock Products (small list) - items with stock <= 10
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        // Expiring Soon (approx 6 months shelf-life heuristic)
        $expiringProducts = Product::where('created_at', '<=', now()->subMonths(5)->subDays(23))
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // Sales Trend (Last 7 days)
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->locale('id')->isoFormat('ddd');
            $sales = Transaction::whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_amount');

            $salesTrend[] = [
                'day' => $dayName,
                'sales' => $sales
            ];
        }

        // Monthly Sales (This Month) and comparison to last month
        // This value is the total for the current calendar month and will naturally reset when month changes.
        $monthlySales = Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        $lastMonthSales = Transaction::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_amount');

        $monthlyPercentage = $lastMonthSales > 0
            ? round((($monthlySales - $lastMonthSales) / $lastMonthSales) * 100, 2)
            : 0;

        // Monthly totals for the current year (use checkout_time if present, else created_at)
        $year = now()->year;

        // Build monthly totals as an array [1=>totalJan, 2=>totalFeb, ...]
        $rows = Transaction::selectRaw("MONTH(COALESCE(checkout_time, created_at)) as month, SUM(total_amount) as total")
            ->whereRaw('YEAR(COALESCE(checkout_time, created_at)) = ?', [$year])
            ->groupByRaw('MONTH(COALESCE(checkout_time, created_at))')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $monthlyTotals = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyTotals[$m] = isset($rows[$m]) ? (float) $rows[$m] : 0.0;
        }

        // Cumulative totals up to each month (useful if you want running accumulation)
        $cumulative = [];
        $running = 0;
        for ($m = 1; $m <= 12; $m++) {
            $running += $monthlyTotals[$m];
            $cumulative[$m] = $running;
        }

        // Attach both arrays so the view can choose monthly or cumulative display
        $monthlyTotals = ['monthly' => $monthlyTotals, 'cumulative' => $cumulative];

        // Low stock counts: number of distinct products with stock <= 10
        $lowStockCount = Product::where('stock', '<=', 10)
            ->count();

        // Expiring Soon: use `expiration_date` when available. Find products whose
        // `expiration_date` falls within the next 7 days from now.
        $now = now();
        $oneWeek = now()->addDays(7);

        // If the products table has an `expiration_date`, use it; otherwise fall back
        // to the old heuristic (created_at + 6 months).
        $hasExpirationColumn = Schema::hasColumn('products', 'expiration_date');

        if ($hasExpirationColumn) {
            $expiringCount = Product::whereBetween('expiration_date', [
                $now->toDateString(),
                $oneWeek->toDateString()
            ])->count();

            $expiringProducts = Product::whereBetween('expiration_date', [
                $now->toDateString(),
                $oneWeek->toDateString()
            ])->orderBy('expiration_date', 'asc')
                ->limit(5)
                ->get()
                ->map(function($p) use ($now) {
                    $expiryDate = $p->expiration_date ? \Carbon\Carbon::parse($p->expiration_date) : $p->created_at->copy()->addMonths(6);
                    $p->expiry_date = $expiryDate;
                    $p->remaining_days = $now->diffInDays($expiryDate, false);
                    return $p;
                });
        } else {
            $expiringCount = Product::whereRaw("DATE_ADD(created_at, INTERVAL 6 MONTH) BETWEEN ? AND ?", [
                $now->toDateString(),
                $oneWeek->toDateString()
            ])->count();

            $expiringProducts = Product::whereRaw("DATE_ADD(created_at, INTERVAL 6 MONTH) BETWEEN ? AND ?", [
                $now->toDateString(),
                $oneWeek->toDateString()
            ])->orderByRaw('DATE_ADD(created_at, INTERVAL 6 MONTH) ASC')
                ->limit(5)
                ->get()
                ->map(function($p) use ($now) {
                    $expiryDate = $p->created_at->copy()->addMonths(6);
                    $p->expiry_date = $expiryDate;
                    $p->remaining_days = $now->diffInDays($expiryDate, false);
                    return $p;
                });
        }

        // Stock almost out list (<=10) for quick view
        $stockAlmostOut = Product::where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        // Recent Transactions (latest 5)
        $recentTransactions = Transaction::withCount(['items as items_count' => function ($q) {
            $q->select(\DB::raw('coalesce(sum(quantity),0)'));
        }])->orderBy('created_at', 'desc')->limit(5)->get();

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
            'stockAlmostOut'
        ));
    }
}
