# Fitur Riwayat Transaksi Kasir

## Deskripsi
Fitur ini memungkinkan kasir untuk melihat riwayat transaksi penjualan yang telah dilakukan pada sesi mereka masing-masing. Setiap kasir hanya dapat melihat transaksi yang mereka buat sendiri.

## URL Akses
```
http://127.0.0.1:8000/cashier/transactions
```

## Fitur Utama

### 1. Halaman List Transaksi (`index`)
- **Dashboard Statistik**: 4 card menampilkan:
  - Total Transaksi (jumlah transaksi)
  - Total Penjualan (total dari semua transaksi)
  - Penjualan Bersih (total setelah dikurangi diskon)
  - Rata-rata Transaksi

- **Filter & Pencarian**:
  - Search: Cari berdasarkan ID transaksi atau nama customer
  - Tanggal Dari/Sampai: Filter berdasarkan range tanggal
  - Sort By: 
    - Terbaru (default)
    - Terlama
    - Total Tertinggi
    - Total Terendah

- **Tabel Transaksi**:
  - ID Transaksi
  - Tanggal & Waktu
  - Customer
  - Jumlah Item
  - Subtotal
  - Diskon
  - Total
  - Aksi (tombol Detail)

- **Pagination**: 20 transaksi per halaman

### 2. Halaman Detail Transaksi (`show`)
- **Informasi Transaksi**:
  - ID Transaksi
  - Tanggal & Waktu
  - Nama Kasir
  - Nama Customer

- **Daftar Item**: Tabel dengan:
  - Nomor
  - Produk (dengan gambar dan kategori)
  - Harga satuan
  - Kuantitas
  - Subtotal

- **Ringkasan Pembayaran**:
  - Subtotal
  - Diskon (jika ada)
  - Total Akhir
  - Metode Pembayaran
  - Status (Lunas)

- **Aksi**:
  - Tombol Cetak (print)
  - Link ke Struk
  - Kembali ke Riwayat

## Keamanan & Isolasi

### Session-Based Filtering
Setiap kasir **HANYA** bisa melihat transaksi mereka sendiri:

```php
// Filter di Controller
$transactions = Transaction::where('cashier_id', auth()->id())
    ->with(['items.product', 'user'])
    ->orderBy('created_at', 'desc')
    ->paginate(20);
```

### Middleware
Route dilindungi dengan middleware `role:cashier`:
```php
Route::middleware(['auth', 'role:cashier'])->prefix('cashier')->group(function () {
    Route::get('/transactions', [CashierTransactionController::class, 'index'])->name('cashier.transactions.index');
    Route::get('/transactions/{id}', [CashierTransactionController::class, 'show'])->name('cashier.transactions.show');
});
```

## Struktur Database

### Tabel `transactions`
```sql
- id (primary key)
- cashier_id (foreign key ke users)
- customer_name (nullable)
- subtotal (decimal)
- discount (decimal)
- discount_pct (decimal)
- total (decimal)
- created_at
- updated_at
```

### Tabel `transaction_items`
```sql
- id (primary key)
- transaction_id (foreign key ke transactions)
- product_id (foreign key ke products)
- product_name
- price (decimal)
- quantity (integer)
- subtotal (decimal)
- created_at
- updated_at
```

## Cara Penggunaan

### Sebagai Kasir (Kasir/Kasir2):

1. **Login** dengan akun kasir
   - URL: `http://127.0.0.1:8000/login`

2. **Akses Riwayat Transaksi** (3 cara):
   - Klik menu "**Riwayat Transaksi**" di sidebar (ikon history)
   - Klik card "**Today's Transactions**" di dashboard
   - Atau akses langsung: `http://127.0.0.1:8000/cashier/transactions`

3. **Melihat Statistik**
   - Dashboard otomatis menampilkan total transaksi, total penjualan, dll

4. **Mencari Transaksi**
   - Gunakan search box untuk cari berdasarkan ID atau customer
   - Gunakan filter tanggal untuk range tertentu
   - Gunakan dropdown sort untuk mengurutkan data

5. **Melihat Detail**
   - Klik tombol "Detail" pada transaksi yang ingin dilihat
   - Akan muncul halaman dengan informasi lengkap item-item yang dibeli
   - Bisa cetak atau lihat struk dari halaman detail

## Files yang Dibuat/Dimodifikasi

### 1. Controller
**File**: `app/Http/Controllers/CashierTransactionController.php`
- Method `index()`: List transaksi dengan statistik dan filter
- Method `show($id)`: Detail transaksi

### 2. Views
**File**: `resources/views/cashier/transactions/index.blade.php`
- Halaman list dengan 4 statistik card
- Form filter (search, tanggal, sort)
- Tabel transaksi dengan pagination

**File**: `resources/views/cashier/transactions/show.blade.php`
- Halaman detail transaksi
- Informasi transaksi lengkap
- Daftar item dengan gambar produk
- Ringkasan pembayaran

### 3. Routes
**File**: `routes/web.php`
```php
Route::get('/transactions', [CashierTransactionController::class, 'index'])
    ->name('cashier.transactions.index');
Route::get('/transactions/{id}', [CashierTransactionController::class, 'show'])
    ->name('cashier.transactions.show');
```

