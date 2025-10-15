<?php

use Illuminate\Support\Facades\Route;

// Redirect homepage to login page
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->isManager()) {
        return view('manager.dashboard');
    } else {
        return view('cashier.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Manager Routes
Route::prefix('manager')->middleware(['auth', 'role:manager'])->name('manager.')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'manager.dashboard')->name('dashboard');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\UserController::class);
    
    // Reports
    Route::get('/reports/sales', [\App\Http\Controllers\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/profits', [\App\Http\Controllers\ReportController::class, 'profits'])->name('reports.profits');
    Route::get('/reports/export/{type}', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
});

// Cashier Routes
Route::prefix('cashier')->middleware(['auth', 'role:kasir'])->name('cashier.')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'cashier.dashboard')->name('dashboard');
    
    // Point of Sale
    Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{transaction}', [\App\Http\Controllers\PosController::class, 'receipt'])->name('pos.receipt');
    
    // Transactions
    Route::get('/transactions', [\App\Http\Controllers\CashierTransactionController::class, 'index'])->name('transactions.index');
    
    // Inventory Management
    Route::get('/inventory', [\App\Http\Controllers\CashierInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/{product}', [\App\Http\Controllers\CashierInventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/update-stock', [\App\Http\Controllers\CashierInventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::get('/inventory/low-stock', [\App\Http\Controllers\CashierInventoryController::class, 'lowStock'])->name('inventory.low-stock');
});

require __DIR__.'/auth.php';
