<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Items - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-green-800 text-white w-64 py-4 flex flex-col">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-green-200">Cashier Dashboard</p>
            </div>
            
            <nav class="flex-1">
                <a href="{{ route('cashier.dashboard') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('cashier.pos.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-cash-register mr-2"></i> Point of Sale
                </a>
                
                <a href="{{ route('cashier.inventory.index') }}" class="block py-2 px-4 bg-green-900 text-white">
                    <i class="fas fa-boxes mr-2"></i> Inventory
                </a>
                
                <a href="{{ route('cashier.transactions.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-receipt mr-2"></i> My Transactions
                </a>
            </nav>
            
            <div class="px-4 py-2 mt-auto border-t border-green-700">
                <div class="flex items-center mb-2">
                    <span class="rounded-full bg-green-600 w-8 h-8 flex items-center justify-center mr-2">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-sm text-green-300 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow">
                <div class="py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <div>
                        <a href="{{ route('cashier.inventory.index') }}" class="text-green-500 hover:text-green-700">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">Low Stock Items</h1>
                    </div>
                </div>
            </header>

            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <!-- Alert -->
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Attention Required!</p>
                            <p>The following products have stock levels at or below their minimum threshold.</p>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimum Stock</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <i class="fas fa-box text-gray-400"></i>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $product->barcode ?? 'No barcode' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category ?? 'Uncategorized' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $product->stock }} units
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->low_stock_threshold }} units</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('cashier.inventory.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No low stock products found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        {{ $products->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
