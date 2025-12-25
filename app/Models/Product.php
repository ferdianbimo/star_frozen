<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

/**
 * Model Product - Representasi produk dalam sistem inventori.
 *
 * Model ini mengelola data produk frozen food Star Frozen dengan
 * sistem multi-unit (pcs, renteng, pack, box, karton) yang fleksibel
 * untuk berbagai jenis kemasan produk.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id                     Unique identifier
 * @property string      $name                   Nama produk
 * @property string|null $description            Deskripsi produk
 * @property string|null $category               Nama kategori produk
 * @property float       $price                  Harga jual default
 * @property float|null  $price_pcs              Harga jual per pcs
 * @property float|null  $price_renteng          Harga jual per renteng
 * @property float|null  $price_box              Harga jual per box
 * @property float|null  $price_karton           Harga jual per karton
 * @property float|null  $price_pack             Harga jual per pack
 * @property float|null  $purchase_price         Harga beli default
 * @property float|null  $purchase_price_pcs     Harga beli per pcs
 * @property float|null  $purchase_price_renteng Harga beli per renteng
 * @property float|null  $purchase_price_box     Harga beli per box
 * @property float|null  $purchase_price_karton  Harga beli per karton
 * @property float|null  $purchase_price_pack    Harga beli per pack
 * @property string      $unit                   Unit default produk
 * @property string|null $base_unit              Unit dasar (biasanya 'pcs')
 * @property int|null    $pcs_per_renteng        Jumlah pcs dalam 1 renteng
 * @property int|null    $pcs_per_pack           Jumlah pcs dalam 1 pack
 * @property string|null $karton_contains_unit   Unit yang dikandung karton
 * @property int|null    $karton_contains_qty    Jumlah unit dalam karton
 * @property string|null $box_contains_unit      Unit yang dikandung box
 * @property int|null    $box_contains_qty       Jumlah unit dalam box
 * @property bool        $sell_pcs               Aktifkan penjualan per pcs
 * @property bool        $sell_renteng           Aktifkan penjualan per renteng
 * @property bool        $sell_box               Aktifkan penjualan per box
 * @property bool        $sell_karton            Aktifkan penjualan per karton
 * @property bool        $sell_pack              Aktifkan penjualan per pack
 * @property int         $stock                  Stok produk (legacy)
 * @property int         $low_stock_threshold    Batas stok minimum
 * @property string|null $image                  Path gambar produk
 * @property string|null $barcode                Barcode produk
 * @property bool        $is_active              Status aktif produk
 * @property \DateTime   $created_at             Waktu pembuatan record
 * @property \DateTime   $updated_at             Waktu update terakhir
 *
 * @property-read Collection<StockLog>     $stockLogs         Log perubahan stok
 * @property-read Collection<ProductBatch> $batches           Semua batch produk
 * @property-read int                      $total_batch_stock Total stok dari batch
 * @property-read int                      $effective_stock   Stok efektif tersedia
 * @property-read array                    $available_units   Unit yang bisa dijual
 * @property-read array                    $all_unit_options  Semua opsi unit
 * @property-read int                      $pcs_per_box       Pcs dalam 1 box
 * @property-read int                      $pcs_per_karton    Pcs dalam 1 karton
 */
