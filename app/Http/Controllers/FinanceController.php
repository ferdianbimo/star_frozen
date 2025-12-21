<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * FinanceController - Mengelola data keuangan dan laporan.
 *
 * Controller ini menangani:
 * - Dashboard keuangan dengan ringkasan pendapatan/pengeluaran
 * - Manajemen pengeluaran (CRUD)
 * - Laporan keuangan dengan export PDF/Excel
 * - Analisis trend dan perbandingan periode
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class FinanceController extends Controller
{
    /**
     * Menampilkan dashboard keuangan.
     *
     * Menampilkan ringkasan:
     * - Total pendapatan dari penjualan
     * - Total pengeluaran
     * - Laba bersih
     * - Trend 7 hari terakhir
     * - Produk terlaris
     *
     * @param  Request $request Request dengan filter start_date dan end_date
     * @return \Illuminate\View\View
     */
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

    /**
     * Menampilkan daftar pengeluaran dengan filter dan export.
     *
     * @param  Request $request Request dengan filter search, category, date range
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\StreamedResponse
     */
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
        
        // Export to Excel (HTML-based untuk kompatibilitas)
        if ($request->has('export') && $request->export === 'excel') {
            $expensesList = $query->get();
            $totalExpenses = $expensesList->sum('amount');
            
            // Log export activity
            ActivityLogService::logExport('finance', "Export laporan pengeluaran ke Excel", [
                'format' => 'Excel/XLS',
                'total_records' => $expensesList->count(),
                'total_amount' => $totalExpenses,
            ]);
            
            $filename = 'laporan-pengeluaran-' . date('Y-m-d') . '.xls';
            $periodText = '';
            if ($request->start_date && $request->end_date) {
                $periodText = Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($request->end_date)->format('d/m/Y');
            } elseif ($request->start_date) {
                $periodText = 'Dari ' . Carbon::parse($request->start_date)->format('d/m/Y');
            } elseif ($request->end_date) {
                $periodText = 'Sampai ' . Carbon::parse($request->end_date)->format('d/m/Y');
            } else {
                $periodText = 'Semua Periode';
            }
            
            return response()->view('exports.expenses-excel', compact('expensesList', 'totalExpenses', 'periodText'))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'max-age=0');
        }
        
        $expenses = $query->paginate(15)->withQueryString();
        $categories = ExpenseCategory::orderBy('name')->get();
        
        return view('manager.finance.expenses', compact('expenses', 'categories'));
    }

    /**
     * Menyimpan pengeluaran baru.
     *
     * @param  Request $request Request dengan data pengeluaran
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Mengupdate data pengeluaran.
     *
     * @param  Request $request Request dengan data update
     * @param  Expense $expense Expense yang akan diupdate
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Menghapus pengeluaran dari database.
     *
     * @param  Expense $expense Expense yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Menampilkan daftar pemasukan (penjualan) dengan filter dan export.
     *
     * @param  Request $request Request dengan filter search, date range
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\StreamedResponse
     */
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
        
        // Export to Excel (HTML-based untuk kompatibilitas)
        if ($request->has('export') && $request->export === 'excel') {
            $incomeLogs = $query->get();
            $totalFilteredValue = $incomeLogs->sum('total_value');
            
            // Log export activity
            ActivityLogService::logExport('finance', "Export laporan pemasukan ke Excel", [
                'format' => 'Excel/XLS',
                'total_records' => $incomeLogs->count(),
                'total_amount' => $totalFilteredValue,
            ]);
            
            $filename = 'laporan-pemasukan-' . date('Y-m-d') . '.xls';
            $periodText = '';
            if ($request->start_date && $request->end_date) {
                $periodText = Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($request->end_date)->format('d/m/Y');
            } elseif ($request->start_date) {
                $periodText = 'Dari ' . Carbon::parse($request->start_date)->format('d/m/Y');
            } elseif ($request->end_date) {
                $periodText = 'Sampai ' . Carbon::parse($request->end_date)->format('d/m/Y');
            } else {
                $periodText = 'Semua Periode';
            }
            
            return response()->view('exports.income-excel', compact('incomeLogs', 'totalFilteredValue', 'periodText'))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'max-age=0');
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

    /*
    |--------------------------------------------------------------------------
    | EXPENSE CATEGORY CRUD (JSON API)
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan daftar kategori pengeluaran.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExpenseCategories()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return response()->json($categories);
    }

    /**
     * Menyimpan kategori pengeluaran baru.
     *
     * @param  Request $request Request dengan nama kategori
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeExpenseCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name'
        ]);

        $category = ExpenseCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan!',
            'category' => $category
        ]);
    }

    /**
     * Mengupdate kategori pengeluaran.
     *
     * Juga mengupdate semua expense yang menggunakan
     * nama kategori lama ke nama baru.
     *
     * @param  Request         $request  Request dengan nama baru
     * @param  ExpenseCategory $category Kategori yang akan diupdate
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateExpenseCategory(Request $request, ExpenseCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $category->id
        ]);

        // Update expenses with old category name to new name
        $oldName = $category->name;
        Expense::where('category', $oldName)->update(['category' => $validated['name']]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diupdate!',
            'category' => $category
        ]);
    }

    /**
     * Menghapus kategori pengeluaran.
     *
     * Tidak dapat menghapus kategori yang masih digunakan oleh expense.
     *
     * @param  ExpenseCategory $category Kategori yang akan dihapus
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyExpenseCategory(ExpenseCategory $category)
    {
        // Check if category is in use
        $usedCount = Expense::where('category', $category->name)->count();
        
        if ($usedCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Kategori tidak dapat dihapus karena digunakan oleh {$usedCount} pengeluaran."
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus!'
        ]);
    }
}
