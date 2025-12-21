<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ReceiptSetting - Pengaturan struk/nota transaksi.
 *
 * Model ini mengelola konfigurasi tampilan struk transaksi
 * termasuk informasi toko, logo, dan teks footer/header.
 * Menggunakan pola singleton untuk memastikan hanya ada satu setting.
 *
 * @package App\Models
 * @author  Star Frozen Team
 * @version 1.0.0
 *
 * @property int         $id                Unique identifier
 * @property string      $store_name        Nama toko
 * @property string|null $store_address     Alamat toko
 * @property string|null $store_phone       Nomor telepon toko
 * @property string|null $store_email       Email toko
 * @property string|null $logo              Path file logo
 * @property string|null $header_text       Teks header struk
 * @property string|null $footer_text       Teks footer struk
 * @property bool        $show_logo         Tampilkan logo
 * @property bool        $show_address      Tampilkan alamat
 * @property bool        $show_phone        Tampilkan telepon
 * @property bool        $show_cashier_name Tampilkan nama kasir
 * @property bool        $show_thank_you    Tampilkan ucapan terima kasih
 * @property string|null $thank_you_text    Teks ucapan terima kasih
 * @property int|null    $receipt_width     Lebar struk (dalam mm)
 * @property \DateTime   $created_at        Waktu pembuatan record
 * @property \DateTime   $updated_at        Waktu update terakhir
 */
class ReceiptSetting extends Model
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

    /**
     * Casting atribut ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'show_logo' => 'boolean',
        'show_address' => 'boolean',
        'show_phone' => 'boolean',
        'show_cashier_name' => 'boolean',
        'show_thank_you' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | STATIC METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan pengaturan struk saat ini (singleton pattern).
     *
     * Jika belum ada pengaturan, akan dibuat dengan nilai default.
     *
     * @return self Instance pengaturan struk
     *
     * @example
     * ```php
     * $settings = ReceiptSetting::current();
     * echo $settings->store_name; // "Star Frozen"
     * ```
     */
    public static function current(): self
    {
        return self::first() ?? self::create([
            'store_name' => 'Star Frozen',
            'thank_you_text' => 'TERIMAKASIH TELAH BERBELANJA',
        ]);
    }
}
