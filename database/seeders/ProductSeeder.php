<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh produk untuk Star Frozen POS
        Product::create([
            'name' => 'Nugget Ayam',
            'description' => 'Nugget ayam ukuran 500gr',
            'category' => 'Frozen Food',
            'price' => 35000,
            'purchase_price' => 25000,
            'stock' => 50,
            'low_stock_threshold' => 10,
            'unit' => 'pack',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(9)->toDateString(),
            'barcode' => 'NUGAY500',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Sosis Sapi',
            'description' => 'Sosis sapi isi 10 pcs',
            'category' => 'Frozen Food',
            'price' => 28000,
            'purchase_price' => 20000,
            'stock' => 45,
            'low_stock_threshold' => 10,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'barcode' => 'SOSSP010',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Bakso Sapi',
            'description' => 'Bakso sapi isi 20 pcs',
            'category' => 'Frozen Food',
            'price' => 45000,
            'purchase_price' => 35000,
            'stock' => 30,
            'low_stock_threshold' => 8,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'barcode' => 'BAKSP020',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Dimsum Ayam',
            'description' => 'Dimsum ayam isi 15 pcs',
            'category' => 'Frozen Food',
            'price' => 32000,
            'purchase_price' => 24000,
            'stock' => 25,
            'low_stock_threshold' => 7,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(6)->toDateString(),
            'barcode' => 'DIMAY015',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Kentang Goreng',
            'description' => 'Kentang goreng 1kg',
            'category' => 'Frozen Food',
            'price' => 38000,
            'purchase_price' => 30000,
            'stock' => 40,
            'low_stock_threshold' => 10,
            'unit' => 'sack',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(12)->toDateString(),
            'barcode' => 'KENGOR1K',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Otak-otak Ikan',
            'description' => 'Otak-otak ikan isi 10 pcs',
            'category' => 'Frozen Food',
            'price' => 25000,
            'purchase_price' => 18000,
            'stock' => 35,
            'low_stock_threshold' => 8,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(4)->toDateString(),
            'barcode' => 'OTAIK010',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Tempura Udang',
            'description' => 'Tempura udang isi 12 pcs',
            'category' => 'Frozen Food',
            'price' => 40000,
            'purchase_price' => 30000,
            'stock' => 20,
            'low_stock_threshold' => 5,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(5)->toDateString(),
            'barcode' => 'TEMPUD012',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Es Krim Vanila',
            'description' => 'Es krim vanila kemasan 500ml',
            'category' => 'Ice Cream',
            'price' => 25000,
            'purchase_price' => 15000,
            'stock' => 15,
            'low_stock_threshold' => 5,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(9)->toDateString(),
            'barcode' => 'ESVAN500',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Es Krim Coklat',
            'description' => 'Es krim coklat kemasan 500ml',
            'category' => 'Ice Cream',
            'price' => 25000,
            'purchase_price' => 15000,
            'stock' => 12,
            'low_stock_threshold' => 5,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(9)->toDateString(),
            'barcode' => 'ESCOK500',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Es Krim Stroberi',
            'description' => 'Es krim stroberi kemasan 500ml',
            'category' => 'Ice Cream',
            'price' => 25000,
            'purchase_price' => 15000,
            'stock' => 10,
            'low_stock_threshold' => 5,
            'unit' => 'pcs',
            'date_in' => now()->toDateString(),
            'expiration_date' => now()->addMonths(9)->toDateString(),
            'barcode' => 'ESSTR500',
            'is_active' => true,
        ]);
    }
}
