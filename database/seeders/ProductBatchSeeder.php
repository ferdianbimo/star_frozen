<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBatch;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Create 2-3 batches per product
            $batchCount = rand(2, 3);
            
            for ($i = 0; $i < $batchCount; $i++) {
                ProductBatch::create([
                    'product_id' => $product->id,
                    'batch_code' => 'BATCH-' . strtoupper(substr($product->barcode ?? 'PRD', 0, 3)) . '-' . now()->format('Ymd') . '-' . rand(100, 999),
                    'quantity' => rand(20, 80),
                    'purchase_price' => $product->purchase_price_pack ?? $product->purchase_price ?? 0,
                    'expiration_date' => Carbon::now()->addDays(rand(150, 250)),
                    'date_received' => Carbon::now()->subDays(rand(1, 45)),
                    'notes' => 'Batch ' . ($i + 1) . ' - Supplier ' . ($product->category ?? 'Umum'),
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✓ Created ' . ProductBatch::count() . ' product batches for ' . $products->count() . ' products');
    }
}
