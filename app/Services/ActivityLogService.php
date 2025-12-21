<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * ActivityLogService - Service untuk mencatat aktivitas sistem.
 *
 * Service ini menyediakan method statis untuk logging berbagai
 * aktivitas dalam sistem Star Frozen POS:
 * - CRUD operations (create, update, delete)
 * - Stock operations (stock_in, stock_out)
 * - Authentication (login, logout)
 * - POS operations (checkout)
 * - Export operations
 *
 * Semua log akan mencatat:
 * - User yang melakukan aksi
 * - IP Address dan User Agent
 * - Timestamp otomatis
 * - Old/New values untuk tracking perubahan
 *
 * @package App\Services
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @example Basic logging
 * ```php
 * ActivityLogService::log('create', 'inventory', 'Menambah produk baru', $product);
 * ```
 *
 * @example Shorthand methods
 * ```php
 * ActivityLogService::logCreate('inventory', 'Produk ditambahkan', $product);
 * ActivityLogService::logUpdate('inventory', 'Produk diupdate', $product, $oldData, $newData);
 * ActivityLogService::logDelete('inventory', 'Produk dihapus', $product);
 * ```
 */
class ActivityLogService
{
    /*
    |--------------------------------------------------------------------------
    | MAIN LOG METHOD
    |--------------------------------------------------------------------------
    |
    | Method utama untuk mencatat aktivitas dengan parameter lengkap.
    |
    */

    /**
     * Mencatat aktivitas ke database.
     *
     * Method utama untuk logging yang digunakan oleh semua
     * shorthand methods. Secara otomatis mencatat:
     * - User ID dari Auth::id()
     * - IP Address dari Request::ip()
     * - User Agent dari Request::userAgent()
     *
     * @param  string      $action      Aksi yang dilakukan (create, update, delete, etc.)
     * @param  string      $module      Modul tempat aksi terjadi (inventory, pos, finance, etc.)
     * @param  string      $description Deskripsi aktivitas yang mudah dibaca
     * @param  Model|null  $model       Model yang terpengaruh (optional)
     * @param  array|null  $oldValues   Nilai sebelum perubahan untuk tracking update
     * @param  array|null  $newValues   Nilai setelah perubahan untuk tracking create/update
     * @param  array|null  $metadata    Data tambahan dalam bentuk array
     * @return ActivityLog Log yang baru dibuat
     *
     * @example
     * ```php
     * ActivityLogService::log(
     *     'update',
     *     'inventory',
     *     'Mengupdate stok produk Bakso Sapi',
     *     $product,
     *     ['stock' => 100],
     *     ['stock' => 150]
     * );
     * ```
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD SHORTHAND METHODS
    |--------------------------------------------------------------------------
    |
    | Method singkat untuk logging operasi CRUD standar.
    |
    */

