<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * Model ExpenseCategory - Representasi kategori pengeluaran.
 *
 * Model ini mengelola data kategori untuk pengelompokan
 * pengeluaran/biaya operasional toko Star Frozen.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int       $id         Unique identifier
 * @property string    $name       Nama kategori pengeluaran
 * @property \DateTime $created_at Waktu pembuatan record
 * @property \DateTime $updated_at Waktu update terakhir
 *
 * @property-read Collection<Expense> $expenses Koleksi pengeluaran dalam kategori
 */
class ExpenseCategory extends Model
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
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan semua pengeluaran dalam kategori ini.
     *
     * Catatan: Menggunakan pencocokan nama kategori karena
     * field expenses.category adalah string, bukan foreign key.
     *
     * @return HasMany<Expense>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category', 'name');
    }
}
