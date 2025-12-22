@extends('layouts.cashier')

@section('title','Inventory')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Inventory Management</h1>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <div class="flex gap-2 border-b border-slate-200">
            <a href="{{ route('cashier.inventory.index') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                Stok Masuk
            </a>
            <a href="{{ route('cashier.inventory.stock-out') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:border-b-2 hover:border-slate-300">
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
                        placeholder="Cari produk..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                        id="searchInput">
                </div>
            </div>

            <div class="flex gap-3 flex-shrink-0">
                <select class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
                    <option>Semua</option>
                    @foreach($categories ?? [] as $cat)
                        <option>{{ $cat }}</option>
                    @endforeach
                </select>

                <button onclick="openAddModal()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-plus"></i>
                    Tambah Produk
                </button>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Nama Produk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Harga Beli</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Harga Jual</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Stok</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Satuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Tanggal Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Kadaluwarsa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    {{-- @var \App\Models\Product $product --}}
                    @forelse($products ?? [] as $index => $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ isset($product->name) ? $product->name : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ isset($product->category) ? $product->category : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">Rp {{ isset($product->purchase_price) ? number_format($product->purchase_price, 0, ',', '.') : '0' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">Rp {{ isset($product->price) ? number_format($product->price, 0, ',', '.') : '0' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ isset($product->stock) ? $product->stock : 0 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ isset($product->unit) ? $product->unit : 'pcs' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ isset($product->created_at) && $product->created_at ? $product->created_at->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ isset($product->expiry_date) && $product->expiry_date ? $product->expiry_date->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick='openEditModal(@json($product))' class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button onclick="deleteProduct({{ isset($product->id) ? $product->id : 0 }})" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-sm text-slate-500">
                            Tidak ada data produk
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Modal functions
function openAddModal() {
    alert('Fitur tambah produk untuk cashier');
}

function openEditModal(product) {
    alert('Fitur edit produk untuk cashier');
}

function deleteProduct(productId) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        alert('Fitur delete produk untuk cashier');
    }
}
</script>
@endsection
