<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * Model ProductBatch - Representasi batch/lot produk.
 *
 * Model ini mengelola data batch produk untuk sistem FIFO
 * (First In First Out) dan tracking tanggal kadaluarsa
 * pada sistem inventori Star Frozen.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int           $id                Unique identifier
 * @property int           $product_id        Foreign key ke tabel products
 * @property string        $batch_code        Kode batch unik
 * @property int           $quantity          Kuantitas tersisa dalam batch
 * @property string|null   $incoming_unit     Unit saat barang masuk
 * @property int|null      $incoming_quantity Kuantitas saat barang masuk
 * @property float|null    $purchase_price    Harga beli per unit
 * @property \DateTime|null $date_received    Tanggal barang diterima
 * @property \DateTime|null $expiration_date  Tanggal kadaluarsa
 * @property string|null   $notes             Catatan batch
 * @property int|null      $received_by       Foreign key ke tabel users
 * @property bool          $is_active         Status aktif batch
 * @property \DateTime     $created_at        Waktu pembuatan record
 * @property \DateTime     $updated_at        Waktu update terakhir
 *
 * @property-read Product                      $product          Produk induk
 * @property-read User|null                    $receivedBy       User yang menerima
 * @property-read Collection<StockLog>         $stockLogs        Log stok batch
 * @property-read Collection<TransactionItem>  $transactionItems Item transaksi
 * @property-read string                       $expiration_status Status kadaluarsa
 *
 * @method static Builder active()     Scope untuk batch aktif
 * @method static Builder available()  Scope untuk batch tersedia
 * @method static Builder notExpired() Scope untuk batch belum expired
 * @method static Builder fifo()       Scope untuk urutan FIFO
 */
class ProductBatch extends Model
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
        'product_id',
        'batch_code',
        'quantity',
        'incoming_unit',
        'incoming_quantity',
        'purchase_price',
        'date_received',
        'expiration_date',
        'notes',
        'received_by',
        'is_active',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'date_received' => 'date',
        'expiration_date' => 'date',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan produk yang memiliki batch ini.
     *
     * @return BelongsTo<Product, ProductBatch>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Mendapatkan user yang menerima batch ini.
     *
     * @return BelongsTo<User, ProductBatch>
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Mendapatkan semua log stok untuk batch ini.
     *
     * @return HasMany<StockLog>
     */
    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class, 'batch_id');
    }

    /**
     * Mendapatkan semua item transaksi dari batch ini.
     *
     * @return HasMany<TransactionItem>
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'batch_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS - EXPIRATION
    |--------------------------------------------------------------------------
    */

    /**
     * Memeriksa apakah batch sudah kadaluarsa.
     *
     * @return bool True jika sudah melewati tanggal kadaluarsa
     */
    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }
        return $this->expiration_date->isPast();
    }

    /**
     * Memeriksa apakah batch akan segera kadaluarsa.
     *
     * @param  int  $days Jumlah hari threshold (default: 7)
     * @return bool       True jika kadaluarsa dalam rentang hari tersebut
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        if (!$this->expiration_date) {
            return false;
        }
        return $this->expiration_date->isBetween(now(), now()->addDays($days));
    }

    /**
     * Menghitung hari hingga kadaluarsa.
     *
     * @return int|null Jumlah hari (negatif jika sudah expired), null jika tidak ada expiry
     */
    public function daysUntilExpiration(): ?int
    {
        if (!$this->expiration_date) {
            return null;
        }
        return (int) now()->diffInDays($this->expiration_date, false);
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk memfilter batch yang aktif.
     *
     * @param  Builder $query Query builder instance
     * @return Builder        Query dengan filter aktif
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk memfilter batch yang tersedia (ada stok dan aktif).
     *
     * @param  Builder $query Query builder instance
     * @return Builder        Query dengan filter tersedia
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0)->where('is_active', true);
    }

    /**
     * Scope untuk memfilter batch yang belum kadaluarsa.
     *
     * Termasuk batch tanpa tanggal kadaluarsa.
     *
     * @param  Builder $query Query builder instance
     * @return Builder        Query dengan filter not expired
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function($q) {
            $q->whereNull('expiration_date')
              ->orWhere('expiration_date', '>=', now()->startOfDay());
        });
    }

    /**
     * Scope untuk mengurutkan berdasarkan FIFO.
     *
     * First In First Out berdasarkan tanggal kadaluarsa,
     * kemudian tanggal diterima. Batch tanpa expiry di urutan akhir.
     *
     * @param  Builder $query Query builder instance
     * @return Builder        Query dengan urutan FIFO
     */
    public function scopeFifo(Builder $query): Builder
    {
        return $query->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END')
                     ->orderBy('expiration_date', 'asc')
                     ->orderBy('date_received', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | STATIC METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Generate kode batch baru.
     *
     * Format: B{YYYYMMDD}-{product_id}-{sequence}
     *
     * @param  int    $productId ID produk
     * @return string            Kode batch yang digenerate
     *
     * @example
     * ```php
     * $code = ProductBatch::generateBatchCode(123);
     * // Result: "B20251221-123-001"
     * ```
     */
    public static function generateBatchCode(int $productId): string
    {
        $date = now()->format('Ymd');
        $count = self::where('product_id', $productId)
                     ->whereDate('created_at', now()->toDateString())
                     ->count() + 1;
        
        return sprintf('B%s-%d-%03d', $date, $productId, $count);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan status kadaluarsa yang diformat.
     *
     * @return string Status dalam bahasa yang mudah dibaca
     */
    public function getExpirationStatusAttribute(): string
    {
        if (!$this->expiration_date) {
            return 'No expiration';
        }

        $days = $this->daysUntilExpiration();

        if ($days < 0) {
            return 'Expired ' . abs($days) . ' days ago';
        } elseif ($days === 0) {
            return 'Expires today';
        } elseif ($days <= 7) {
            return 'Expires in ' . $days . ' days';
        } else {
            return $this->expiration_date->format('d M Y');
        }
    }
}
