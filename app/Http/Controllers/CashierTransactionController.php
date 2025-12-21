<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * CashierTransactionController - Mengelola riwayat transaksi kasir.
 *
 * Controller ini menangani:
 * - Daftar transaksi yang dibuat oleh kasir yang sedang login
 * - Filter berdasarkan pencarian dan rentang tanggal
 * - Statistik transaksi kasir
 * - Detail transaksi
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class CashierTransactionController extends Controller
{
    /**
     * Menampilkan riwayat transaksi kasir.
     *
     * Menampilkan transaksi dengan fitur:
     * - Filter berdasarkan ID atau nomor invoice
     * - Filter berdasarkan rentang tanggal
     * - Sorting (terbaru, terlama, total besar/kecil)
     * - Statistik: total transaksi, total sales, net sales, average
     * - Grouping berdasarkan bulan
     *
     * Hanya menampilkan transaksi milik kasir yang sedang login.
     *
     * @param  Request $request Request dengan parameter filter
     * @return View
     */
    public function index(Request $request): View
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
     * Menampilkan detail transaksi.
     *
     * @param  int $id ID transaksi
     * @return View
     */
    public function show($id): View
    {
        $transaction = Transaction::with('items.product', 'user')
            ->where('user_id', auth()->id()) // Only own transactions
            ->findOrFail($id);
        
        return view('cashier.transactions.show', compact('transaction'));
    }
}
