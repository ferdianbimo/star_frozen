<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Inventory - Star Frozen POS</title>
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
                        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $product->name }}</h1>
                    </div>
                </div>
            </header>

            <main class="py-6 px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Product Info -->
                    <div class="bg-white rounded-lg shadow overflow-hidden md:col-span-2">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Product Information</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-1/4 flex items-center justify-center mb-4 md:mb-0">
                                    @if($product->image)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-32 w-32 object-cover rounded-lg">
                                    @else
                                        <div class="h-32 w-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box fa-3x text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="md:w-3/4 md:pl-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Category</p>
                                            <p class="mt-1">{{ $product->category ?? 'Uncategorized' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Price</p>
                                            <p class="mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Current Stock</p>
                                            <p class="mt-1 {{ $product->hasLowStock() ? 'text-red-600 font-semibold' : '' }}">
                                                {{ $product->stock }} units
                                                @if($product->hasLowStock())
                                                    <span class="text-xs text-red-600">(Low stock)</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">Barcode</p>
                                            <p class="mt-1">{{ $product->barcode ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-sm font-medium text-gray-500">Description</p>
                                            <p class="mt-1">{{ $product->description ?? 'No description available' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Stock Form -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Update Stock</h2>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('cashier.inventory.update-stock') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                
                                <div class="mb-4">
                                    <label for="stock_change" class="block text-sm font-medium text-gray-700 mb-1">Stock Change</label>
                                    <div class="flex">
                                        <button type="button" onclick="decrementStock()" class="bg-red-500 text-white px-4 py-2 rounded-l-md">-</button>
                                        <input type="number" id="stock_change" name="stock_change" value="0" required 
                                            class="flex-1 border-gray-300 focus:ring focus:ring-green-200 focus:border-green-500 text-center" 
                                            min="{{ -$product->stock }}" step="1">
                                        <button type="button" onclick="incrementStock()" class="bg-green-500 text-white px-4 py-2 rounded-r-md">+</button>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Current stock: <span id="current_stock">{{ $product->stock }}</span></p>
                                    <p class="mt-1 text-sm text-gray-500">New stock will be: <span id="new_stock">{{ $product->stock }}</span></p>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Note (optional)</label>
                                    <textarea id="note" name="note" rows="3" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200 focus:border-green-500"
                                        placeholder="Add note about this stock change..."></textarea>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                                        Update Stock
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Stock History -->
                    <div class="bg-white rounded-lg shadow overflow-hidden md:col-span-3">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium text-gray-900">Stock History</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($product->stockLogs()->latest()->get() as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->previous_stock }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $log->change > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $log->change > 0 ? '+' : '' }}{{ $log->change }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->new_stock }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                            {{ $log->note ?? '-' }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No stock history found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Stock update functionality
        const stockChangeInput = document.getElementById('stock_change');
        const currentStockSpan = document.getElementById('current_stock');
        const newStockSpan = document.getElementById('new_stock');
        const currentStock = {{ $product->stock }};
        
        function updateNewStock() {
            const change = parseInt(stockChangeInput.value) || 0;
            const newStock = currentStock + change;
            newStockSpan.textContent = newStock;
            
            // Change color based on whether stock is increasing or decreasing
            if (change > 0) {
                newStockSpan.className = 'text-green-600 font-semibold';
            } else if (change < 0) {
                newStockSpan.className = 'text-red-600 font-semibold';
            } else {
                newStockSpan.className = '';
            }
        }
        
        function incrementStock() {
            stockChangeInput.value = (parseInt(stockChangeInput.value) || 0) + 1;
            updateNewStock();
        }
        
        function decrementStock() {
            const newValue = (parseInt(stockChangeInput.value) || 0) - 1;
            // Prevent stock from going negative
            if (newValue >= -currentStock) {
                stockChangeInput.value = newValue;
                updateNewStock();
            }
        }
        
        stockChangeInput.addEventListener('input', updateNewStock);
        
        // Initialize
        updateNewStock();
    </script>
</body>
</html>
