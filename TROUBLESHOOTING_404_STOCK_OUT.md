# Troubleshooting: Error 404 pada Stok Keluar Kasir

## Problem
Halaman `/cashier/inventory/stock-out` menampilkan error **404 NOT FOUND**

## Possible Causes & Solutions

### 1. ❌ Belum Login atau Login Salah Role

**Symptoms:**
- Error 404 NOT FOUND
- Redirect ke halaman login
- Tidak bisa akses halaman kasir

**Solution:**
```bash
# Login dengan user yang memiliki role "kasir"
# Contoh:
# Email: kasir@example.com atau user dengan role_id = 2
```

**Check Your Role:**
1. Buka browser DevTools (F12)
2. Cek tab Application/Storage → Cookies
3. Pastikan ada session Laravel
4. Atau cek di halaman profile/dashboard untuk melihat role Anda

---

### 2. 🔄 Cache Not Cleared

**Symptoms:**
- Route sudah benar di `web.php`
- Controller method sudah ada
- Tetapi masih 404

**Solution:**
```powershell
cd c:\star-frozen-pos
php artisan optimize:clear
# atau
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

---

### 3. 🚫 Middleware Role Blocking

**Symptoms:**
- Error **403 Forbidden** (bukan 404)
- Pesan "Unauthorized action"

**Check Middleware:**
```php
// app/Http/Middleware/CheckRole.php
if (!$request->user() || !$request->user()->hasRole($role)) {
    abort(403, 'Unauthorized action.'); // ← 403, bukan 404
}
```

**Solution:**
- Pastikan user login memiliki role "kasir"
- Check database: `SELECT * FROM users WHERE id = [your_user_id]`
- Check role_id user harus mengarah ke role dengan name = 'kasir'

---

### 4. 🌐 Server Not Running

**Symptoms:**
- Semua halaman 404
- Browser tidak bisa connect

**Check Server:**
```powershell
# Check if PHP process is running
Get-Process | Where-Object {$_.ProcessName -eq "php"}

# Restart server if needed
php artisan serve --host=127.0.0.1 --port=8000
```

**Verify URL:**
- ✅ Correct: `http://127.0.0.1:8000/cashier/inventory/stock-out`
- ❌ Wrong: `http://localhost:8000/inventory/stock-out` (missing cashier prefix)
- ❌ Wrong: `http://127.0.0.1:8000/manager/inventory/stock-out` (wrong role)

---

### 5. 📁 Route Not Registered

**Verify Route Exists:**
```powershell
cd c:\star-frozen-pos
php artisan route:list --name=stock-out
```

**Expected Output:**
```
GET|HEAD  cashier/inventory/stock-out → cashier.inventory.stock-out
```

**If Route Missing:**
Check `routes/web.php` line 70:
```php
Route::get('/inventory/stock-out', [CashierInventoryController::class, 'stockOut'])
    ->name('inventory.stock-out');
```

---

### 6. 🔧 Controller Method Missing

**Check Controller:**
```php
// app/Http/Controllers/CashierInventoryController.php
public function stockOut(Request $request)
{
    $search = $request->input('search');
    $sort = $request->input('sort', 'tanggal_terbaru');
    
    $query = StockLog::with('product', 'user')
        ->where('change', '<', 0)
        ->where('user_id', auth()->id());
    
    // ... more code
    
    return view('cashier.inventory.stock-out', compact('stockLogs'));
}
```

---

### 7. 📄 View File Missing

**Check View Exists:**
```powershell
Test-Path "c:\star-frozen-pos\resources\views\cashier\inventory\stock-out.blade.php"
# Should return: True
```

**If False:**
- View file tidak ada atau salah nama
- Pastikan file benar: `stock-out.blade.php` (bukan `stock_out.blade.php`)

---

## Quick Fix Checklist

Jalankan command ini satu per satu:

```powershell
# 1. Navigate to project root
cd c:\star-frozen-pos

# 2. Clear all caches
php artisan optimize:clear

# 3. Verify route exists
php artisan route:list --name=stock-out

# 4. Check if controller method exists
php artisan tinker --execute="method_exists(new App\Http\Controllers\CashierInventoryController, 'stockOut');"

# 5. Check if view exists
Test-Path "resources\views\cashier\inventory\stock-out.blade.php"

# 6. Restart server (if needed)
# Press Ctrl+C to stop current server, then:
php artisan serve
```

---

## Debug Mode

Jika masih bermasalah, aktifkan debug untuk melihat error detail:

### Add Debug to Controller:
```php
public function stockOut(Request $request)
{
    dd([
        'Authenticated' => auth()->check(),
        'User ID' => auth()->id(),
        'User Name' => auth()->user()?->name,
        'Role' => auth()->user()?->role?->name,
        'Has Role Kasir' => auth()->user()?->hasRole('kasir'),
    ]);
    
    // ... rest of code
}
```

### Check Laravel Log:
```powershell
Get-Content storage\logs\laravel.log -Tail 100
```

---

## Testing After Fix

1. **Clear Browser Cache:**
   - Press `Ctrl + Shift + R` (hard refresh)
   - Or clear browser cache completely

2. **Test Login:**
   ```
   Email: [kasir-email]
   Password: [kasir-password]
   ```

3. **Navigate:**
   - Dashboard → Inventory → Stok Keluar
   - Or direct: `http://127.0.0.1:8000/cashier/inventory/stock-out`

4. **Expected Result:**
   - ✅ Halaman stok keluar muncul
   - ✅ Menampilkan tabel dengan filter search & sort
   - ✅ Data ter-filter sesuai user login (session-based)
   - ✅ Pagination berfungsi

---

## Common Mistakes

### ❌ Wrong URL
```
http://127.0.0.1:8000/inventory/stock-out  ← Missing "cashier" prefix
```

### ✅ Correct URL
```
http://127.0.0.1:8000/cashier/inventory/stock-out
```

### ❌ Wrong Role
- Login sebagai Manager → tidak bisa akses route kasir
- Route kasir hanya untuk `role:kasir`

### ❌ Route Cache Not Cleared
- Setelah edit `web.php` harus clear route cache
- Command: `php artisan route:clear`

---

## Need More Help?

Jika semua solusi di atas sudah dicoba dan masih error 404:

1. **Screenshot:**
   - Browser URL bar
   - Full error page
   - DevTools Network tab

2. **Check:**
   - User role di database
   - Laravel log file
   - PHP error log

3. **Provide Info:**
   - Laravel version
   - PHP version
   - Web server (artisan serve / Apache / Nginx)

---

## ⚠️ UPDATE: Route Conflict Issue (November 26, 2025)

### New Problem Found
Error 404 juga bisa disebabkan oleh **Route Conflict** - route dengan parameter dinamis didefinisikan sebelum route specific.

### Root Cause
```php
// ❌ WRONG ORDER - {product} catches "stock-out"
Route::get('/inventory/{product}', ...)->name('inventory.show');
Route::get('/inventory/stock-out', ...)->name('inventory.stock-out');  // Never reached!
```

### Solution Applied
Routes sudah diurutkan dengan benar di `routes/web.php`:
```php
// ✅ CORRECT ORDER - Specific routes first
Route::get('/inventory/low-stock', ...)->name('inventory.low-stock');
Route::get('/inventory/stock-out', ...)->name('inventory.stock-out');
Route::get('/inventory/{product}', ...)->name('inventory.show');  // Dynamic route last
```

### After This Fix
```bash
php artisan route:clear
```

Halaman `/cashier/inventory/stock-out` seharusnya sudah bisa diakses!

---

**Last Updated:** November 26, 2025 (Route conflict fix applied)
**Project:** Star Frozen POS
