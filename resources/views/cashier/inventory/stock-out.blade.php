@extends('layouts.cashier')

@section('title','Stok Keluar')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Inventory Management</h1>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <div class="flex gap-2 border-b border-slate-200">
            <a href="{{ route('cashier.inventory.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:border-b-2 hover:border-slate-300">
                Stok Masuk
            </a>
            <a href="{{ route('cashier.inventory.stock-out') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                Stok Keluar
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-lg p-4 border border-slate-200 mb-4">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input
                        type="text"
                        placeholder="Cari transaksi..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                        id="searchInput">
                </div>
            </div>

            <div class="flex gap-3 flex-shrink-0">
                <select id="categoryFilter" name="category" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white" onchange="filterByCategory()">
                    <option value="Semua">Semua</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">ID Transaksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Produk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Satuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Harga Jual</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    {{-- @var \App\Models\StockLog $log --}}
                    @forelse($logs ?? [] as $index => $log)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ isset($log->created_at) && $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ isset($log->transaction_id) ? $log->transaction_id : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">{{ isset($log->product) && $log->product ? $log->product->name : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ isset($log->change) ? abs($log->change) : 0 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ isset($log->unit_type) ? $log->unit_type : 'pcs' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">Rp {{ isset($log->price) ? number_format($log->price, 0, ',', '.') : '0' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-medium">Rp {{ isset($log->price, $log->change) ? number_format($log->price * abs($log->change), 0, ',', '.') : '0' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ isset($log->note) ? $log->note : 'Penjualan' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500">
                            Tidak ada data stok keluar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($logs) && $logs->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function filterByCategory() {
    const category = document.getElementById('categoryFilter').value;
    const currentUrl = new URL(window.location.href);

    if (category === 'Semua') {
        currentUrl.searchParams.delete('category');
    } else {
        currentUrl.searchParams.set('category', category);
    }

    window.location.href = currentUrl.toString();
}
</script>
@endsection
