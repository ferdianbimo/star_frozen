<?php

use Illuminate\Support\Facades\Route;

// Redirect homepage to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Test auth status
Route::get('/test-auth', [\App\Http\Controllers\TestAuthController::class, 'check'])->name('test.auth');

Route::get('/dashboard', function () {
    if (auth()->user()->isManager()) {
        return redirect()->route('manager.dashboard');
    } else {
        return redirect()->route('cashier.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Manager Routes
Route::prefix('manager')->middleware(['auth', 'role:manager'])->name('manager.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\ManagerDashboardController::class, 'index'])->name('dashboard');
    
    // Inventory Management (View Only - No CRUD)
    Route::get('/inventory', [\App\Http\Controllers\ManagerInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/stock-out', [\App\Http\Controllers\ManagerInventoryController::class, 'stockOut'])->name('inventory.stock-out');
    Route::get('/inventory/batches', [\App\Http\Controllers\ManagerInventoryController::class, 'batches'])->name('inventory.batches');
    
    // Activity Logs (separate feature)
    Route::get('/activity-logs', [\App\Http\Controllers\ManagerInventoryController::class, 'activityLogs'])->name('activity-logs.index');
    
    // Finance (Keuangan)
    Route::get('/finance', [\App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/income', [\App\Http\Controllers\FinanceController::class, 'income'])->name('finance.income');
    Route::get('/finance/expenses', [\App\Http\Controllers\FinanceController::class, 'expenses'])->name('finance.expenses');
    Route::post('/finance/expenses', [\App\Http\Controllers\FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
    Route::put('/finance/expenses/{expense}', [\App\Http\Controllers\FinanceController::class, 'updateExpense'])->name('finance.expenses.update');
    Route::delete('/finance/expenses/{expense}', [\App\Http\Controllers\FinanceController::class, 'destroyExpense'])->name('finance.expenses.destroy');
    
    // Access Control (Hak Akses) - User & Role Management
    Route::get('/access', [\App\Http\Controllers\AccessController::class, 'index'])->name('access.index');
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->only(['edit', 'update', 'destroy']);
});

// Cashier Routes
Route::prefix('cashier')->middleware(['auth', 'role:kasir'])->name('cashier.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\CashierDashboardController::class, 'index'])->name('dashboard');
    
    // Point of Sale
    Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/add', [\App\Http\Controllers\PosController::class, 'addToCart'])->name('pos.add');
    Route::post('/pos/remove', [\App\Http\Controllers\PosController::class, 'removeFromCart'])->name('pos.remove');
    Route::post('/pos/update', [\App\Http\Controllers\PosController::class, 'updateCart'])->name('pos.update');
    Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{transaction}', [\App\Http\Controllers\PosController::class, 'receipt'])->name('pos.receipt');
    // New transaction: clear last transaction and cart, redirect to POS
    Route::get('/pos/new', [\App\Http\Controllers\PosController::class, 'newTransaction'])->name('pos.new');
    
    // Transactions
    Route::get('/transactions', [\App\Http\Controllers\CashierTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [\App\Http\Controllers\CashierTransactionController::class, 'show'])->name('transactions.show');
    
    // Inventory Management
    Route::get('/inventory', [\App\Http\Controllers\CashierInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [\App\Http\Controllers\CashierInventoryController::class, 'create'])->name('inventory.create');
    Route::get('/inventory/low-stock', [\App\Http\Controllers\CashierInventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('/inventory/stock-out', [\App\Http\Controllers\CashierInventoryController::class, 'stockOut'])->name('inventory.stock-out');
    Route::get('/inventory/stock-out/updates', [\App\Http\Controllers\CashierInventoryController::class, 'stockOutUpdates'])->name('inventory.stock-out.updates');
    Route::post('/inventory', [\App\Http\Controllers\CashierInventoryController::class, 'store'])->name('inventory.store');
    Route::post('/inventory/update-stock', [\App\Http\Controllers\CashierInventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::get('/inventory/{product}', [\App\Http\Controllers\CashierInventoryController::class, 'show'])->name('inventory.show');
    Route::put('/inventory/{product}', [\App\Http\Controllers\CashierInventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{product}', [\App\Http\Controllers\CashierInventoryController::class, 'destroy'])->name('inventory.destroy');
    
    // Batch Management (Stock In)
    Route::get('/inventory/batch/stock-in', [\App\Http\Controllers\CashierInventoryController::class, 'stockIn'])->name('inventory.batch.stock-in');
    Route::post('/inventory/batch/store', [\App\Http\Controllers\CashierInventoryController::class, 'storeBatch'])->name('inventory.batch.store');
    Route::get('/inventory/{product}/batches', [\App\Http\Controllers\CashierInventoryController::class, 'productBatches'])->name('inventory.batches');
    
    // API for POS batch selection
    Route::get('/api/products/{product}/batches', [\App\Http\Controllers\CashierInventoryController::class, 'getProductBatches'])->name('api.product.batches');
});

require __DIR__.'/auth.php';
