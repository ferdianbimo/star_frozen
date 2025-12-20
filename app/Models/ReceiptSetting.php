<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'store_email',
        'logo',
        'header_text',
        'footer_text',
        'show_logo',
        'show_address',
        'show_phone',
        'show_cashier_name',
        'show_thank_you',
        'thank_you_text',
        'receipt_width',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_address' => 'boolean',
        'show_phone' => 'boolean',
        'show_cashier_name' => 'boolean',
        'show_thank_you' => 'boolean',
    ];

    /**
     * Get the current receipt settings (singleton pattern)
     */
    public static function current(): self
    {
        return self::first() ?? self::create([
            'store_name' => 'Star Frozen',
            'thank_you_text' => 'TERIMAKASIH TELAH BERBELANJA',
        ]);
    }
}
