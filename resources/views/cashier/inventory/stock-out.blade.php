@extends('layouts.cashier')

@section('title','Stok Keluar')

@section('content')
                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Tabs -->
                    <div class="mb-4 border-b">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <a href="{{ route('cashier.inventory.index') }}" class="py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700">Stok Masuk</a>
                            <a href="{{ route('cashier.inventory.stock-out') }}" class="py-4 px-1 border-b-2 border-blue-600 text-sm font-medium text-blue-600">Stok Keluar</a>
                        </nav>
                    </div>
                    
                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('cashier.inventory.stock-out') }}" class="flex items-center gap-4 mb-6">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk, kategori..." class="w-full border rounded-lg p-3 shadow-sm">
                        </div>
                         <div class="flex items-center gap-2">
                         <label for="sortSelect" class="text-sm text-gray-600 mr-2">Urutkan:</label>
                        <select name="sort" class="border rounded-lg px-3 py-2 text-sm" onchange="this.form.submit()">
                            <option value="tanggal_terbaru" {{ ($sort ?? '') == 'tanggal_terbaru' ? 'selected' : '' }}>Tanggal Terbaru</option>
                            <option value="tanggal_terlama" {{ ($sort ?? '') == 'tanggal_terlama' ? 'selected' : '' }}>Tanggal Terlama</option>
                            <option value="jumlah_banyak" {{ ($sort ?? '') == 'jumlah_banyak' ? 'selected' : '' }}>Jumlah Terbanyak</option>
                            <option value="jumlah_sedikit" {{ ($sort ?? '') == 'jumlah_sedikit' ? 'selected' : '' }}>Jumlah Tersedikit</option>
                        </select>
                        </div>
                        <button type="submit" class="inline-flex items-center px-8 py-2 bg-blue-600 text-white rounded-lg shadow">
                            <i class="fas fa-search mr-2"></i> Cari
                        </button>
                    </form>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sebelum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Keluar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Sekarang</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $index => $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $logs->firstItem() + $index }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->created_at->format('H:i:s') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($log->product && $log->product->image)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($log->product->image) }}" alt="{{ $log->product->name }}" class="h-10 w-10 rounded object-cover mr-3">
                                            @else
                                                <div class="h-10 w-10 rounded bg-gray-200 mr-3 flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $log->product ? $log->product->name : '—' }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->product ? $log->product->category : '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->previous_stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ abs($log->change) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->new_stock }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Stok Keluar</h3>
                                            <p class="text-sm text-gray-500">Stok keluar dari transaksi POS akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                </div>
            </main>
@endsection
