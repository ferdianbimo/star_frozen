<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * Model Transaction - Representasi transaksi penjualan.
 *
 * Model ini mengelola data transaksi penjualan pada sistem POS
 * Star Frozen, mencakup informasi pembayaran, item, dan kalkulasi.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id                  Unique identifier
 * @property \DateTime|null $checkout_time    Waktu checkout transaksi
 * @property int         $user_id             Foreign key ke tabel users (kasir)
 * @property string      $transaction_number  Nomor transaksi unik
 * @property string|null $invoice_number      Nomor invoice
 * @property float       $subtotal            Total sebelum diskon dan pajak
 * @property float       $tax                 Jumlah pajak
 * @property float       $discount            Jumlah diskon
 * @property float       $total               Total akhir (subtotal - discount + tax)
 * @property float|null  $total_amount        Total keseluruhan
 * @property float       $profit              Keuntungan bersih
 * @property string      $payment_method      Metode pembayaran (cash/transfer/qris)
 * @property float       $payment_amount      Jumlah pembayaran dari pelanggan
 * @property float       $change_amount       Jumlah kembalian
 * @property string      $status              Status transaksi (pending/completed/cancelled)
 * @property string|null $notes               Catatan tambahan
 * @property \DateTime   $created_at          Waktu pembuatan record
 * @property \DateTime   $updated_at          Waktu update terakhir
 *
 * @property-read User|null                    $user  Kasir yang melakukan transaksi
 * @property-read Collection<TransactionItem> $items Item dalam transaksi
 */
class Transaction extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | KONSTANTA
    |--------------------------------------------------------------------------
    */

    /** @var string Status transaksi pending */
    public const STATUS_PENDING = 'pending';

    /** @var string Status transaksi selesai */
    public const STATUS_COMPLETED = 'completed';

    /** @var string Status transaksi dibatalkan */
    public const STATUS_CANCELLED = 'cancelled';

    /** @var string Pembayaran tunai */
    public const PAYMENT_CASH = 'cash';

    /** @var string Pembayaran transfer */
    public const PAYMENT_TRANSFER = 'transfer';

    /** @var string Pembayaran QRIS */
    public const PAYMENT_QRIS = 'qris';

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
        'checkout_time',
        'user_id',
        'transaction_number',
        'invoice_number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'total_amount',
        'profit',
        'payment_method',
        'payment_amount',
        'change_amount',
        'status',
        'notes',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'checkout_time' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'profit' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan kasir yang melakukan transaksi.
     *
     * @return BelongsTo<User, Transaction>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan semua item dalam transaksi ini.
     *
     * @return HasMany<TransactionItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
