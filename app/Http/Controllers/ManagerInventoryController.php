<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\Request;

class ManagerInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        
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
        ];
        
        if (isset($sortMapping[$sort])) {
            [$column, $direction] = $sortMapping[$sort];
            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        $products = $query->paginate(10)->withQueryString();
        
        // Get stock in logs (positive changes)
        $stockInLogs = StockLog::where('change', '>', 0)
            ->with('product', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('manager.inventory.index', compact('products', 'stockInLogs'));
    }
    
    public function stockOut(Request $request)
    {
        $query = Product::query();
        
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
        ];
        
        if (isset($sortMapping[$sort])) {
            [$column, $direction] = $sortMapping[$sort];
            $query->orderBy($column, $direction);
        } else {
            $query->orderBy('name', 'asc');
        }
        
        $products = $query->paginate(10)->withQueryString();
        
        // Get stock out logs (negative changes) with pagination
        $stockLogs = StockLog::where('change', '<', 0)
            ->with('product', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        return view('manager.inventory.stock-out', compact('products', 'stockLogs'));
    }
}
