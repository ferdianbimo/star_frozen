<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'date_received' => 'date',
        'expiration_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns this batch.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who received this batch.
     */
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the stock logs for this batch.
     */
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class, 'batch_id');
    }

    /**
     * Get the transaction items for this batch.
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'batch_id');
    }

    /**
     * Check if batch is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }
        return $this->expiration_date->isPast();
    }

    /**
     * Check if batch is expiring soon (within 7 days).
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        if (!$this->expiration_date) {
            return false;
        }
        return $this->expiration_date->isBetween(now(), now()->addDays($days));
    }

    /**
     * Get days until expiration.
     */
    public function daysUntilExpiration(): ?int
    {
        if (!$this->expiration_date) {
            return null;
        }
        return (int) now()->diffInDays($this->expiration_date, false);
    }

    /**
     * Scope a query to only include active batches.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include batches with available stock.
     */
    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0)->where('is_active', true);
    }

    /**
     * Scope a query to only include non-expired batches.
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expiration_date')
              ->orWhere('expiration_date', '>=', now()->startOfDay());
        });
    }

    /**
     * Scope a query to order by FIFO (First In First Out based on expiration date).
     */
    public function scopeFifo($query)
    {
        return $query->orderByRaw('CASE WHEN expiration_date IS NULL THEN 1 ELSE 0 END')
                     ->orderBy('expiration_date', 'asc')
                     ->orderBy('date_received', 'asc');
    }

    /**
     * Generate a batch code.
     */
    public static function generateBatchCode(int $productId): string
    {
        $date = now()->format('Ymd');
        $count = self::where('product_id', $productId)
                     ->whereDate('created_at', now()->toDateString())
                     ->count() + 1;
        
        return sprintf('B%s-%d-%03d', $date, $productId, $count);
    }

    /**
     * Get formatted expiration date with status.
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