    /**
     * Mencatat aksi create (pembuatan data baru).
     *
     * @param  string      $module      Modul tempat aksi terjadi
     * @param  string      $description Deskripsi aktivitas
     * @param  Model|null  $model       Model yang dibuat
     * @param  array|null  $newValues   Data model yang baru dibuat
     * @return ActivityLog
     */
    public static function logCreate(string $module, string $description, $model = null, ?array $newValues = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_CREATE, $module, $description, $model, null, $newValues);
    }

    /**
     * Mencatat aksi update (perubahan data).
     *
     * @param  string      $module      Modul tempat aksi terjadi
     * @param  string      $description Deskripsi aktivitas
     * @param  Model|null  $model       Model yang diupdate
     * @param  array|null  $oldValues   Data sebelum perubahan
     * @param  array|null  $newValues   Data setelah perubahan
     * @return ActivityLog
     */
    public static function logUpdate(string $module, string $description, $model = null, ?array $oldValues = null, ?array $newValues = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_UPDATE, $module, $description, $model, $oldValues, $newValues);
    }

    /**
     * Mencatat aksi delete (penghapusan data).
     *
     * @param  string      $module      Modul tempat aksi terjadi
     * @param  string      $description Deskripsi aktivitas
     * @param  Model|null  $model       Model yang dihapus
     * @param  array|null  $oldValues   Data yang dihapus (untuk audit trail)
     * @return ActivityLog
     */
    public static function logDelete(string $module, string $description, $model = null, ?array $oldValues = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_DELETE, $module, $description, $model, $oldValues);
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK OPERATIONS METHODS
    |--------------------------------------------------------------------------
    |
    | Method untuk logging operasi stok inventory.
    |
    */

    /**
     * Mencatat aksi stock in (penambahan stok).
     *
     * @param  string      $description Deskripsi aktivitas
     * @param  Model|null  $model       ProductBatch atau Product yang ditambah
     * @param  array|null  $newValues   Data stok yang ditambahkan
     * @return ActivityLog
     */
    public static function logStockIn(string $description, $model = null, ?array $newValues = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_STOCK_IN, ActivityLog::MODULE_INVENTORY, $description, $model, null, $newValues);
    }

    /**
     * Mencatat aksi stock out (pengurangan stok).
     *
     * @param  string      $description Deskripsi aktivitas
     * @param  Model|null  $model       ProductBatch atau Product yang dikurangi
     * @param  array|null  $metadata    Data tambahan (misal: alasan, qty)
     * @return ActivityLog
     */
    public static function logStockOut(string $description, $model = null, ?array $metadata = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_STOCK_OUT, ActivityLog::MODULE_INVENTORY, $description, $model, null, null, $metadata);
    }

    /*
    |--------------------------------------------------------------------------
    | POS OPERATIONS METHODS
    |--------------------------------------------------------------------------
    |
    | Method untuk logging operasi Point of Sale.
    |
    */

    /**
     * Mencatat aksi checkout (transaksi penjualan).
     *
     * @param  string      $description Deskripsi transaksi
     * @param  Model|null  $transaction Model Transaction yang dibuat
     * @param  array|null  $metadata    Data tambahan (items, payment method, dll)
     * @return ActivityLog
     */
    public static function logCheckout(string $description, $transaction = null, ?array $metadata = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_CHECKOUT, ActivityLog::MODULE_POS, $description, $transaction, null, null, $metadata);
    }

    /*
    |--------------------------------------------------------------------------
    | BATCH OPERATIONS METHODS
    |--------------------------------------------------------------------------
    |
    | Method untuk logging operasi batch produk.
    |
    */

    /**
     * Mencatat pembuatan batch baru.
     *
     * @param  string      $description Deskripsi batch yang dibuat
     * @param  Model|null  $batch       Model ProductBatch yang dibuat
     * @param  array|null  $newValues   Data batch yang dibuat
     * @return ActivityLog
     */
    public static function logBatchCreate(string $description, $batch = null, ?array $newValues = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_CREATE, ActivityLog::MODULE_BATCH, $description, $batch, null, $newValues);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION METHODS
    |--------------------------------------------------------------------------
    |
    | Method untuk logging aktivitas autentikasi.
    |
    */

    /**
     * Mencatat aksi login user.
     *
     * @param  int|null $userId ID user yang login (optional, default dari Auth)
     * @return ActivityLog
     */
    public static function logLogin(?int $userId = null): ActivityLog
    {
        $user = $userId ? User::find($userId) : Auth::user();
        $description = $user ? "User {$user->name} logged in" : "User logged in";
        
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => ActivityLog::ACTION_LOGIN,
            'module' => ActivityLog::MODULE_AUTH,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Mencatat aksi logout user.
     *
     * @return ActivityLog
     */
    public static function logLogout(): ActivityLog
    {
        $user = Auth::user();
        $description = $user ? "User {$user->name} logged out" : "User logged out";
        
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => ActivityLog::ACTION_LOGOUT,
            'module' => ActivityLog::MODULE_AUTH,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT METHODS
    |--------------------------------------------------------------------------
    |
    | Method untuk logging aktivitas export data.
    |
    */

    /**
     * Mencatat aksi export data.
     *
     * @param  string      $module      Modul yang di-export (finance, inventory, etc)
     * @param  string      $description Deskripsi export
     * @param  array|null  $metadata    Data tambahan (format, filter, dll)
     * @return ActivityLog
     */
    public static function logExport(string $module, string $description, ?array $metadata = null): ActivityLog
    {
        return self::log(ActivityLog::ACTION_EXPORT, $module, $description, null, null, null, $metadata);
    }
}
