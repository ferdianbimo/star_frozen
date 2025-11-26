<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        
        // Low Stock Products
        $lowStockCount = Product::whereRaw('stock <= low_stock_threshold')
            ->where('stock', '>', 0)
            ->count();
        
        $lowStockProducts = Product::whereRaw('stock <= low_stock_threshold')
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
        
        // Expiring Soon (products created more than 5 months 23 days ago - assuming 6 months shelf life)
        $expiringCount = Product::where('created_at', '<=', now()->subMonths(5)->subDays(23))
            ->count();
        
        $expiringProducts = Product::where('created_at', '<=', now()->subMonths(5)->subDays(23))
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();
        
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
