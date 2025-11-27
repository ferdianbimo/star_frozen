@extends('layouts.manager')

@section('title','Stok Keluar')

@section('content')
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
                        <a href="{{ route('manager.inventory.index') }}" class="px-6 py-3 text-gray-600 hover:text-blue-600">
                            Stok Masuk
                        </a>
                        <a href="{{ route('manager.inventory.stock-out') }}" class="px-6 py-3 text-blue-600 border-b-2 border-blue-600 font-semibold">
                            Stok Keluar
                        </a>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="bg-white rounded-lg shadow p-4 mb-4">
                    <form method="GET" action="{{ route('manager.inventory.stock-out') }}" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." 
                                class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 mr-2">Urutkan:</label>
                            <select name="sort" onchange="this.form.submit()" class="border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Tanggal (terbaru)</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Tanggal (terlama)</option>
                                <option value="qty_high" {{ request('sort') == 'qty_high' ? 'selected' : '' }}>Jumlah (tinggi → rendah)</option>
                                <option value="qty_low" {{ request('sort') == 'qty_low' ? 'selected' : '' }}>Jumlah (rendah → tinggi)</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                Cari
                            </button>
                        </div>
                        @if(request('search') || request('sort'))
                        <div>
                            <a href="{{ route('manager.inventory.stock-out') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                Reset
                            </a>
                        </div>
                        @endif
                    </form>
                </div>

                <!-- Stock Out History Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Keluar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sebelum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sesudah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stockLogs as $index => $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ ($stockLogs->currentPage() - 1) * $stockLogs->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $log->product->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-lg text-red-600">{{ abs($log->change) }} pack</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $log->previous_stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $log->new_stock }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="text-gray-600">{{ $log->note ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $log->user->name ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-lg">Belum ada riwayat stok keluar</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $stockLogs->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
