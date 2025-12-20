<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
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
        'is_active'
    ];

    /**
     * The attributes that should be cast.
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

    /**
     * Get the stock logs for the product.
     */
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    /**
     * Get the batches for the product.
     */
    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    /**
     * Get available batches (with stock and not expired).
     */
    public function availableBatches()
    {
        return $this->batches()
                    ->available()
                    ->notExpired()
                    ->fifo();
    }

    /**
     * Get total stock from all available (active, not expired) batches.
     */
    public function getTotalBatchStockAttribute(): int
    {
        return $this->batches()->available()->notExpired()->sum('quantity');
    }

    /**
     * Get the effective stock (always from batches - batch is required for transactions).
     */
    public function getEffectiveStockAttribute(): int
    {
        // Stock is always based on batch accumulation
        return $this->total_batch_stock;
    }

    /**
     * Check if product has low stock.
     */
    public function hasLowStock()
    {
        return $this->stock <= $this->low_stock_threshold;
    }

    /**
     * Get price for specific unit type.
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
     * Get purchase price for specific unit type.
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

    /**
     * Get how many pcs in one box (backwards compatible).
     */
    public function getPcsPerBoxAttribute(): int
    {
        return $this->getBoxPcsEquivalent();
    }

    /**
     * Get how many pcs in one karton (backwards compatible).
     */
    public function getPcsPerKartonAttribute(): int
    {
        return $this->getKartonPcsEquivalent();
    }

    /**
     * Get unit info with contents description.
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

    /**
     * Get available units for sale.
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

    /**
     * Get box contents description text.
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
     * Get karton contents description text.
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

    /**
     * Get how many pcs in one box (using new flexible structure).
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
     * Get how many pcs in one karton (using new flexible structure).
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
     * Convert quantity to base unit (pcs) - updated for flexible structure.
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

    /**
     * Get all unit options for stock input (both selling and non-selling units).
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

    /**
     * Check if product can be sold in given quantity and unit.
     */
    public function canSell(int $quantity, string $unit = 'pcs'): bool
    {
        $pcsNeeded = $this->convertToBaseUnit($quantity, $unit);
        return $this->effective_stock >= $pcsNeeded;
    }
}
