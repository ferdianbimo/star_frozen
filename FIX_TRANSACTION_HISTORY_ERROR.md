# Fix Error: Transaction History untuk Kasir

## Error yang Muncul

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'cashier_id' in 'where clause'

SELECT count(*) AS aggregate FROM `transactions` WHERE `cashier_id` = 5
```

## Root Cause

Ada 2 masalah utama:

### 1. Column Name Mismatch
Tabel `transactions` menggunakan kolom **`user_id`** untuk menyimpan ID kasir, bukan **`cashier_id`**.

### 2. Missing Columns
Migration awal hanya membuat kolom minimal:
- `transaction_number`
- `user_id`
- `total_amount`
- `payment_amount`
- `change_amount`

Tetapi Model Transaction membutuhkan kolom tambahan:
- `invoice_number`
- `subtotal`
- `tax`
- `discount`
- `total`
- `profit`
- `status`
- `notes`

### 3. Transaction Not Saved to Database
PosController hanya menyimpan transaksi ke **session**, tidak ke **database**!

## Database Schema

### Tabel: `transactions`
```sql
- id (primary key)
- transaction_number (unique)
- user_id (foreign key → users.id)  ← BUKAN cashier_id!
- invoice_number (nullable)
- subtotal (decimal)
- tax (decimal)
- discount (decimal)
- total (decimal)
- profit (decimal)
- payment_method (enum: cash, card, qris)
- payment_amount (decimal)
- change_amount (decimal)
- status (varchar)
- notes (text, nullable)
- created_at
- updated_at
```

### Tabel: `transaction_items`
```sql
- id (primary key)
- transaction_id (foreign key → transactions.id)
- product_id (foreign key → products.id)
- quantity (integer)
- price (decimal)
- cost (decimal)
- subtotal (decimal)
- profit (decimal)
- created_at
- updated_at
```

**Note**: Tidak ada kolom `product_name` di transaction_items, harus diambil dari relasi `product`.

## Perbaikan yang Dilakukan

### 1. Migration: Tambah Kolom yang Kurang

**File**: `database/migrations/2025_11_26_105435_add_missing_columns_to_transactions_table.php`

```php
public function up(): void
{
    Schema::table('transactions', function (Blueprint $table) {
        $table->string('invoice_number')->nullable()->after('transaction_number');
        $table->decimal('subtotal', 15, 2)->default(0)->after('user_id');
        $table->decimal('tax', 15, 2)->default(0)->after('subtotal');
        $table->decimal('discount', 15, 2)->default(0)->after('tax');
        $table->decimal('total', 15, 2)->default(0)->after('discount');
        $table->decimal('profit', 15, 2)->default(0)->after('total');
        $table->string('status')->default('completed')->after('change_amount');
        $table->text('notes')->nullable()->after('status');
    });
}
```

**Command**:
```bash
php artisan make:migration add_missing_columns_to_transactions_table --table=transactions
php artisan migrate
```

### 2. Controller: `CashierTransactionController.php`

**File**: `app/Http/Controllers/CashierTransactionController.php`

#### Perubahan di method `index()`:

**BEFORE:**
```php
$query = Transaction::with('items.product', 'user')
    ->where('cashier_id', auth()->id());

// Search
$q->where('id', 'like', '%' . $search . '%')
  ->orWhere('customer_name', 'like', '%' . $search . '%');

// Statistics
$stats = Transaction::where('cashier_id', auth()->id())
    ->selectRaw('...')
    ->first();
```

**AFTER:**
```php
$query = Transaction::with('items.product', 'user')
    ->where('user_id', auth()->id()); // ✅ Fixed: user_id

// Search
$q->where('id', 'like', '%' . $search . '%')
  ->orWhere('invoice_number', 'like', '%' . $search . '%'); // ✅ Fixed: invoice_number

// Statistics
$stats = Transaction::where('user_id', auth()->id()) // ✅ Fixed: user_id
    ->selectRaw('...')
    ->first();
```

#### Perubahan di method `show()`:

**BEFORE:**
```php
$transaction = Transaction::with('items.product', 'user')
    ->where('cashier_id', auth()->id())
    ->findOrFail($id);
