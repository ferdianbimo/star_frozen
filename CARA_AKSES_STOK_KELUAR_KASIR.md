# Cara Akses Stok Keluar Kasir - PANDUAN LENGKAP

## ⚠️ MASALAH: Error 404 saat Akses Stok Keluar Kasir

### Penyebab Error 404:
Anda mengakses URL **MANAGER** padahal login sebagai **KASIR2**

### URL yang SALAH ❌:
```
http://127.0.0.1:8000/manager/inventory/stock-out
                     ^^^^^^^^ ← SALAH! Ini untuk Manager
```

### URL yang BENAR ✅:
```
http://127.0.0.1:8000/cashier/inventory/stock-out
                     ^^^^^^^^ ← BENAR! Ini untuk Kasir
```

---

## 🎯 SOLUSI - 3 Cara Akses Stok Keluar Kasir

### **Cara 1: Dari Dashboard Kasir** (TERMUDAH)

1. **Login sebagai kasir2**
   - URL: `http://127.0.0.1:8000/login`
   - Email: [kasir2@example.com atau sesuai database Anda]
   - Password: [password kasir2]

2. **Ke Dashboard Kasir**
   - Setelah login akan otomatis redirect ke dashboard kasir
   - URL: `http://127.0.0.1:8000/cashier/dashboard`

3. **Klik Card "Stok Keluar"**
   - Di dashboard ada 4 card:
     - ✅ New Transaction (hijau)
     - ✅ Inventory (kuning)
     - ✅ **Stok Keluar (merah)** ← KLIK INI!
     - ✅ Today's Transactions (biru)

4. **Halaman Stok Keluar Terbuka**
   - Menampilkan riwayat stok keluar dari transaksi POS kasir2
   - Ada filter search & sort

---

### **Cara 2: Dari Menu Inventory**

1. **Login sebagai kasir2**

2. **Klik menu "Inventory"** di sidebar kiri

3. **Klik tab "Stok Keluar"**
   - Ada 2 tab:
     - Stok Masuk
     - **Stok Keluar** ← KLIK INI!

4. **Halaman Stok Keluar Terbuka**

---

### **Cara 3: Direct URL** (TERCEPAT)

1. **Login sebagai kasir2**

2. **Ketik langsung di browser:**
```
http://127.0.0.1:8000/cashier/inventory/stock-out
```

3. **Enter!**

---

## 🔐 Penting: Login dengan Role yang Benar

### Kasir vs Manager:

| Fitur | URL Manager | URL Kasir |
|-------|-------------|-----------|
| Dashboard | `/manager/dashboard` | `/cashier/dashboard` |
| Inventory | `/manager/inventory` | `/cashier/inventory` |
| **Stok Keluar** | `/manager/inventory/stock-out` | **`/cashier/inventory/stock-out`** |

### Perbedaan Data:

**Manager (di screenshot Anda):**
- Melihat SEMUA stok keluar dari SEMUA kasir
- Kolom "USER" menampilkan: Kasir, kasir2
- Total: 11 transaksi (gabungan semua kasir)

**Kasir2 (yang harus Anda akses):**
- Melihat HANYA stok keluar dari kasir2 sendiri
- Filter otomatis: `WHERE user_id = kasir2_id AND transaction_type = 'sale'`
- Total: Hanya transaksi kasir2 (misalnya 6 transaksi)

---

## 📊 Apa yang Akan Muncul di Halaman Stok Keluar Kasir2?

Berdasarkan screenshot manager Anda, transaksi kasir2:
1. **Kentang Goreng** - 1 pack (26/11/2025 05:10) 
2. **Kentang Goreng** - 1 pack (26/11/2025 04:59)
3. **Kentang Goreng** - 1 pack (26/11/2025 04:55)
4. **Dimsum Ayam** - 1 pack (26/11/2025 04:49)
5. **Bakso Sapi** - 1 pack (26/11/2025 04:42)

Total: **5 transaksi** (hanya dari kasir2, tidak termasuk transaksi "Kasir")

---

## 🧪 Test - Verifikasi Login Anda

### Step 1: Cek Status Login
Buka di browser:
```
http://127.0.0.1:8000/test-auth
```

Hasil yang diharapkan:
```json
{
  "authenticated": true,
  "user_id": 5,  ← ID kasir2
  "user_name": "kasir2",
  "user_email": "kasir2@example.com",
  "role_name": "kasir",
  "role_id": 2
}
```

Jika `"authenticated": false`:
- ❌ Anda BELUM login
- ✅ Solusi: Login dulu di `/login`

---

## 🚫 Kesalahan Umum

### ❌ Kesalahan 1: Akses URL Manager
```
URL: http://127.0.0.1:8000/manager/inventory/stock-out
Role: kasir2

Result: 403 Forbidden atau 404
Kenapa: kasir2 tidak boleh akses route manager
```

### ✅ Yang Benar:
```
URL: http://127.0.0.1:8000/cashier/inventory/stock-out
Role: kasir2

Result: 200 OK
Kenapa: Route sesuai dengan role kasir2
```

### ❌ Kesalahan 2: Belum Login
```
URL: http://127.0.0.1:8000/cashier/inventory/stock-out
Login: Belum login

Result: Redirect ke /login atau 404
```

### ✅ Yang Benar:
```
URL: http://127.0.0.1:8000/cashier/inventory/stock-out
Login: Sudah login sebagai kasir2

Result: Halaman stok keluar muncul
```

---

## 📝 Summary

### YANG HARUS ANDA LAKUKAN SEKARANG:

1. ✅ **Pastikan login sebagai kasir2**
   ```
   Check: http://127.0.0.1:8000/test-auth
   ```

2. ✅ **Akses URL yang BENAR**
   ```
   http://127.0.0.1:8000/cashier/inventory/stock-out
   BUKAN: /manager/inventory/stock-out
   ```

3. ✅ **Atau klik dari Dashboard**
   ```
   Dashboard → Card "Stok Keluar" (merah)
   ```

### Expected Result:
- ✅ Halaman muncul dengan header "Stok Keluar dari POS"
- ✅ Total Transaksi POS: 5 (atau sesuai jumlah transaksi kasir2)
- ✅ Tabel menampilkan hanya transaksi kasir2
- ✅ Filter search & sort berfungsi

---

## 🆘 Jika Masih Error 404

### Debug Checklist:

1. **Cek Login:**
   ```
   http://127.0.0.1:8000/test-auth
   → authenticated: true? 
   → role_name: "kasir"?
   ```

2. **Cek URL:**
   ```
   Harus: /cashier/inventory/stock-out
   Bukan: /manager/inventory/stock-out
   ```

3. **Cek Route:**
   ```powershell
   php artisan route:list --name=cashier.inventory.stock-out
   ```

4. **Clear Cache:**
   ```powershell
   php artisan optimize:clear
   php artisan view:clear
   ```

5. **Restart Browser:**
   ```
   Ctrl + Shift + Delete → Clear Cache
   Atau buka Incognito/Private Window
   ```

---

## 📸 Screenshot Reference

**Yang Anda Lihat (Manager):**
- URL: `/manager/inventory/stock-out`
- Sidebar: Biru (Manager Dashboard)
- Data: Semua kasir (Kasir + kasir2)

**Yang Harus Anda Akses (Kasir):**
- URL: `/cashier/inventory/stock-out`  
- Sidebar: Hijau (Cashier Dashboard)
- Data: Hanya kasir2

---

**Terakhir Diupdate:** 26 November 2025
**Status:** ✅ Route sudah benar, tinggal akses URL yang benar!
