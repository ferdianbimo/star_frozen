<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model TransactionItem - Representasi item dalam transaksi penjualan.
 *
 * Model ini mengelola data item individual yang dibeli dalam
 * sebuah transaksi, termasuk harga, kuantitas, dan kalkulasi profit.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int       $id             Unique identifier
 * @property int       $transaction_id Foreign key ke tabel transactions
 * @property int       $product_id     Foreign key ke tabel products
 * @property int|null  $batch_id       Foreign key ke tabel product_batches
 * @property int       $quantity       Jumlah item yang dibeli
 * @property float     $price          Harga jual per unit
 * @property float     $cost           Harga modal per unit
 * @property float     $subtotal       Total harga (price × quantity)
 * @property float     $profit         Keuntungan (subtotal - cost × quantity)
 * @property \DateTime $created_at     Waktu pembuatan record
 * @property \DateTime $updated_at     Waktu update terakhir
 *
 * @property-read Transaction        $transaction Transaksi induk
 * @property-read Product            $product     Produk yang dibeli
 * @property-read ProductBatch|null  $batch       Batch produk (jika ada)
 */
class TransactionItem extends Model
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
        'transaction_id',
        'product_id',
        'batch_id',
        'quantity',
        'price',
        'cost',
        'subtotal',
        'profit',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan transaksi induk dari item ini.
     *
     * @return BelongsTo<Transaction, TransactionItem>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Mendapatkan produk yang terkait dengan item ini.
     *
     * @return BelongsTo<Product, TransactionItem>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Mendapatkan batch produk yang digunakan untuk item ini.
     *
     * Batch diperlukan untuk tracking FIFO dan tanggal kadaluarsa.
     *
     * @return BelongsTo<ProductBatch, TransactionItem>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }
}
