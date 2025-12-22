@extends('layouts.manager')

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
            <a href="{{ route('manager.inventory.index') }}" class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                Stok Masuk
            </a>
            <a href="{{ route('manager.inventory.stock-out') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 hover:border-b-2 hover:border-slate-300">
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
                    <option>Frozen Food</option>
                    <option>Sembako</option>
                    <option>Minuman</option>
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
                    @forelse($products ?? [] as $index => $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">Rp {{ number_format($product->purchase_price ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700">Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ $product->stock ?? 0 }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $product->unit ?? 'pcs' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ optional($product->created_at)->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ optional($product->expiry_date)->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick='openEditModal(@json($product))' class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button onclick="deleteProduct({{ $product->id }})" class="text-red-600 hover:text-red-700">
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

<!-- Modal Tambah Produk -->
<div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Tambah Produk Baru</h2>

            <form id="addProductForm">
                @csrf
                <!-- Nama Produk -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Produk<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                        placeholder="Masukkan nama produk">
                </div>

                <!-- Kategori -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Kategori<span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
                        <option value="">Pilih kategori</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Harga Beli & Harga Jual -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Harga Beli<span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                            <input type="number" name="purchase_price" required
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                                placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Harga Jual<span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                            <input type="number" name="selling_price" required
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Stok Awal & Satuan -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Stok Awal<span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Satuan<span class="text-red-500">*</span>
                        </label>
                        <select name="unit" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
                            <option value="">Pilih satuan</option>
                            <option value="pcs">Pcs</option>
                            <option value="pack">Pack</option>
                            <option value="box">Box</option>
                            <option value="kg">Kg</option>
                            <option value="gram">Gram</option>
                            <option value="liter">Liter</option>
                            <option value="ml">Ml</option>
                            <option value="sack">Sack</option>
                        </select>
                    </div>
                </div>

                <!-- Tanggal Masuk & Kadaluwarsa -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Masuk<span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="entry_date" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Kadaluwarsa
                        </label>
                        <input type="date" name="expiry_date"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Produk
                    </button>
                    <button type="button" onclick="closeAddModal()"
                        class="flex-1 px-4 py-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-sm font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<div id="editProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Edit Produk</h2>

            <form id="editProductForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="product_id" id="edit_product_id">

                <!-- Nama Produk -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Produk<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                        placeholder="Masukkan nama produk">
                </div>

                <!-- Kategori -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Kategori<span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="edit_category_id" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
                        <option value="">Pilih kategori</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Harga Beli & Harga Jual -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Harga Beli<span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                            <input type="number" name="purchase_price" id="edit_purchase_price" required
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                                placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Harga Jual<span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">Rp</span>
                            <input type="number" name="selling_price" id="edit_selling_price" required
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Stok & Satuan -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Stok Awal<span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" id="edit_stock" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20"
                            placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Satuan<span class="text-red-500">*</span>
                        </label>
                        <select name="unit" id="edit_unit" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
                            <option value="">Pilih satuan</option>
                            <option value="pcs">Pcs</option>
                            <option value="pack">Pack</option>
                            <option value="box">Box</option>
                            <option value="kg">Kg</option>
                            <option value="gram">Gram</option>
                            <option value="liter">Liter</option>
                            <option value="ml">Ml</option>
                            <option value="sack">Sack</option>
                        </select>
                    </div>
                </div>

                <!-- Tanggal Masuk & Kadaluwarsa -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Masuk<span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="entry_date" id="edit_entry_date" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Kadaluwarsa
                        </label>
                        <input type="date" name="expiry_date" id="edit_expiry_date"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Produk
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 px-4 py-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-sm font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal functions
function openAddModal() {
    document.getElementById('addProductModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addProductModal').classList.add('hidden');
    document.getElementById('addProductForm').reset();
}

function openEditModal(product) {
    // Fill form with product data
    document.getElementById('edit_product_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_category_id').value = product.category_id || '';
    document.getElementById('edit_purchase_price').value = product.purchase_price || '';
    document.getElementById('edit_selling_price').value = product.selling_price || '';
    document.getElementById('edit_stock').value = product.stock || '';
    document.getElementById('edit_unit').value = product.unit || '';

    // Format dates if exists
    if (product.created_at) {
        const entryDate = new Date(product.created_at);
        document.getElementById('edit_entry_date').value = entryDate.toISOString().split('T')[0];
    }
    if (product.expiry_date) {
        const expiryDate = new Date(product.expiry_date);
        document.getElementById('edit_expiry_date').value = expiryDate.toISOString().split('T')[0];
    }

    document.getElementById('editProductModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editProductModal').classList.add('hidden');
    document.getElementById('editProductForm').reset();
}

// Form submissions
document.getElementById('addProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/manager/inventory', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produk berhasil ditambahkan');
            closeAddModal();
            location.reload();
        } else {
            alert('Gagal menambahkan produk: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menambahkan produk');
    });
});

document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const productId = document.getElementById('edit_product_id').value;

    fetch(`/manager/inventory/${productId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produk berhasil diupdate');
            closeEditModal();
            location.reload();
        } else {
            alert('Gagal mengupdate produk: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate produk');
    });
});

function deleteProduct(productId) {
    if (!confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        return;
    }

    fetch(`/manager/inventory/${productId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Produk berhasil dihapus');
            location.reload();
        } else {
            alert('Gagal menghapus produk: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus produk');
    });
}

// Close modal when clicking outside
document.getElementById('addProductModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddModal();
    }
});

document.getElementById('editProductModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection
