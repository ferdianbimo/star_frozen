<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * Model Role - Representasi peran/jabatan pengguna dalam sistem.
 *
 * Model ini mengelola data role yang menentukan hak akses
 * dan fitur yang dapat diakses oleh setiap pengguna.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id           Unique identifier
 * @property string      $name         Nama role (untuk sistem, e.g., 'manager', 'kasir')
 * @property string|null $display_name Nama tampilan role (e.g., 'Manager Toko')
 * @property string|null $description  Deskripsi role
 * @property \DateTime   $created_at   Waktu pembuatan record
 * @property \DateTime   $updated_at   Waktu update terakhir
 *
 * @property-read Collection<User> $users Koleksi pengguna dengan role ini
 */
class Role extends Model
{
    use HasFactory;

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
        'display_name',
        'description',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan semua pengguna yang memiliki role ini.
     *
     * @return HasMany<User>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
