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
        'purchase_price',
        'stock',
        'low_stock_threshold',
        'image',
        'barcode',
        'is_active'
    ];

    /**
     * Get the stock logs for the product.
     */
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    /**
     * Check if product has low stock.
     */
    public function hasLowStock()
    {
        return $this->stock <= $this->low_stock_threshold;
    }
}
