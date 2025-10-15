<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'previous_stock',
        'new_stock',
        'change',
        'note'
    ];

    /**
     * Get the product that owns the stock log.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the stock change.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
