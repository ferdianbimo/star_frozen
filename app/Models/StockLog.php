<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StockLog - Representasi log perubahan stok produk.
 *
 * Model ini mencatat setiap perubahan stok produk untuk keperluan
 * audit trail, pelacakan inventori, dan analisis pergerakan barang.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id               Unique identifier
 * @property int         $product_id       Foreign key ke tabel products
 * @property int|null    $batch_id         Foreign key ke tabel product_batches
 * @property int|null    $user_id          Foreign key ke tabel users
 * @property int         $previous_stock   Stok sebelum perubahan
 * @property int         $new_stock        Stok setelah perubahan
 * @property int         $change           Jumlah perubahan (+ untuk masuk, - untuk keluar)
 * @property float|null  $unit_price       Harga per unit
 * @property float|null  $total_value      Total nilai (unit_price × |change|)
 * @property string      $transaction_type Jenis transaksi (stock_in/stock_out/adjustment/sale)
 * @property string|null $note             Catatan perubahan
 * @property \DateTime   $created_at       Waktu pembuatan record
 * @property \DateTime   $updated_at       Waktu update terakhir
 *
 * @property-read Product           $product Produk terkait
 * @property-read User|null         $user    Pengguna yang melakukan perubahan
 * @property-read ProductBatch|null $batch   Batch terkait
 */
class StockLog extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | KONSTANTA
    |--------------------------------------------------------------------------
    */

    /** @var string Tipe transaksi stok masuk */
    public const TYPE_STOCK_IN = 'stock_in';

    /** @var string Tipe transaksi stok keluar */
    public const TYPE_STOCK_OUT = 'stock_out';

    /** @var string Tipe transaksi penyesuaian stok */
    public const TYPE_ADJUSTMENT = 'adjustment';

    /** @var string Tipe transaksi penjualan */
    public const TYPE_SALE = 'sale';

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
        'product_id',
        'batch_id',
        'user_id',
        'previous_stock',
        'new_stock',
        'change',
        'unit_price',
        'total_value',
        'transaction_type',
        'note',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan produk yang terkait dengan log ini.
     *
     * @return BelongsTo<Product, StockLog>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Mendapatkan pengguna yang melakukan perubahan stok.
     *
     * @return BelongsTo<User, StockLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan batch yang terkait dengan log ini.
     *
     * @return BelongsTo<ProductBatch, StockLog>
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }
}
