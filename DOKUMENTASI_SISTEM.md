# Dokumentasi Sistem Star Frozen POS#
# 📋 Deskripsi Sistem #

Star Frozen POS adalah sistem Point of Sale berbasis web yang dirancang khusus untuk mengelola toko es krim/frozen food dengan fitur manajemen inventori berbasis batch FIFO (First In First Out), tracking tanggal kadaluarsa, dan pencatatan transaksi lengkap.

## 🔑 Akun Default
# Manager
- **Username/Email**: `manager@starfrozen.com`
- **Password**: `manager123`
- **Role**: Manager
# Kasir 
- **Username/Email**: `kasir@starfrozen.com`
- **Password**: `kasir123`
- **Role**: Kasir

## 🎯 Fitur Utama

### Dashboard Manager
- **Statistik Penjualan**: Ringkasan transaksi harian, mingguan, bulanan
- **Produk Hampir Kadaluarsa**: Alert produk yang akan expired dalam 7 hari (berdasarkan batch)
- **Stok Menipis**: Tampilan card detail batch untuk produk dengan stok ≤ 10 pcs
- **Grafik Penjualan**: Visualisasi data penjualan berbasis waktu

### Dashboard Kasir
- **Statistik Penjualan**: Identik dengan Manager
- **Produk Hampir Kadaluarsa**: Alert batch yang akan expired dalam 7 hari
- **Stok Menipis**: Tampilan card detail batch untuk produk dengan stok ≤ 10 pcs
- **Quick Actions**: Shortcut ke POS dan manajemen inventori

### Point of Sale (POS)
- **Scan Produk**: Pencarian dan pemilihan produk untuk transaksi
- **Keranjang Belanja**: Manajemen item dengan quantity adjustment
- **Pembayaran**: Input uang tunai dengan kalkulasi kembalian otomatis
- **Cetak Struk**: Generate receipt transaksi
- **Transaksi Baru**: Reset keranjang untuk transaksi selanjutnya

### Manajemen Inventori
- **CRUD Produk**: Create, Read, Update, Delete produk
- **Manajemen Batch**: 
  - Stock In dengan batch code otomatis
  - Format Batch: `B{YYYYMMDD}-{product_id}-{sequence}`
  - Contoh: `B20260110-5-001`
- **FIFO System**: Sistem First In First Out untuk penjualan
- **Tracking Kadaluarsa**: Monitoring expiration date per batch
- **Stok Keluar**: History produk yang habis/terjual
- **Update Stok**: Adjustment stok manual
- **Hapus Batch**: Soft delete dengan logging aktivitas

### Keuangan (Manager Only)
- **Laporan Pendapatan**: Detail transaksi dan total income
- **Laporan Pengeluaran**: Manajemen expense dengan kategori
- **Kategori Pengeluaran**: CRUD kategori untuk klasifikasi expense
- **Filter Tanggal**: Laporan berdasarkan periode tertentu
- **Export Excel**: Download laporan dalam format Excel

### Activity Logs
- **Log Sistem**: Pencatatan semua aktivitas penting
- **User Tracking**: Identifikasi user yang melakukan aksi
- **Timestamp**: Waktu detail setiap aktivitas
- **Filter**: Pencarian berdasarkan tipe aktivitas dan user

### Manajemen Pengguna (Manager Only)
- **User Management**: CRUD users dengan role assignment
- **Role Management**: Update dan delete role
- **Access Control**: Pembatasan akses berdasarkan role

---

## 📦 Sistem Batch & FIFO

### Konsep Batch Code
Setiap stock in produk akan mendapatkan batch code unik yang terdiri dari:
- **B**: Prefix batch
- **YYYYMMDD**: Tanggal stock in (contoh: 20260110)
- **product_id**: ID produk (contoh: 5)
- **sequence**: Nomor urut batch pada hari dan produk yang sama (contoh: 001, 002, 003)

### Algoritma FIFO
1. Setiap penjualan otomatis menggunakan batch dengan tanggal kadaluarsa terdekat
2. Batch dengan `expiration_date` paling awal akan dijual terlebih dahulu
3. Jika quantity batch tidak cukup, sistem akan mengambil dari batch berikutnya
4. Batch expired atau quantity = 0 tidak akan muncul dalam POS

### Fitur Batch Deletion History
- **Tracking Penghapusan**: Log semua batch yang dihapus oleh kasir
- **Detail Lengkap**: Batch code, produk, quantity, tanggal kadaluarsa
- **Audit Trail**: Nama kasir dan timestamp penghapusan
- **Filter**: Pencarian berdasarkan tanggal dan kasir

---

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 10.x (PHP)
- **Frontend**: Blade Templates, Tailwind CSS, Livewire
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **Icons**: Font Awesome, SVG Custom Icons
- **Export**: Maatwebsite Excel
- **PDF**: DomPDF (untuk struk)


## 🚀 Instalasi & Setup

### Requirements
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Setup Steps
```bash
# 1. Clone repository
git clone https://github.com/ferdianbimo/star_frozen.git
cd star_frozen

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database configuration
# Edit .env file dengan kredensial database Anda
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=star_frozen
DB_USERNAME=root
DB_PASSWORD=

# 5. Migrate & seed database
php artisan migrate:fresh --seed

# 6. Build assets
npm run build

# 7. Start development server
php artisan serve
```

### Akses Aplikasi
- URL: `http://localhost:8000`
- Login dengan kredensial di atas
