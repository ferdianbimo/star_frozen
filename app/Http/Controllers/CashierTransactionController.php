<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierTransactionController extends Controller
{
    /**
     * Display transaction history for the logged-in cashier.
     * Shows only transactions made by the current cashier.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sort = $request->input('sort', 'terbaru');
        
        $query = Transaction::with('items.product', 'user')
            ->where('user_id', auth()->id()); // Filter by current cashier
        
        // Search filter (transaction ID or invoice number)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $search . '%');
            });
        }
        
        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Sorting
        $sortMapping = [
            'terbaru' => ['created_at', 'desc'],
            'terlama' => ['created_at', 'asc'],
            'total_besar' => ['total', 'desc'],
            'total_kecil' => ['total', 'asc'],
        ];
        
        if (isset($sortMapping[$sort])) {
            $query->orderBy($sortMapping[$sort][0], $sortMapping[$sort][1]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(20)->withQueryString();
        
        // Calculate statistics berdasarkan filter aktif
        // Jika tidak ada filter tanggal, tampilkan semua data kasir (tidak dibatasi bulan)
        $statsQuery = Transaction::where('user_id', auth()->id());

        if ($dateFrom) {
            $statsQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $statsQuery->whereDate('created_at', '<=', $dateTo);
        }

        $stats = $statsQuery->selectRaw('
                COUNT(*) as total_transactions,
                COALESCE(SUM(total), 0) as total_sales,
                COALESCE(SUM(total - discount), 0) as net_sales,
                COALESCE(AVG(total), 0) as average_transaction
            ')
            ->first();

        // Calculate monthly totals for grouping headers (semua data kasir)
        $monthlyQuery = Transaction::where('user_id', auth()->id());
        
        // Apply same filters to monthly totals
        if ($dateFrom) {
            $monthlyQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $monthlyQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        $monthlyTotals = $monthlyQuery
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
            ->get()
            ->keyBy('month_key');

        return view('cashier.transactions.index', compact('transactions', 'stats', 'monthlyTotals'));
    }
    
    /**
     * Show transaction detail
     */
    public function show($id)
    {
        $transaction = Transaction::with('items.product', 'user')
            ->where('user_id', auth()->id()) // Only own transactions
            ->findOrFail($id);
        
        return view('cashier.transactions.show', compact('transaction'));
    }
}