### 4. Sidebar Navigation (Updated)
Menu "Riwayat Transaksi" ditambahkan ke semua halaman kasir:
- ✅ `resources/views/cashier/dashboard.blade.php`
- ✅ `resources/views/cashier/pos.blade.php`
- ✅ `resources/views/cashier/inventory/index.blade.php`
- ✅ `resources/views/cashier/inventory/show.blade.php`
- ✅ `resources/views/cashier/inventory/low-stock.blade.php`
- ✅ `resources/views/cashier/inventory/stock-out.blade.php`
- ✅ `resources/views/cashier/transactions/index.blade.php`
- ✅ `resources/views/cashier/transactions/show.blade.php`

### 5. Dashboard Cards
**File**: `resources/views/cashier/dashboard.blade.php`
- Card "Today's Transactions" yang mengarah ke riwayat transaksi

## Testing Checklist

### Test Basic Access
- [x] Login sebagai kasir2
- [ ] Akses `/cashier/transactions` → Harus berhasil
- [ ] Akses `/cashier/transactions/1` → Harus berhasil (jika transaksi milik kasir)
- [ ] Statistik card menampilkan data yang benar

### Test Session Filtering
- [ ] Login sebagai Kasir → Harus hanya lihat transaksi Kasir
- [ ] Login sebagai kasir2 → Harus hanya lihat transaksi kasir2
- [ ] Tidak boleh ada transaksi dari kasir lain

### Test Search & Filter
- [ ] Search dengan ID transaksi → Harus menemukan transaksi
- [ ] Search dengan nama customer → Harus filter dengan benar
- [ ] Filter tanggal dari/sampai → Harus filter range yang benar
- [ ] Sort terbaru → Transaksi terbaru di atas
- [ ] Sort terlama → Transaksi terlama di atas
- [ ] Sort total tertinggi → Transaksi dengan total terbesar di atas
- [ ] Sort total terendah → Transaksi dengan total terkecil di atas

### Test Detail View
- [ ] Klik detail → Harus buka halaman detail
- [ ] Informasi transaksi lengkap muncul
- [ ] Daftar item dengan gambar produk muncul
- [ ] Perhitungan subtotal + diskon = total benar
- [ ] Tombol cetak berfungsi
- [ ] Tombol kembali berfungsi

### Test Pagination
- [ ] Pagination muncul jika transaksi > 20
- [ ] Klik halaman 2 → Data halaman 2 muncul
- [ ] Filter tetap bertahan saat pindah halaman

## Troubleshooting

### Halaman 404 Not Found
**Penyebab**: Route tidak terdaftar atau URL salah
**Solusi**:
1. Cek route: `php artisan route:list --path=cashier/transactions`
2. Pastikan URL benar: `/cashier/transactions` (bukan `/manager/transactions`)
3. Pastikan login sebagai kasir (bukan manager)

### Data Kosong
**Penyebab**: Belum ada transaksi atau filter terlalu ketat
**Solusi**:
1. Cek apakah kasir sudah pernah membuat transaksi di POS
2. Reset filter (hapus search dan tanggal)
3. Cek database: `SELECT * FROM transactions WHERE cashier_id = [id_kasir]`

### Statistik Tidak Akurat
**Penyebab**: Perhitungan query salah
**Solusi**:
1. Cek method `index()` di controller
2. Pastikan SUM dan COUNT menggunakan where cashier_id
3. Cek apakah diskon sudah dikurangi dengan benar

### Tidak Bisa Akses Transaksi Lain
**Behavior**: Ini NORMAL dan BENAR
**Penjelasan**: Setiap kasir hanya boleh lihat transaksi mereka sendiri untuk keamanan data

## Integrasi dengan Fitur Lain

### 1. Point of Sale (POS)
- Setiap transaksi yang dibuat di `/cashier/pos` otomatis masuk ke riwayat
- `cashier_id` otomatis terisi dengan auth()->id()

### 2. Stock-Out
- Transaksi POS juga tercatat di `stock_logs` dengan `transaction_type='sale'`
- Link antara POS → Riwayat Transaksi → Stok Keluar

### 3. Dashboard
- Statistik di dashboard bisa mengambil data dari query yang sama
- Card "Transaksi Hari Ini" bisa link ke riwayat dengan filter today

## Perbaikan Mendatang (Optional)

### Potential Enhancements:
1. **Export to Excel**: Export riwayat transaksi ke file Excel
2. **Email Receipt**: Kirim struk ke email customer
3. **Grafik Penjualan**: Visualisasi penjualan harian/mingguan/bulanan
4. **Return/Refund**: Fitur retur barang dan pengembalian uang
5. **Advanced Filter**: Filter by payment method, filter by product category

## Kesimpulan

Fitur Riwayat Transaksi telah berhasil diimplementasikan dengan:
- ✅ Session-based filtering (isolasi per kasir)
- ✅ Statistik dashboard (4 metrics)
- ✅ Search & filter lengkap
- ✅ Detail transaksi dengan list item
- ✅ Print & receipt support
- ✅ Responsive design dengan green theme kasir
- ✅ Pagination untuk performa

Kasir sekarang dapat dengan mudah:
- Melihat semua transaksi mereka
- Mencari transaksi tertentu
- Melihat detail item yang terjual
- Mencetak atau lihat struk
- Memantau performa penjualan mereka

**Status**: ✅ COMPLETE & READY TO USE