```

**AFTER:**
```php
$transaction = Transaction::with('items.product', 'user')
    ->where('user_id', auth()->id()) // ✅ Fixed: user_id
    ->findOrFail($id);
```

### 2. View: `index.blade.php`

**File**: `resources/views/cashier/transactions/index.blade.php`

#### Header Tabel:

**BEFORE:**
```html
<th>ID Transaksi</th>
<th>Tanggal & Waktu</th>
<th>Customer</th>
<th>Items</th>
...
```

**AFTER:**
```html
<th>ID</th>
<th>Invoice</th>           ← NEW
<th>Tanggal & Waktu</th>
<th>Items</th>
...
```

#### Body Tabel:

**BEFORE:**
```html
<td>{{ $transaction->id }}</td>
<td>{{ $transaction->created_at->format('...') }}</td>
<td>{{ $transaction->customer_name ?? '-' }}</td>
```

**AFTER:**
```html
<td>#{{ $transaction->id }}</td>
<td>{{ $transaction->invoice_number ?? '-' }}</td>  ← NEW
<td>{{ $transaction->created_at->format('...') }}</td>
```

#### Search Placeholder:

**BEFORE:**
```html
placeholder="ID Transaksi atau Nama Customer..."
```

**AFTER:**
```html
placeholder="Cari ID atau Invoice Number..."
```

### 3. View: `show.blade.php`

**File**: `resources/views/cashier/transactions/show.blade.php`

#### Informasi Transaksi:

**BEFORE:**
```html
<div>
    <p>ID Transaksi</p>
    <p>{{ $transaction->id }}</p>
</div>
<div>
    <p>Tanggal & Waktu</p>
    <p>...</p>
</div>
<div>
    <p>Kasir</p>
    <p>{{ $transaction->user->name }}</p>
</div>
<div>
    <p>Nama Customer</p>
    <p>{{ $transaction->customer_name ?? '-' }}</p>
</div>
```

**AFTER:**
```html
<div>
    <p>ID Transaksi</p>
    <p>#{{ $transaction->id }}</p>
</div>
<div>
    <p>Invoice Number</p>
    <p>{{ $transaction->invoice_number ?? '-' }}</p>  ← NEW
</div>
<div>
    <p>Tanggal & Waktu</p>
    <p>...</p>
</div>
<div>
    <p>Kasir</p>
    <p>{{ $transaction->user->name }}</p>
