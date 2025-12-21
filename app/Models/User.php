<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model User - Representasi pengguna sistem Star Frozen.
 *
 * Model ini mengelola data pengguna aplikasi POS Star Frozen,
 * termasuk autentikasi, otorisasi berbasis role, dan profil pengguna.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id                Unique identifier
 * @property string      $name              Nama lengkap pengguna
 * @property string      $email             Alamat email (unique)
 * @property string      $password          Password terenkripsi
 * @property int|null    $role_id           Foreign key ke tabel roles
 * @property string|null $avatar            Path file avatar pengguna
 * @property \DateTime|null $email_verified_at Waktu verifikasi email
 * @property string|null $remember_token    Token untuk "remember me"
 * @property \DateTime   $created_at        Waktu pembuatan record
 * @property \DateTime   $updated_at        Waktu update terakhir
 *
 * @property-read Role|null $role           Relasi ke model Role
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | KONSTANTA
    |--------------------------------------------------------------------------
    */

    /** @var string Role manager */
    public const ROLE_MANAGER = 'manager';

    /** @var string Role kasir */
    public const ROLE_KASIR = 'kasir';

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
        'name',
        'email',
        'password',
        'role_id',
        'avatar',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan role yang dimiliki pengguna.
     *
     * Setiap pengguna memiliki satu role yang menentukan
     * hak akses dan menu yang dapat diakses.
     *
     * @return BelongsTo<Role, User>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Memeriksa apakah pengguna memiliki role tertentu.
     *
     * @param  string $role Nama role yang diperiksa
     * @return bool         True jika pengguna memiliki role tersebut
     *
     * @example
     * ```php
     * if ($user->hasRole('manager')) {
     *     // Akses fitur manager
     * }
     * ```
     */
    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    /**
     * Memeriksa apakah pengguna adalah Manager.
     *
     * Manager memiliki akses penuh ke seluruh fitur aplikasi
     * termasuk laporan, manajemen user, dan pengaturan.
     *
     * @return bool True jika pengguna adalah manager
     */
    public function isManager(): bool
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    /**
     * Memeriksa apakah pengguna adalah Kasir.
     *
     * Kasir memiliki akses terbatas, fokus pada operasional
     * penjualan dan pengelolaan transaksi harian.
     *
     * @return bool True jika pengguna adalah kasir
     */
    public function isCashier(): bool
    {
        return $this->hasRole(self::ROLE_KASIR);
    }
}
