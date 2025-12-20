<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        // Default periode: 1 bulan terakhir (bukan startOfMonth)
        // Ini memastikan data transaksi bulan November termasuk
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        // Debug: Log untuk melihat periode yang digunakan
        \Log::info('Finance Index - Period', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        // Calculate income from sales in stock_logs (transaction_type = 'sale')
        // Menggunakan whereDate untuk memastikan tanggal dibandingkan dengan benar
        $totalIncome = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_value');
        
        // Debug: Log total income
        \Log::info('Total Income', [
            'total' => $totalIncome,
            'count' => StockLog::where('transaction_type', 'sale')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count()
        ]);
        
        $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $prevStartDate = Carbon::parse($startDate)->subDays($daysDiff);
        $prevEndDate = Carbon::parse($endDate)->subDays($daysDiff);
        
        // Previous period income - juga dari stock_logs
        $prevIncome = StockLog::where('transaction_type', 'sale')
            ->whereDate('created_at', '>=', $prevStartDate)
            ->whereDate('created_at', '<=', $prevEndDate)
            ->sum('total_value');
        
        // Calculate percentage change
        $incomePercentage = $prevIncome > 0 
            ? round((($totalIncome - $prevIncome) / $prevIncome) * 100, 2)
            : ($totalIncome > 0 ? 100 : 0);
        
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $prevExpenses = Expense::whereBetween('expense_date', [$prevStartDate, $prevEndDate])
            ->sum('amount');
        
        $expensePercentage = $prevExpenses > 0
            ? round((($totalExpenses - $prevExpenses) / $prevExpenses) * 100, 2)
            : ($totalExpenses > 0 ? 100 : 0);
        
        $netProfit = $totalIncome - $totalExpenses;
        
        // Calculate profit change percentage
        $prevProfit = $prevIncome - $prevExpenses;
        $profitPercentage = $prevProfit != 0
            ? round((($netProfit - $prevProfit) / abs($prevProfit)) * 100, 2)
            : ($netProfit > 0 ? 100 : ($netProfit < 0 ? -100 : 0));
        
        // Top products by sales value from stock_logs - OTOMATIS TERUPDATE
        $incomeByProduct = DB::table('stock_logs')
            ->join('products', 'stock_logs.product_id', '=', 'products.id')
            ->where('stock_logs.transaction_type', 'sale')
            ->whereDate('stock_logs.created_at', '>=', $startDate)
            ->whereDate('stock_logs.created_at', '<=', $endDate)
            ->select('products.name', 
                     DB::raw('SUM(ABS(stock_logs.change)) as total_quantity'),
                     DB::raw('SUM(stock_logs.total_value) as total_sales'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
        
        // Chart data - 7 hari terakhir dari stock_logs
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->locale('id')->isoFormat('ddd');
            
            // Income from sales - langsung dari stock_logs
            $income = StockLog::where('transaction_type', 'sale')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_value');
            
            $expense = Expense::whereDate('expense_date', $date->format('Y-m-d'))
                ->sum('amount');
            
            $chartData[] = [
                'day' => $dayName,
                'income' => $income,
                'expense' => $expense
            ];
        }
        
        // Recent Sales - 10 transaksi terbaru dari stock_logs (OTOMATIS TERUPDATE)
        $recentSales = StockLog::with(['product', 'user'])
            ->where('transaction_type', 'sale')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('manager.finance.index', compact(
            'totalIncome',
            'incomePercentage',
            'totalExpenses',
            'expensePercentage',
            'netProfit',
            'profitPercentage',
            'incomeByProduct',
            'chartData',
            'recentSales',
            'startDate',
            'endDate'
        ));
    }

    public function expenses(Request $request)
    {
        $query = Expense::with('user')->orderBy('expense_date', 'desc');
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('category', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }
        
        // Export to PDF
        if ($request->has('export') && $request->export === 'pdf') {
            $expensesList = $query->get();
            $totalExpenses = $expensesList->sum('amount');
            
            // Log export activity
            ActivityLogService::logExport('finance', "Export laporan pengeluaran ke PDF", [
                'format' => 'PDF',
                'total_records' => $expensesList->count(),
                'total_amount' => $totalExpenses,
            ]);
            
            $pdf = Pdf::loadView('manager.finance.expenses-pdf', compact('expensesList', 'totalExpenses'));
            return $pdf->download('laporan-pengeluaran-' . date('Y-m-d') . '.pdf');
        }
        
        // Export to Excel (CSV)
        if ($request->has('export') && $request->export === 'excel') {
            $expensesList = $query->get();
            $totalExpenses = $expensesList->sum('amount');
            
            // Log export activity
            ActivityLogService::logExport('finance', "Export laporan pengeluaran ke Excel", [
                'format' => 'Excel/CSV',
                'total_records' => $expensesList->count(),
                'total_amount' => $totalExpenses,
            ]);
            
            $filename = 'laporan-pengeluaran-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($expensesList, $totalExpenses) {
                $file = fopen('php://output', 'w');
                
                // Add BOM untuk Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Headers
                fputcsv($file, ['No', 'Tanggal', 'Kategori', 'Deskripsi', 'Jumlah', 'Dicatat Oleh']);
                
                // Data
                $no = 1;
                foreach ($expensesList as $expense) {
                    fputcsv($file, [
                        $no++,
                        Carbon::parse($expense->expense_date)->format('d/m/Y'),
                        $expense->category,
                        $expense->description,
                        'Rp ' . number_format($expense->amount, 0, ',', '.'),
                        $expense->user->name ?? 'N/A'
                    ]);
                }
                
                // Total row
                fputcsv($file, ['', '', '', 'TOTAL:', 'Rp ' . number_format($totalExpenses, 0, ',', '.'), '']);
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
        
        $expenses = $query->paginate(15)->withQueryString();
        $categories = Expense::distinct()->pluck('category');
        
        return view('manager.finance.expenses', compact('expenses', 'categories'));
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date'
        ]);
        
        $validated['user_id'] = auth()->id();
        
        $expense = Expense::create($validated);

        // Log activity
        ActivityLogService::logCreate(
            'finance',
            "Menambahkan pengeluaran: {$expense->category} - Rp " . number_format($expense->amount, 0, ',', '.'),
            $expense,
            ['category' => $expense->category, 'amount' => $expense->amount, 'description' => $expense->description]
        );
        
        return redirect()->route('manager.finance.expenses')
            ->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    public function updateExpense(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date'
        ]);

        $oldValues = [
            'category' => $expense->category,
            'amount' => $expense->amount,
            'description' => $expense->description,
        ];
        
        $expense->update($validated);

        // Log activity
        ActivityLogService::logUpdate(
            'finance',
            "Mengubah pengeluaran: {$expense->category}",
            $expense,
            $oldValues,
            ['category' => $expense->category, 'amount' => $expense->amount, 'description' => $expense->description]
        );
        
        return redirect()->route('manager.finance.expenses')
            ->with('success', 'Pengeluaran berhasil diupdate!');
    }

    public function destroyExpense(Expense $expense)
    {
        $expenseCategory = $expense->category;
        $expenseAmount = $expense->amount;

        // Log activity before delete
        ActivityLogService::logDelete(
            'finance',
            "Menghapus pengeluaran: {$expenseCategory} - Rp " . number_format($expenseAmount, 0, ',', '.'),
            null,
            ['category' => $expenseCategory, 'amount' => $expenseAmount, 'description' => $expense->description]
        );

        $expense->delete();
        
        return redirect()->route('manager.finance.expenses')
            ->with('success', 'Pengeluaran berhasil dihapus!');
    }

    public function income(Request $request)
    {
        $query = StockLog::with(['product', 'user'])
            ->where('transaction_type', 'sale')
            ->orderBy('created_at', 'desc');
        
        // Filter by product name
        if ($request->has('search') && $request->search) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Filter by cashier (user)
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Export to PDF
        if ($request->has('export') && $request->export === 'pdf') {
            $incomeLogs = $query->get();
            $totalFilteredValue = $incomeLogs->sum('total_value');
            $users = User::where('role_id', 2)->orderBy('name')->get();
            
            // Log export activity
            ActivityLogService::logExport('finance', "Export laporan pemasukan ke PDF", [
                'format' => 'PDF',
                'total_records' => $incomeLogs->count(),
                'total_amount' => $totalFilteredValue,
            ]);
            
            $pdf = Pdf::loadView('manager.finance.income-pdf', compact('incomeLogs', 'totalFilteredValue', 'users'));
            return $pdf->download('laporan-pemasukan-' . date('Y-m-d') . '.pdf');
        }
        
        // Export to Excel (CSV)
        if ($request->has('export') && $request->export === 'excel') {
            $incomeLogs = $query->get();
            $totalFilteredValue = $incomeLogs->sum('total_value');
            
            // Log export activity
            ActivityLogService::logExport('finance', "Export laporan pemasukan ke Excel", [
                'format' => 'Excel/CSV',
                'total_records' => $incomeLogs->count(),
                'total_amount' => $totalFilteredValue,
            ]);
            
            $filename = 'laporan-pemasukan-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($incomeLogs, $totalFilteredValue) {
                $file = fopen('php://output', 'w');
                
                // Add BOM untuk Excel agar UTF-8 terbaca dengan benar
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Headers
                fputcsv($file, ['No', 'Tanggal', 'Produk', 'Jumlah Terjual', 'Harga Satuan', 'Total Pemasukan', 'User/Kasir', 'Catatan']);
                
                // Data
                $no = 1;
                foreach ($incomeLogs as $log) {
                    fputcsv($file, [
                        $no++,
                        $log->created_at->format('d/m/Y H:i'),
                        $log->product->name ?? 'N/A',
                        abs($log->change) . ' pack',
                        'Rp ' . number_format($log->unit_price, 0, ',', '.'),
                        'Rp ' . number_format($log->total_value, 0, ',', '.'),
                        $log->user->name ?? 'System',
                        $log->note ?? '-'
                    ]);
                }
                
                // Total row
                fputcsv($file, ['', '', '', '', 'TOTAL:', 'Rp ' . number_format($totalFilteredValue, 0, ',', '.'), '', '']);
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
        
        $incomeLogs = $query->paginate(15)->withQueryString();
        
        // Calculate total with filters applied
        $totalFilteredValue = StockLog::where('transaction_type', 'sale');
        
        if ($request->has('search') && $request->search) {
            $totalFilteredValue->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->has('start_date') && $request->start_date) {
            $totalFilteredValue->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $totalFilteredValue->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->has('user_id') && $request->user_id) {
            $totalFilteredValue->where('user_id', $request->user_id);
        }
        
        $totalFilteredValue = $totalFilteredValue->sum('total_value');
        
        // Get all cashiers (role_id = 2 for kasir)
        $users = User::where('role_id', 2)->orderBy('name')->get();
        
        return view('manager.finance.income', compact('incomeLogs', 'totalFilteredValue', 'users'));
    }
}
