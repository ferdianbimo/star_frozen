<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ActivityLog - Representasi log aktivitas pengguna.
 *
 * Model ini mencatat semua aktivitas penting dalam sistem
 * untuk keperluan audit trail, monitoring, dan analisis
 * penggunaan aplikasi Star Frozen.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id          Unique identifier
 * @property int|null    $user_id     Foreign key ke tabel users
 * @property string      $action      Jenis aksi (create/update/delete/login/etc)
 * @property string      $module      Modul yang terpengaruh (inventory/pos/finance/etc)
 * @property string|null $model_type  Fully qualified class name model terkait
 * @property int|null    $model_id    ID model terkait
 * @property string      $description Deskripsi aktivitas
 * @property array|null  $old_values  Nilai sebelum perubahan (untuk update/delete)
 * @property array|null  $new_values  Nilai setelah perubahan (untuk create/update)
 * @property array|null  $metadata    Data tambahan kontekstual
 * @property string|null $ip_address  IP address pengguna
 * @property string|null $user_agent  User agent browser
 * @property \DateTime   $created_at  Waktu pembuatan record
 * @property \DateTime   $updated_at  Waktu update terakhir
 *
 * @property-read User|null $user         Pengguna yang melakukan aktivitas
 * @property-read string    $action_color Warna badge untuk aksi
 * @property-read string    $module_icon  Icon untuk modul
 *
 * @method static Builder module(string $module)                    Filter berdasarkan modul
 * @method static Builder action(string $action)                    Filter berdasarkan aksi
 * @method static Builder byUser(int $userId)                       Filter berdasarkan user
 * @method static Builder dateRange(\DateTime $start, \DateTime $end) Filter berdasarkan rentang tanggal
 */
class ActivityLog extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | KONSTANTA
    |--------------------------------------------------------------------------
    */

    /** @var string Aksi membuat data baru */
    public const ACTION_CREATE = 'create';

    /** @var string Aksi mengubah data */
    public const ACTION_UPDATE = 'update';

    /** @var string Aksi menghapus data */
    public const ACTION_DELETE = 'delete';

    /** @var string Aksi stok masuk */
    public const ACTION_STOCK_IN = 'stock_in';

    /** @var string Aksi stok keluar */
    public const ACTION_STOCK_OUT = 'stock_out';

    /** @var string Aksi login */
    public const ACTION_LOGIN = 'login';

    /** @var string Aksi logout */
    public const ACTION_LOGOUT = 'logout';

    /** @var string Aksi checkout transaksi */
    public const ACTION_CHECKOUT = 'checkout';

    /** @var string Aksi export data */
    public const ACTION_EXPORT = 'export';

    /** @var string Modul inventori */
    public const MODULE_INVENTORY = 'inventory';

    /** @var string Modul POS */
    public const MODULE_POS = 'pos';

    /** @var string Modul keuangan */
    public const MODULE_FINANCE = 'finance';

    /** @var string Modul users */
    public const MODULE_USERS = 'users';

    /** @var string Modul autentikasi */
    public const MODULE_AUTH = 'auth';

    /** @var string Modul batch */
    public const MODULE_BATCH = 'batch';

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI MODEL
    |--------------------------------------------------------------------------
    */

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan pengguna yang melakukan aktivitas.
     *
     * @return BelongsTo<User, ActivityLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan model terkait dari log ini.
     *
     * Menggunakan polymorphic lookup berdasarkan model_type dan model_id.
     *
     * @return Model|null Model terkait atau null jika tidak ditemukan
     */
    public function model(): ?Model
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk filter berdasarkan modul.
     *
     * @param  Builder $query  Query builder instance
     * @param  string  $module Nama modul
     * @return Builder         Query dengan filter modul
     */
    public function scopeModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    /**
     * Scope untuk filter berdasarkan aksi.
     *
     * @param  Builder $query  Query builder instance
     * @param  string  $action Nama aksi
     * @return Builder         Query dengan filter aksi
     */
    public function scopeAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan user.
     *
     * @param  Builder $query  Query builder instance
     * @param  int     $userId ID pengguna
     * @return Builder         Query dengan filter user
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal.
     *
     * @param  Builder              $query     Query builder instance
     * @param  \DateTime|string     $startDate Tanggal awal
     * @param  \DateTime|string     $endDate   Tanggal akhir
     * @return Builder                         Query dengan filter tanggal
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan warna badge untuk aksi.
     *
     * Digunakan untuk styling visual di UI.
     *
     * @return string Nama warna (green/blue/red/etc)
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            self::ACTION_CREATE, self::ACTION_STOCK_IN => 'green',
            self::ACTION_UPDATE => 'blue',
            self::ACTION_DELETE, self::ACTION_STOCK_OUT => 'red',
            self::ACTION_LOGIN => 'indigo',
            self::ACTION_LOGOUT => 'gray',
            self::ACTION_CHECKOUT => 'purple',
            self::ACTION_EXPORT => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Mendapatkan icon untuk modul.
     *
     * Menggunakan Font Awesome class names.
     *
     * @return string Class icon Font Awesome
     */
    public function getModuleIconAttribute(): string
    {
        return match($this->module) {
            self::MODULE_INVENTORY => 'fas fa-boxes',
            self::MODULE_POS => 'fas fa-cash-register',
            self::MODULE_FINANCE => 'fas fa-dollar-sign',
            self::MODULE_USERS => 'fas fa-users',
            self::MODULE_AUTH => 'fas fa-lock',
            self::MODULE_BATCH => 'fas fa-layer-group',
            default => 'fas fa-circle',
        };
    }
}
