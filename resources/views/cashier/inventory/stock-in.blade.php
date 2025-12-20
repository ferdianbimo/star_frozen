@extends('layouts.cashier')

@section('title','Stok Masuk - Batch')

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Input Stok Masuk (Batch)</h2>
                    <p class="text-gray-600">Tambahkan stok baru berdasarkan batch dan tanggal kadaluarsa</p>
                </div>
                <a href="{{ route('cashier.inventory.index') }}" class="inline-flex items-center px-4 py-2 text-gray-600 border rounded-lg hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Input Batch -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Form Input Batch Baru</h3>
                
                <form method="POST" action="{{ route('cashier.inventory.batch.store') }}">
                    @csrf
                    
                    <!-- Pilih Produk -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Produk <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="product_id" required
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('product_id') border-red-500 @enderror">
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-stock="{{ $product->stock }}"
                                        data-unit="{{ $product->unit ?? 'pcs' }}"
                                        data-purchase-price="{{ $product->purchase_price }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Stok: {{ $product->stock }} {{ $product->unit ?? 'pcs' }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Stok Saat Ini -->
                    <div id="currentStockInfo" class="mb-4 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center text-blue-800">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span>Stok saat ini: <strong id="currentStock">0</strong> <span id="stockUnit">pcs</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" min="1" required
                            value="{{ old('quantity') }}"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-red-500 @enderror"
                            placeholder="Masukkan jumlah">
                        @error('quantity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Beli (per unit)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                            <input type="number" name="purchase_price" id="purchasePrice" min="0" step="0.01"
                                value="{{ old('purchase_price') }}"
                                class="w-full border rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('purchase_price') border-red-500 @enderror"
                                placeholder="0">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk menggunakan harga beli default produk</p>
                        @error('purchase_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Diterima -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Diterima <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_received" required
                            value="{{ old('date_received', date('Y-m-d')) }}"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('date_received') border-red-500 @enderror">
                        @error('date_received')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Kadaluarsa -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Kadaluarsa
                        </label>
                        <input type="date" name="expiration_date"
                            value="{{ old('expiration_date') }}"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('expiration_date') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika produk tidak memiliki tanggal kadaluarsa</p>
                        @error('expiration_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" rows="3"
                            class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Simpan Batch
                        </button>
                        <button type="reset" class="px-6 py-3 border text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info & Recent Batches -->
            <div class="space-y-6">
                <!-- Info Panel -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="font-bold text-blue-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                        Sistem Batch
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Setiap batch memiliki kode unik untuk tracking
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Produk dengan tanggal kadaluarsa berbeda disimpan terpisah
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Sistem FIFO: batch dengan kadaluarsa terdekat dijual duluan
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Di POS, kasir dapat memilih batch tertentu saat penjualan
                        </li>
                    </ul>
                </div>

                <!-- Recent Batches -->
                <div class="bg-white border rounded-lg p-6">
                    <h4 class="font-bold text-gray-900 mb-4">Batch Terbaru</h4>
                    
                    @if($recentBatches->count() > 0)
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            @foreach($recentBatches as $batch)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-sm">{{ $batch->product->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">
                                            Batch: {{ $batch->batch_code }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($batch->expiration_date)
                                                Exp: {{ $batch->expiration_date->format('d M Y') }}
                                                @if($batch->isExpiringSoon())
                                                    <span class="text-orange-600 font-medium">({{ $batch->daysUntilExpiration() }} hari lagi)</span>
                                                @elseif($batch->isExpired())
                                                    <span class="text-red-600 font-medium">(Kadaluarsa)</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Tidak ada exp date</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-green-600">+{{ $batch->quantity }}</div>
                                        <div class="text-xs text-gray-500">{{ $batch->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-4">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p>Belum ada batch yang ditambahkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const currentStockInfo = document.getElementById('currentStockInfo');
    const currentStock = document.getElementById('currentStock');
    const stockUnit = document.getElementById('stockUnit');
    const purchasePrice = document.getElementById('purchasePrice');

    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const stock = selectedOption.getAttribute('data-stock');
            const unit = selectedOption.getAttribute('data-unit');
            const defaultPrice = selectedOption.getAttribute('data-purchase-price');
            
            currentStock.textContent = stock;
            stockUnit.textContent = unit;
            currentStockInfo.classList.remove('hidden');
            
            // Set default purchase price if empty
            if (!purchasePrice.value && defaultPrice) {
                purchasePrice.value = defaultPrice;
            }
        } else {
            currentStockInfo.classList.add('hidden');
        }
    });

    // Trigger change event if there's an old value
    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