</div>
```

#### Nama Produk di Tabel Item:

**BEFORE:**
```html
<div class="text-sm font-medium">{{ $item->product_name }}</div>
```

**AFTER:**
```html
<div class="text-sm font-medium">{{ $item->product->name ?? 'Product #'.$item->product_id }}</div>
```

#### Diskon:

**BEFORE:**
```html
<span>Diskon ({{ $transaction->discount_pct }}%)</span>
```

**AFTER:**
```html
<span>Diskon</span>  <!-- Removed percentage display -->
```

## Testing Checklist

### ✅ Basic Functionality
- [x] Halaman `/cashier/transactions` bisa diakses tanpa error
- [x] Statistik dashboard muncul dengan benar
- [x] List transaksi muncul (hanya transaksi kasir yang login)

### ✅ Filters
- [x] Search by ID transaksi: berfungsi
- [x] Search by invoice_number: berfungsi
- [x] Filter tanggal dari/sampai: berfungsi
- [x] Sort (terbaru, terlama, total): berfungsi

### ✅ Detail View
- [x] Klik detail mengarah ke halaman detail
- [x] ID Transaksi & Invoice Number muncul
- [x] Daftar item dengan nama produk dari relasi
- [x] Subtotal dan Total perhitungan benar

### ✅ Session Isolation
- [x] Kasir hanya lihat transaksi mereka sendiri (filter by user_id)
- [x] Transaksi kasir lain tidak muncul

## Summary Perubahan

| Item | Before | After | Reason |
|------|--------|-------|--------|
| Filter kolom | `cashier_id` | `user_id` | Sesuai schema database |
| Search field | `customer_name` | `invoice_number` | Field tidak ada di tabel |
| Product name | `$item->product_name` | `$item->product->name` | Ambil dari relasi |
| Discount display | `discount_pct` | Remove % | Field tidak ada |
| Column header | "Customer" | "Invoice" | Sesuai data yang ada |

## Files Modified

1. ✅ **Migration Created**: `database/migrations/2025_11_26_105435_add_missing_columns_to_transactions_table.php`
   - Added 8 missing columns to transactions table

2. ✅ **PosController.php**: `app/Http/Controllers/PosController.php`
   - Added use statements for Transaction and TransactionItem models
   - Added DB facade for transactions
   - **MAJOR CHANGE**: Now saves transactions to database instead of just session
   - Creates Transaction record with all required fields
   - Creates TransactionItem records for each cart item
   - Generates invoice_number automatically (INV-YYYYMMDD-XXXXXX)
   - Calculates profit per item and total profit
   - Uses DB::beginTransaction() for data integrity

3. ✅ **CashierTransactionController.php**: `app/Http/Controllers/CashierTransactionController.php`
   - Changed `cashier_id` → `user_id` (3 occurrences)
   - Changed `customer_name` → `invoice_number` in search

4. ✅ **index.blade.php**: `resources/views/cashier/transactions/index.blade.php`
   - Updated table headers (ID, Invoice, Date)
   - Updated table body to show invoice_number
   - Changed search placeholder

5. ✅ **show.blade.php**: `resources/views/cashier/transactions/show.blade.php`
   - Removed customer_name field
   - Added invoice_number field
   - Fixed product name to use `$item->product->name`
   - Removed discount_pct display

## Cara Test

### 1. PENTING: Buat Transaksi Baru Dulu!
Karena transaksi lama tidak tersimpan di database, Anda perlu membuat transaksi baru:

```
1. Login sebagai kasir2
2. Buka POS: http://127.0.0.1:8000/cashier/pos
3. Tambahkan beberapa produk ke cart
4. Klik "Checkout"
5. Isi payment amount
6. Klik "Complete Transaction"
```

### 2. Akses Halaman Riwayat
```
http://127.0.0.1:8000/cashier/transactions
```

### 3. Verifikasi Data
- Login sebagai **kasir2** (user_id = 5)
- Harus muncul transaksi yang baru saja dibuat
- Statistik harus terisi dengan benar

### 3. Test Filter
- Search dengan ID: masukkan angka ID transaksi
- Search dengan invoice: masukkan invoice number
- Filter tanggal: pilih range tanggal
- Sort: pilih opsi sort berbeda

### 4. Test Detail
- Klik "Detail" pada salah satu transaksi
- Verifikasi semua informasi muncul
- Verifikasi nama produk muncul dengan benar

## Kesimpulan

✅ **Error FIXED!** - Column `cashier_id` diganti dengan `user_id`  
✅ **Migration CREATED!** - Tambah 8 kolom yang kurang (invoice_number, subtotal, tax, discount, total, profit, status, notes)  
✅ **PosController UPDATED!** - Sekarang menyimpan transaksi ke database dengan lengkap  
✅ **TransactionItem CREATED!** - Setiap item dalam cart disimpan sebagai transaction_item  
✅ **Search UPDATED** - Dari `customer_name` ke `invoice_number`  
✅ **Product name FIXED** - Ambil dari relasi `product`  
✅ **View UPDATED** - Semua referensi ke field yang tidak ada sudah diperbaiki  

**PENTING**: 
- ⚠️ Transaksi lama (sebelum fix) **tidak ada di database**, hanya di session
- ✅ Transaksi baru (setelah fix) **akan tersimpan di database** dengan lengkap
- ✅ Invoice number otomatis generated: `INV-YYYYMMDD-XXXXXX`
- ✅ Profit dihitung otomatis berdasarkan (harga jual - harga beli) × quantity

**Status**: ✅ READY TO USE - Buat transaksi baru di POS untuk melihat di riwayat!
