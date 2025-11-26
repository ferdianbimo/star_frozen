<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 py-4 flex flex-col">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-blue-200">Manager Dashboard</p>
            </div>
            
            <nav class="flex-1">
                <a href="{{ route('manager.dashboard') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('manager.inventory.index') }}" class="block py-2 px-4 bg-blue-900 text-white">
                    <i class="fas fa-boxes mr-2"></i> Inventory
                </a>
                
                <a href="{{ route('manager.finance.index') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-dollar-sign mr-2"></i> Keuangan
                </a>
                
                <a href="{{ route('manager.access.index') }}" class="block py-2 px-4 hover:bg-blue-700 text-white">
                    <i class="fas fa-user-lock mr-2"></i> Hak Akses
                </a>
            </nav>
            
            <div class="px-4 py-2 mt-auto border-t border-blue-700">
                <div class="flex items-center mb-2">
                    <span class="rounded-full bg-blue-600 w-8 h-8 flex items-center justify-center mr-2">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-sm text-blue-300 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Inventory Management</h2>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Tabs -->
                <div class="mb-4">
                    <div class="flex border-b">
                        <a href="{{ route('manager.inventory.index') }}" class="px-6 py-3 text-blue-600 border-b-2 border-blue-600 font-semibold">
                            Stok Masuk
                        </a>
                        <a href="{{ route('manager.inventory.stock-out') }}" class="px-6 py-3 text-gray-600 hover:text-blue-600">
                            Stok Keluar
                        </a>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <form method="GET" action="{{ route('manager.inventory.index') }}" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." 
                                class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 mr-2">Urutkan:</label>
                            <select name="sort" onchange="this.form.submit()" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                                <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>Stok (rendah → tinggi)</option>
                                <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Stok (tinggi → rendah)</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga (rendah → tinggi)</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga (tinggi → rendah)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Kadaluarsa (terdekat)</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Kadaluarsa (terjauh)</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                Cari
                            </button>
                        </div>
                        @if(request('search') || request('sort'))
                        <div>
                            <a href="{{ route('manager.inventory.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                Reset
                            </a>
                        </div>
                        @endif
                    </form>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Beli</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Masuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kadaluwarsa</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $index => $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $product->category ?? 'Frozen Food' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm">Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-lg {{ $product->stock <= $product->low_stock_threshold ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    pack
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $product->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $product->created_at->addMonths(6)->format('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-lg">Belum ada produk</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
