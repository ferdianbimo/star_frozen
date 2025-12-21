<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * Model Category - Representasi kategori produk.
 *
 * Model ini mengelola data kategori untuk pengelompokan produk
 * dalam sistem inventori Star Frozen.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id          Unique identifier
 * @property string      $name        Nama kategori
 * @property string|null $description Deskripsi kategori
 * @property bool        $is_active   Status aktif kategori
 * @property \DateTime   $created_at  Waktu pembuatan record
 * @property \DateTime   $updated_at  Waktu update terakhir
 *
 * @property-read Collection<Product> $products      Koleksi produk dalam kategori
 * @property-read int                 $products_count Jumlah produk dalam kategori
 *
 * @method static Builder active() Scope untuk kategori aktif
 */
class Category extends Model
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
        'description',
        'is_active',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan semua produk dalam kategori ini.
     *
     * Catatan: Menggunakan pencocokan nama kategori karena
     * field products.category adalah string, bukan foreign key.
     *
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category', 'name');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan jumlah produk dalam kategori.
     *
     * Accessor ini berguna ketika relasi tidak dapat digunakan
     * atau untuk optimasi query.
     *
     * @return int Jumlah produk
     */
    public function getProductsCountAttribute(): int
    {
        return Product::where('category', $this->name)->count();
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk memfilter kategori yang aktif saja.
     *
     * @param  Builder $query Query builder instance
     * @return Builder        Query dengan filter aktif
     *
     * @example
     * ```php
     * $activeCategories = Category::active()->get();
     * ```
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
