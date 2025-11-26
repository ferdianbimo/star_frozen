<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Stok - Star Frozen POS</title>
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
                    <a href="{{ route('manager.inventory.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">
                        ← Kembali ke Inventory
                    </a>
                    <h2 class="text-3xl font-bold text-gray-800">Riwayat Pergerakan Stok</h2>
                    <p class="text-gray-600">History semua transaksi stok masuk dan keluar</p>
                </div>

                <!-- Filter -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <form method="GET" action="{{ route('manager.inventory.history') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Produk</label>
                            <select name="product_id" class="w-full border rounded px-3 py-2">
                                <option value="">Semua Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2">Tipe</label>
                            <select name="type" class="w-full border rounded px-3 py-2">
                                <option value="">Semua Tipe</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                class="w-full border rounded px-3 py-2">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-2">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                class="w-full border rounded px-3 py-2">
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 w-full">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- History Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sebelum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sesudah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stockLogs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ $log->product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->product->sku ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->type == 'in')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold {{ $log->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $log->type == 'in' ? '+' : '-' }}{{ $log->quantity }} {{ $log->product->unit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $log->old_stock ?? '-' }} {{ $log->product->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                    {{ $log->new_stock ?? '-' }} {{ $log->product->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $log->user->name }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $log->notes ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada riwayat pergerakan stok
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $stockLogs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
