<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Masuk - Star Frozen POS</title>
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
                    <h2 class="text-3xl font-bold text-gray-800">Stok Masuk</h2>
                    <p class="text-gray-600">Tambah stok produk</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Form Stok Masuk -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-bold mb-4">Form Stok Masuk</h3>
                        <form method="POST" action="{{ route('manager.inventory.store-stock-in') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                                    Produk <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="product_id" id="product_id" required 
                                        class="custom-select w-full appearance-none border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer hover:border-slate-300 @error('product_id') border-red-500 @enderror">
                                        <option value="">Pilih Produk</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-stock="{{ $product->effective_stock }}"
                                                    data-unit="{{ $product->unit }}"
                                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} (Stok: {{ $product->effective_stock }} {{ $product->unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                                @error('product_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4" id="current-stock-info" style="display: none;">
                                <div class="bg-blue-50 border border-blue-200 rounded p-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>Stok Saat Ini:</strong> <span id="current-stock">0</span> <span id="stock-unit"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Jumlah <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="quantity" min="1" required
                                    value="{{ old('quantity') }}"
                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror">
                                @error('quantity')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Catatan
                                </label>
                                <textarea name="notes" rows="3"
                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                            </div>

                            <div class="flex space-x-3">
                                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 font-bold">
                                    Simpan Stok Masuk
                                </button>
                                <a href="{{ route('manager.inventory.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Info Panel -->
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h4 class="font-bold text-blue-900 mb-2">📦 Stok Masuk</h4>
                            <p class="text-sm text-blue-800">
                                Gunakan form ini untuk mencatat penambahan stok produk dari supplier atau sumber lain.
                            </p>
                        </div>

                        <div class="bg-white border rounded-lg p-6">
                            <h4 class="font-bold mb-3">Tips:</h4>
                            <ul class="text-sm space-y-2 text-gray-700">
                                <li>• Pastikan produk yang dipilih sudah benar</li>
                                <li>• Masukkan jumlah stok yang diterima dengan akurat</li>
                                <li>• Tambahkan catatan untuk referensi (nomor PO, supplier, dll)</li>
                                <li>• Stok akan otomatis bertambah setelah disimpan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('product_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            const unit = selectedOption.getAttribute('data-unit');
            
            if (this.value) {
                document.getElementById('current-stock-info').style.display = 'block';
                document.getElementById('current-stock').textContent = stock;
                document.getElementById('stock-unit').textContent = unit;
            } else {
                document.getElementById('current-stock-info').style.display = 'none';
            }
        });
    </script>
</body>
</html>
