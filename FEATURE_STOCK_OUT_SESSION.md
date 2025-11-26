# Fitur Stok Keluar Kasir - Session Based

## Ringkasan
Fitur stok keluar untuk role kasir yang menampilkan data **sesuai sesi masing-masing**. Setiap kasir hanya dapat melihat riwayat stok keluar yang mereka lakukan sendiri.

## Cara Kerja

### 1. Filter Berdasarkan Session
```php
// Di CashierInventoryController.php - stockOut()
->where('user_id', auth()->id())
```
- **Kasir** (user_id = 2) hanya melihat stok keluar yang dilakukan oleh dirinya sendiri
- **kasir2** (user_id = 5) hanya melihat stok keluar yang dilakukan oleh dirinya sendiri
- Filter otomatis berdasarkan user yang sedang login (`auth()->id()`)

### 2. Fitur Pencarian & Filter
**Pencarian:**
- Nama produk
- Barcode produk  
- Kategori produk

**Pengurutan:**
- Tanggal Terbaru (default)
- Tanggal Terlama
- Jumlah Terbanyak
- Jumlah Tersedikit

### 3. Tampilan Data
Tabel menampilkan:
- **No** - Nomor urut dengan pagination
- **Tanggal** - Tanggal dan jam transaksi (format: dd/mm/yyyy HH:ii)
- **Produk** - Gambar, nama, dan kategori produk
- **Jumlah Keluar** - Jumlah stok yang keluar (ditampilkan dalam warna merah)
- **Stok Sebelum** - Stok sebelum transaksi
- **Stok Sesudah** - Stok setelah transaksi
- **Catatan** - Catatan tambahan (jika ada)

## File yang Dimodifikasi

### 1. Controller
**File:** `app/Http/Controllers/CashierInventoryController.php`

#### Method: `stockOut()`
```php
public function stockOut(Request $request)
{
    $search = $request->input('search');
    $sort = $request->input('sort', 'tanggal_terbaru');

    $sortMap = [
        'tanggal_terbaru' => ['created_at', 'desc'],
        'tanggal_terlama' => ['created_at', 'asc'],
        'jumlah_banyak' => ['change', 'asc'],
        'jumlah_sedikit' => ['change', 'desc'],
    ];

    $sortColumn = $sortMap[$sort][0] ?? 'created_at';
    $sortDirection = $sortMap[$sort][1] ?? 'desc';

    $stockLogs = StockLog::with('product', 'user')
        ->where('change', '<', 0)
        ->where('user_id', auth()->id()) // FILTER SESSION
        ->when($search, function ($query, $search) {
            return $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        })
        ->orderBy($sortColumn, $sortDirection)
        ->paginate(15)
        ->withQueryString();

    return view('cashier.inventory.stock-out', compact('stockLogs'));
}
```

#### Method: `stockOutUpdates()`
```php
public function stockOutUpdates(Request $request)
{
    $sinceId = $request->input('since_id');

    $query = StockLog::with('product', 'user')
        ->where('change', '<', 0)
        ->where('user_id', auth()->id()) // FILTER SESSION
        ->orderBy('created_at', 'desc');
        
    if ($sinceId) {
        $query->where('id', '>', (int)$sinceId);
    }

    $logs = $query->limit(50)->get();
    
    // Return JSON response
    return response()->json(['logs' => $data]);
}
```

### 2. View
**File:** `resources/views/cashier/inventory/stock-out.blade.php`

**Perubahan Utama:**
- Variable `$logs` → `$stockLogs` (konsisten dengan controller)
- Menambahkan form pencarian dan filter
- Menambahkan card total transaksi
- Menampilkan nomor urut dengan pagination
- UI yang lebih modern dan informatif
- JavaScript polling dinonaktifkan (optional untuk real-time update)

## Route
```php
Route::middleware(['auth', 'role:kasir'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/inventory/stock-out', [CashierInventoryController::class, 'stockOut'])
        ->name('inventory.stock-out');
    Route::get('/inventory/stock-out/updates', [CashierInventoryController::class, 'stockOutUpdates'])
        ->name('inventory.stock-out.updates');
});
```

## Testing

### Skenario Pengujian

#### 1. Test Session Filter
**Langkah:**
1. Login sebagai **Kasir** (user_id = 2)
2. Buka halaman Stok Keluar
3. Lakukan transaksi stok keluar
4. Logout

5. Login sebagai **kasir2** (user_id = 5)
6. Buka halaman Stok Keluar
7. Lakukan transaksi stok keluar
8. Verifikasi hanya melihat transaksi sendiri

**Expected Result:**
- Kasir hanya melihat stok keluar yang dilakukan oleh Kasir
- kasir2 hanya melihat stok keluar yang dilakukan oleh kasir2
- Tidak ada data cross-user visible

#### 2. Test Search Functionality
**Langkah:**
1. Login sebagai kasir
2. Buka halaman Stok Keluar
3. Masukkan nama produk di search box
4. Klik "Terapkan"

**Expected Result:**
- Menampilkan hanya produk yang sesuai dengan keyword
- Data tetap ter-filter by session

#### 3. Test Sort Functionality
**Langkah:**
1. Login sebagai kasir
2. Buka halaman Stok Keluar
3. Ubah urutan (Tanggal Terbaru/Terlama/Jumlah Banyak/Sedikit)
4. Klik "Terapkan"

**Expected Result:**
- Data terurut sesuai pilihan
- Pagination tetap berfungsi
- Session filter tetap aktif

## Keamanan

### Session Isolation
✅ Setiap kasir hanya dapat melihat data mereka sendiri
✅ Filter berdasarkan `auth()->id()` mencegah akses data user lain
✅ Query menggunakan Eloquent ORM (aman dari SQL injection)

### Authorization
✅ Middleware `role:kasir` memastikan hanya kasir yang dapat mengakses
✅ Auth check pada setiap request

## Manfaat

1. **Privacy** - Setiap kasir tidak dapat melihat aktivitas kasir lain
2. **Akuntabilitas** - Tracking jelas siapa yang melakukan stok keluar
3. **User Experience** - Interface yang clean dan mudah digunakan
4. **Performance** - Pagination dan filter mencegah overload data
5. **Flexibility** - Search & sort untuk kemudahan analisis

## Troubleshooting

### Problem: Melihat semua data kasir
**Solution:** Pastikan filter `->where('user_id', auth()->id())` ada di method `stockOut()` dan `stockOutUpdates()`

### Problem: Pagination tidak berfungsi
**Solution:** Gunakan `->withQueryString()` pada pagination untuk mempertahankan parameter search & sort

### Problem: Search tidak bekerja
**Solution:** Pastikan relasi `product` sudah di-load dengan `->with('product')`

## Future Improvements

1. **Export to Excel/PDF** - Kasir dapat export riwayat stok keluar mereka
2. **Date Range Filter** - Filter berdasarkan rentang tanggal
3. **Summary Statistics** - Total item keluar, produk terbanyak keluar, dll
4. **Notifications** - Real-time notification untuk stok keluar baru

## Changelog

### Version 1.0 (Current)
- ✅ Session-based filtering for stock-out
- ✅ Search by product name/barcode/category
- ✅ Sort functionality (date & quantity)
- ✅ Pagination (15 items per page)
- ✅ Modern UI with total transactions card
- ✅ Security: User isolation enforced

---
**Author:** GitHub Copilot
**Date:** {{ now()->format('d M Y') }}
**Project:** Star Frozen POS
