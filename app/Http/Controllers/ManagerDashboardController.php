<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        // Daily Sales (Today)
        $dailySales = Transaction::whereDate('created_at', today())->sum('total_amount');
        
        // Daily Sales Yesterday for comparison
        $yesterdaySales = Transaction::whereDate('created_at', today()->subDay())->sum('total_amount');
        
        $dailyPercentage = $yesterdaySales > 0 
            ? round((($dailySales - $yesterdaySales) / $yesterdaySales) * 100, 2)
            : 0;
        
        // Monthly Sales (This Month)
        $monthlySales = Transaction::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');
        
        // Last Month Sales for comparison
        $lastMonthSales = Transaction::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_amount');
        
        $monthlyPercentage = $lastMonthSales > 0
            ? round((($monthlySales - $lastMonthSales) / $lastMonthSales) * 100, 2)
            : 0;
        
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
        $oneWeek = now()->addDays(7);
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
                    $expiryDate = $p->expiration_date ? Carbon::parse($p->expiration_date) : $p->created_at->copy()->addMonths(6);
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
        
        // Sales Trend (Last 7 days)
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->locale('id')->isoFormat('ddd'); // Short day name in Indonesian
            $sales = Transaction::whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_amount');
            
            $salesTrend[] = [
                'day' => $dayName,
                'sales' => $sales
            ];
        }
        
        // Stock Almost Out (Stok Hampir Habis)
        $stockAlmostOut = Product::where('stock', '>', 0)
            ->where('stock', '<=', 10)
            ->orderBy('stock', 'asc')
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
            'stockAlmostOut'
        ));
    }
}
