<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get products in this category.
     * Note: Uses category name matching since products.category is a string field.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'name');
    }

    /**
     * Get products count attribute for when relationships can't be used.
     */
    public function getProductsCountAttribute()
    {
        return Product::where('category', $this->name)->count();
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
