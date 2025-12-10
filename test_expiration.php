<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

// Update 3 produk pertama dengan expiration date dalam 7 hari ke depan
$products = Product::limit(3)->get();

echo "Setting expiration dates for testing:\n";
echo "==========================================\n";

foreach ($products as $product) {
    $daysUntilExpiry = rand(1, 7);
    $product->expiration_date = now()->addDays($daysUntilExpiry)->toDateString();
    $product->save();
    
    echo "- {$product->name}: {$product->expiration_date} ({$daysUntilExpiry} days from now)\n";
}

echo "\nDone! Check dashboard untuk lihat Expired Soon.\n";
