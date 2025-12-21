<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Expense - Representasi pengeluaran/biaya operasional.
 *
 * Model ini mengelola data pengeluaran toko Star Frozen,
 * termasuk biaya operasional, pembelian supplies, dan lainnya.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int        $id           Unique identifier
 * @property string     $category     Kategori pengeluaran
 * @property string     $description  Deskripsi pengeluaran
 * @property float      $amount       Jumlah pengeluaran
 * @property \DateTime  $expense_date Tanggal pengeluaran
 * @property int        $user_id      Foreign key ke tabel users
 * @property \DateTime  $created_at   Waktu pembuatan record
 * @property \DateTime  $updated_at   Waktu update terakhir
 *
 * @property-read User $user Pengguna yang mencatat pengeluaran
 */
class Expense extends Model
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
        'category',
        'description',
        'amount',
        'expense_date',
        'user_id',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan pengguna yang mencatat pengeluaran ini.
     *
     * @return BelongsTo<User, Expense>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
