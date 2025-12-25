<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Nugget',
                'description' => 'Produk nugget berbagai varian',
                'is_active' => true,
            ],
            [
                'name' => 'Sosis',
                'description' => 'Produk sosis berbagai jenis',
                'is_active' => true,
            ],
            [
                'name' => 'Bakso',
                'description' => 'Produk bakso frozen',
                'is_active' => true,
            ],
            [
                'name' => 'Olahan Ayam',
                'description' => 'Produk olahan ayam frozen',
                'is_active' => true,
            ],
            [
                'name' => 'Dimsum',
                'description' => 'Produk dimsum dan siomay',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        // Create Products with Batches
        $products = [
            // Nugget Products
            [
                'name' => 'Nugget Ayam Original',
                'description' => 'Nugget ayam original premium',
                'category' => 'Nugget',
                'barcode' => 'NUG001',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 20,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 10,
                'price' => 25000,
                'price_pcs' => 1500,
                'price_pack' => 25000,
                'price_karton' => 240000,
                'purchase_price' => 20000,
                'purchase_price_pcs' => 1200,
                'purchase_price_pack' => 20000,
                'purchase_price_karton' => 190000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 20,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 50, 'days_until_expiry' => 180],
                    ['quantity' => 30, 'days_until_expiry' => 210],
                ]
            ],
            [
                'name' => 'Nugget Ayam Spicy',
                'description' => 'Nugget ayam pedas',
                'category' => 'Nugget',
                'barcode' => 'NUG002',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 20,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 10,
                'price' => 27000,
                'price_pcs' => 1600,
                'price_pack' => 27000,
                'price_karton' => 260000,
                'purchase_price' => 22000,
                'purchase_price_pcs' => 1300,
                'purchase_price_pack' => 22000,
                'purchase_price_karton' => 210000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 15,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 40, 'days_until_expiry' => 170],
                ]
            ],
            // Sosis Products
            [
                'name' => 'Sosis Ayam Jumbo',
                'description' => 'Sosis ayam jumbo premium',
                'category' => 'Sosis',
                'barcode' => 'SOS001',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 10,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 12,
                'price' => 30000,
                'price_pcs' => 3500,
                'price_pack' => 30000,
                'price_karton' => 350000,
                'purchase_price' => 25000,
                'purchase_price_pcs' => 3000,
                'purchase_price_pack' => 25000,
                'purchase_price_karton' => 290000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 15,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 60, 'days_until_expiry' => 200],
                    ['quantity' => 25, 'days_until_expiry' => 150],
                ]
            ],
            [
                'name' => 'Sosis Sapi Premium',
                'description' => 'Sosis sapi 100% halal',
                'category' => 'Sosis',
                'barcode' => 'SOS002',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 8,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 10,
                'price' => 45000,
                'price_pcs' => 6000,
                'price_pack' => 45000,
                'price_karton' => 440000,
                'purchase_price' => 38000,
                'purchase_price_pcs' => 5000,
                'purchase_price_pack' => 38000,
                'purchase_price_karton' => 370000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 10,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 35, 'days_until_expiry' => 190],
                ]
            ],
            // Bakso Products
            [
                'name' => 'Bakso Sapi Kenyal',
                'description' => 'Bakso sapi kenyal premium',
                'category' => 'Bakso',
                'barcode' => 'BAK001',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 25,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 10,
                'price' => 35000,
                'price_pcs' => 1600,
                'price_pack' => 35000,
                'price_karton' => 340000,
                'purchase_price' => 28000,
                'purchase_price_pcs' => 1300,
                'purchase_price_pack' => 28000,
                'purchase_price_karton' => 270000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 20,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 45, 'days_until_expiry' => 160],
                    ['quantity' => 30, 'days_until_expiry' => 220],
                ]
            ],
            [
                'name' => 'Bakso Urat Jumbo',
                'description' => 'Bakso urat ukuran jumbo',
                'category' => 'Bakso',
                'barcode' => 'BAK002',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 15,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 12,
                'price' => 42000,
                'price_pcs' => 3000,
                'price_pack' => 42000,
                'price_karton' => 490000,
                'purchase_price' => 35000,
                'purchase_price_pcs' => 2500,
                'purchase_price_pack' => 35000,
                'purchase_price_karton' => 410000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 15,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 28, 'days_until_expiry' => 175],
                ]
            ],
            // Olahan Ayam
            [
                'name' => 'Chicken Katsu Premium',
                'description' => 'Chicken katsu siap goreng',
                'category' => 'Olahan Ayam',
                'barcode' => 'CHK001',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 5,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 10,
                'price' => 40000,
                'price_pcs' => 8500,
                'price_pack' => 40000,
                'price_karton' => 390000,
                'purchase_price' => 33000,
                'purchase_price_pcs' => 7000,
                'purchase_price_pack' => 33000,
                'purchase_price_karton' => 320000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 10,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 40, 'days_until_expiry' => 140],
                    ['quantity' => 20, 'days_until_expiry' => 200],
                ]
            ],
            [
                'name' => 'Chicken Wings BBQ',
                'description' => 'Sayap ayam bumbu BBQ',
                'category' => 'Olahan Ayam',
                'barcode' => 'CHK002',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 12,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 8,
                'price' => 48000,
                'price_pcs' => 4500,
                'price_pack' => 48000,
                'price_karton' => 380000,
                'purchase_price' => 40000,
                'purchase_price_pcs' => 3800,
                'purchase_price_pack' => 40000,
                'purchase_price_karton' => 315000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 12,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 32, 'days_until_expiry' => 165],
                ]
            ],
            // Dimsum
            [
                'name' => 'Dimsum Mix Isi 10',
                'description' => 'Dimsum aneka isi 10 pcs',
                'category' => 'Dimsum',
                'barcode' => 'DIM001',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 10,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 15,
                'price' => 28000,
                'price_pcs' => 3000,
                'price_pack' => 28000,
                'price_karton' => 410000,
                'purchase_price' => 23000,
                'purchase_price_pcs' => 2500,
                'purchase_price_pack' => 23000,
                'purchase_price_karton' => 340000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 20,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 55, 'days_until_expiry' => 185],
                    ['quantity' => 40, 'days_until_expiry' => 155],
                ]
            ],
            [
                'name' => 'Siomay Ayam Premium',
                'description' => 'Siomay ayam isi udang',
                'category' => 'Dimsum',
                'barcode' => 'DIM002',
                'unit' => 'pack',
                'base_unit' => 'pcs',
                'pcs_per_pack' => 12,
                'karton_contains_unit' => 'pack',
                'karton_contains_qty' => 12,
                'price' => 32000,
                'price_pcs' => 2800,
                'price_pack' => 32000,
                'price_karton' => 380000,
                'purchase_price' => 26000,
                'purchase_price_pcs' => 2300,
                'purchase_price_pack' => 26000,
                'purchase_price_karton' => 310000,
                'sell_pcs' => true,
                'sell_pack' => true,
                'sell_karton' => true,
                'low_stock_threshold' => 18,
                'is_active' => true,
                'batches' => [
                    ['quantity' => 48, 'days_until_expiry' => 195],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $batches = $productData['batches'];
            unset($productData['batches']);

            $product = Product::create($productData);

            // Create batches for each product
            foreach ($batches as $batch) {
                ProductBatch::create([
                    'product_id' => $product->id,
                    'batch_code' => 'BATCH-' . strtoupper(substr($product->barcode, 0, 3)) . '-' . now()->format('Ymd') . '-' . rand(100, 999),
                    'quantity' => $batch['quantity'],
                    'purchase_price' => $product->purchase_price_pack ?? $product->purchase_price,
                    'expiration_date' => Carbon::now()->addDays($batch['days_until_expiry']),
                    'date_received' => Carbon::now()->subDays(rand(1, 30)),
                    'notes' => 'Batch awal - Supplier ' . $product->category,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✓ Created ' . Category::count() . ' categories');
        $this->command->info('✓ Created ' . Product::count() . ' products');
        $this->command->info('✓ Created ' . ProductBatch::count() . ' product batches');
    }
}