class Product extends Model
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
        'category',
        'price',
        'price_pcs',
        'price_renteng',
        'price_box',
        'price_karton',
        'price_pack',
        'purchase_price',
        'purchase_price_pcs',
        'purchase_price_renteng',
        'purchase_price_box',
        'purchase_price_karton',
        'purchase_price_pack',
        'unit',
        'base_unit',
        'pcs_per_renteng',
        'pcs_per_pack',
        'karton_contains_unit',
        'karton_contains_qty',
        'box_contains_unit',
        'box_contains_qty',
        'sell_pcs',
        'sell_renteng',
        'sell_box',
        'sell_karton',
        'sell_pack',
        'stock',
        'low_stock_threshold',
        'image',
        'barcode',
        'is_active',
    ];

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sell_pcs' => 'boolean',
        'sell_renteng' => 'boolean',
        'sell_box' => 'boolean',
        'sell_karton' => 'boolean',
        'sell_pack' => 'boolean',
        'price_pcs' => 'decimal:2',
        'price_renteng' => 'decimal:2',
        'price_box' => 'decimal:2',
        'price_karton' => 'decimal:2',
        'price_pack' => 'decimal:2',
        'purchase_price_pcs' => 'decimal:2',
        'purchase_price_renteng' => 'decimal:2',
        'purchase_price_box' => 'decimal:2',
        'purchase_price_karton' => 'decimal:2',
        'purchase_price_pack' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan semua log perubahan stok produk.
     *
     * @return HasMany<StockLog>
     */
    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    /**
     * Mendapatkan semua batch produk.
     *
     * @return HasMany<ProductBatch>
     */
    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    /**
     * Mendapatkan batch yang tersedia (ada stok dan belum kadaluarsa).
     *
     * Query menggunakan scope available(), notExpired(), dan fifo()
     * untuk memastikan urutan FIFO berdasarkan tanggal kadaluarsa.
     *
     * @return HasMany<ProductBatch>
     */
    public function availableBatches(): HasMany
    {
        return $this->batches()
                    ->available()
                    ->notExpired()
                    ->fifo();
    }

    /**
     * Mendapatkan total stok dari semua batch yang tersedia.
     *
     * Menghitung akumulasi stok dari batch yang aktif dan masih memiliki stok,
     * termasuk batch yang sudah kadaluarsa.
     *
     * @return int Total stok tersedia
     */
    public function getTotalBatchStockAttribute(): int
    {
        return $this->batches()->available()->sum('quantity');
    }

    /**
     * Mendapatkan stok efektif produk.
     *
     * Stok selalu berdasarkan akumulasi batch karena batch
     * diperlukan untuk setiap transaksi penjualan.
     *
     * @return int Stok efektif tersedia
     */
    public function getEffectiveStockAttribute(): int
    {
        return $this->total_batch_stock;
    }

    /**
     * Memeriksa apakah stok produk rendah.
     *
     * @return bool True jika stok di bawah atau sama dengan threshold
     */
    public function hasLowStock(): bool
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS - HARGA
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan harga jual untuk unit tertentu.
     *
     * @param  string $unit Tipe unit (pcs/renteng/pack/box/karton)
     * @return float        Harga jual untuk unit tersebut
     */
    public function getPriceForUnit(string $unit): float
    {
        return match($unit) {
            'karton' => (float) ($this->price_karton ?? $this->price ?? 0),
            'box' => (float) ($this->price_box ?? $this->price ?? 0),
            'pack' => (float) ($this->price_pack ?? $this->price ?? 0),
            'renteng' => (float) ($this->price_renteng ?? $this->price ?? 0),
            'pcs' => (float) ($this->price_pcs ?? $this->price ?? 0),
            default => (float) ($this->price ?? 0),
        };
    }

    /**
     * Mendapatkan harga beli untuk unit tertentu.
     *
     * @param  string $unit Tipe unit (pcs/renteng/pack/box/karton)
     * @return float        Harga beli untuk unit tersebut
     */
    public function getPurchasePriceForUnit(string $unit): float
    {
        return match($unit) {
            'karton' => (float) ($this->purchase_price_karton ?? $this->purchase_price ?? 0),
            'box' => (float) ($this->purchase_price_box ?? $this->purchase_price ?? 0),
            'pack' => (float) ($this->purchase_price_pack ?? $this->purchase_price ?? 0),
            'renteng' => (float) ($this->purchase_price_renteng ?? $this->purchase_price ?? 0),
            'pcs' => (float) ($this->purchase_price_pcs ?? $this->purchase_price ?? 0),
            default => (float) ($this->purchase_price ?? 0),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS - UNIT CONVERSION
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan jumlah pcs dalam satu box (backward compatible).
     *
     * @return int Jumlah pcs per box
     */
    public function getPcsPerBoxAttribute(): int
    {
        return $this->getBoxPcsEquivalent();
    }

    /**
     * Mendapatkan jumlah pcs dalam satu karton (backward compatible).
     *
     * @return int Jumlah pcs per karton
     */
    public function getPcsPerKartonAttribute(): int
    {
        return $this->getKartonPcsEquivalent();
    }

    /**
     * Mendapatkan informasi unit lengkap.
     *
     * @param  string $unit Tipe unit
     * @return array{label: string, contents: string, pcs: int} Informasi unit
     */
    public function getUnitInfo(string $unit): array
    {
        return match($unit) {
            'karton' => [
                'label' => 'Karton',
                'contents' => $this->getKartonContentsText(),
                'pcs' => $this->getKartonPcsEquivalent(),
            ],
            'box' => [
                'label' => 'Box',
                'contents' => $this->getBoxContentsText(),
                'pcs' => $this->getBoxPcsEquivalent(),
            ],
            'pack' => [
                'label' => 'Pack',
                'contents' => ($this->pcs_per_pack ?? 1) . ' Pcs',
                'pcs' => $this->pcs_per_pack ?? 1,
            ],
            'renteng' => [
                'label' => 'Renteng',
                'contents' => ($this->pcs_per_renteng ?? 1) . ' Pcs',
                'pcs' => $this->pcs_per_renteng ?? 1,
            ],
            'pcs' => [
                'label' => 'Pcs',
                'contents' => '1 Pcs',
                'pcs' => 1,
            ],
            default => [
                'label' => 'Pcs',
                'contents' => '1 Pcs',
                'pcs' => 1,
            ],
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS - AVAILABLE UNITS
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan daftar unit yang tersedia untuk dijual.
     *
     * Mengembalikan array unit berdasarkan konfigurasi sell_*
     * yang aktif pada produk.
     *
     * @return array<int, array{type: string, label: string, contents: string, price: float|null, purchase_price: float|null}>
     */
    public function getAvailableUnitsAttribute(): array
    {
        $units = [];
        
        if ($this->sell_pcs) {
            $units[] = [
                'type' => 'pcs',
                'label' => 'Pcs',
                'contents' => '1 Pcs',
                'price' => $this->price_pcs ?? $this->price,
                'purchase_price' => $this->purchase_price_pcs ?? $this->purchase_price,
            ];
        }
        
        if ($this->sell_pack && $this->pcs_per_pack) {
            $units[] = [
                'type' => 'pack',
                'label' => 'Pack',
                'contents' => $this->pcs_per_pack . ' Pcs',
                'price' => $this->price_pack,
                'purchase_price' => $this->purchase_price_pack,
            ];
        }
        
        if ($this->sell_renteng && $this->pcs_per_renteng) {
            $units[] = [
                'type' => 'renteng',
                'label' => 'Renteng',
                'contents' => $this->pcs_per_renteng . ' Pcs',
                'price' => $this->price_renteng,
                'purchase_price' => $this->purchase_price_renteng,
            ];
        }
        
        if ($this->sell_box && $this->box_contains_qty) {
            $boxContents = $this->getBoxContentsText();
            $units[] = [
                'type' => 'box',
                'label' => 'Box',
                'contents' => $boxContents,
                'price' => $this->price_box,
                'purchase_price' => $this->purchase_price_box,
            ];
        }
        
        if ($this->sell_karton && $this->karton_contains_qty) {
            $kartonContents = $this->getKartonContentsText();
            $units[] = [
                'type' => 'karton',
                'label' => 'Karton',
                'contents' => $kartonContents,
                'price' => $this->price_karton,
                'purchase_price' => $this->purchase_price_karton,
            ];
        }
        
        return $units;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS - UNIT TEXT DESCRIPTION
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan teks deskripsi isi box.
     *
     * @return string Deskripsi isi box (e.g., "10 Renteng = 100 Pcs")
     */
    public function getBoxContentsText(): string
    {
        $qty = $this->box_contains_qty ?? 0;
        $unit = $this->box_contains_unit ?? 'pcs';
        $unitLabel = ucfirst($unit);
        $pcsEquiv = $this->getBoxPcsEquivalent();
        
        if ($unit === 'pcs') {
            return "{$qty} Pcs";
        }
        return "{$qty} {$unitLabel} = {$pcsEquiv} Pcs";
    }

    /**
     * Mendapatkan teks deskripsi isi karton.
     *
     * @return string Deskripsi isi karton (e.g., "5 Box = 500 Pcs")
     */
    public function getKartonContentsText(): string
    {
        $qty = $this->karton_contains_qty ?? 0;
        $unit = $this->karton_contains_unit ?? 'box';
        $unitLabel = ucfirst($unit);
        $pcsEquiv = $this->getKartonPcsEquivalent();
        
        if ($unit === 'pcs') {
            return "{$qty} Pcs";
        }
        return "{$qty} {$unitLabel} = {$pcsEquiv} Pcs";
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS - UNIT EQUIVALENTS
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung jumlah pcs ekuivalen dalam satu box.
     *
     * Menggunakan struktur fleksibel dimana box dapat berisi
     * renteng, pack, atau langsung pcs.
     *
     * @return int Jumlah pcs dalam satu box
     */
    public function getBoxPcsEquivalent(): int
    {
        $qty = $this->box_contains_qty ?? 1;
        $unit = $this->box_contains_unit ?? 'pcs';
        
        return match($unit) {
            'renteng' => $qty * ($this->pcs_per_renteng ?? 1),
            'pack' => $qty * ($this->pcs_per_pack ?? 1),
            'pcs' => $qty,
            default => $qty,
        };
    }

    /**
     * Menghitung jumlah pcs ekuivalen dalam satu karton.
     *
     * Menggunakan struktur fleksibel dimana karton dapat berisi
     * box, renteng, pack, atau langsung pcs.
     *
     * @return int Jumlah pcs dalam satu karton
     */
    public function getKartonPcsEquivalent(): int
    {
        $qty = $this->karton_contains_qty ?? 1;
        $unit = $this->karton_contains_unit ?? 'box';
        
        return match($unit) {
            'box' => $qty * $this->getBoxPcsEquivalent(),
            'renteng' => $qty * ($this->pcs_per_renteng ?? 1),
            'pack' => $qty * ($this->pcs_per_pack ?? 1),
            'pcs' => $qty,
            default => $qty,
        };
    }

    /**
     * Mengkonversi kuantitas ke unit dasar (pcs).
     *
     * Digunakan untuk kalkulasi stok dan validasi ketersediaan.
     *
     * @param  int    $quantity Jumlah dalam unit tertentu
     * @param  string $unit     Tipe unit asal
     * @return int              Jumlah dalam pcs
     *
     * @example
     * ```php
     * // Konversi 2 karton ke pcs
     * $pcs = $product->convertToBaseUnit(2, 'karton');
     * ```
     */
    public function convertToBaseUnit(int $quantity, string $unit): int
    {
        return match($unit) {
            'karton' => $quantity * $this->getKartonPcsEquivalent(),
            'box' => $quantity * $this->getBoxPcsEquivalent(),
            'pack' => $quantity * ($this->pcs_per_pack ?? 1),
            'renteng' => $quantity * ($this->pcs_per_renteng ?? 1),
            'pcs' => $quantity,
            default => $quantity,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS - ALL UNIT OPTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan semua opsi unit untuk input stok.
     *
     * Mengembalikan semua unit yang dikonfigurasi, termasuk
     * unit yang tidak untuk dijual (untuk keperluan stok masuk).
     *
     * @return array<int, array{type: string, label: string, contents: string, pcs_equivalent: int}>
     */
    public function getAllUnitOptionsAttribute(): array
    {
        $units = [];
        
        // Always include pcs
        $units[] = [
            'type' => 'pcs',
            'label' => 'Pcs',
            'contents' => '1 Pcs',
            'pcs_equivalent' => 1,
        ];
        
        if ($this->pcs_per_pack) {
            $units[] = [
                'type' => 'pack',
                'label' => 'Pack',
                'contents' => $this->pcs_per_pack . ' Pcs',
                'pcs_equivalent' => $this->pcs_per_pack,
            ];
        }
        
        if ($this->pcs_per_renteng) {
            $units[] = [
                'type' => 'renteng',
                'label' => 'Renteng',
                'contents' => $this->pcs_per_renteng . ' Pcs',
                'pcs_equivalent' => $this->pcs_per_renteng,
            ];
        }
        
        if ($this->box_contains_qty) {
            $units[] = [
                'type' => 'box',
                'label' => 'Box',
                'contents' => $this->getBoxContentsText(),
                'pcs_equivalent' => $this->getBoxPcsEquivalent(),
            ];
        }
        
        if ($this->karton_contains_qty) {
            $units[] = [
                'type' => 'karton',
                'label' => 'Karton',
                'contents' => $this->getKartonContentsText(),
                'pcs_equivalent' => $this->getKartonPcsEquivalent(),
            ];
        }
        
        return $units;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS - STOCK VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Memeriksa apakah produk dapat dijual dalam jumlah tertentu.
     *
     * @param  int    $quantity Jumlah yang akan dijual
     * @param  string $unit     Unit penjualan (default: 'pcs')
     * @return bool             True jika stok mencukupi
     */
    public function canSell(int $quantity, string $unit = 'pcs'): bool
    {
        $pcsNeeded = $this->convertToBaseUnit($quantity, $unit);
        return $this->effective_stock >= $pcsNeeded;
    }
}
