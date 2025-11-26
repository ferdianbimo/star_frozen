<?php
// Simple test to check if checkout route works
echo "Testing checkout functionality...\n";

// Check if route exists
$routes = file_get_contents(__DIR__ . '/routes/web.php');
if (strpos($routes, 'pos/checkout') !== false) {
    echo "✓ Route 'pos/checkout' found in web.php\n";
} else {
    echo "✗ Route 'pos/checkout' NOT found in web.php\n";
}

// Check if controller method exists
if (method_exists('App\Http\Controllers\PosController', 'checkout')) {
    echo "✓ Method 'checkout' exists in PosController\n";
} else {
    echo "✗ Method 'checkout' NOT found in PosController\n";
}

echo "\nDone.\n";
