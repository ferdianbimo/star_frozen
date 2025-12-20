@extends('layouts.cashier')

@section('title','Stok Masuk - Batch')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-boxes text-xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Input Stok Masuk (Batch)</h2>
                        <p class="text-slate-500 text-sm">Tambahkan stok baru berdasarkan batch dan satuan</p>
                    </div>
                </div>
                <a href="{{ route('cashier.inventory.index') }}" class="inline-flex items-center px-4 py-2.5 text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <div class="flex items-center gap-2 font-medium mb-2">
                    <i class="fas fa-exclamation-circle"></i>
                    Terjadi kesalahan:
                </div>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Input Batch -->
            <div class="bg-gradient-to-br from-slate-50 to-white rounded-xl p-6 border border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-emerald-500"></i>
                    Form Input Batch Baru
                </h3>
                
                <form method="POST" action="{{ route('cashier.inventory.batch.store') }}">
                    @csrf
                    
                    <!-- Pilih Produk -->
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                            Produk <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="product_id" id="product_id" required
                                class="custom-select w-full appearance-none border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all cursor-pointer hover:border-slate-300 @error('product_id') border-red-500 @enderror">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    @php
                                        $unitPrices = [
                                            'pcs' => $product->purchase_price_pcs ?? $product->purchase_price ?? 0,
                                            'pack' => $product->purchase_price_pack ?? 0,
                                            'renteng' => $product->purchase_price_renteng ?? 0,
                                            'box' => $product->purchase_price_box ?? 0,
                                            'karton' => $product->purchase_price_karton ?? 0,
                                        ];
                                    @endphp
                                    <option value="{{ $product->id }}" 
                                            data-stock="{{ $product->effective_stock }}"
                                            data-unit="{{ $product->unit ?? 'pcs' }}"
                                            data-purchase-price="{{ $product->purchase_price }}"
                                            data-unit-prices='@json($unitPrices)'
                                            data-units='@json($product->all_unit_options)'
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} (Stok: {{ $product->effective_stock }} pcs)
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

                    <!-- Info Stok Saat Ini -->
                    <div id="currentStockInfo" class="mb-4 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                            <div class="flex items-center text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Stok saat ini: <strong id="currentStock">0</strong> <span id="stockUnit">pcs</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Satuan Masuk -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Satuan Barang Masuk <span class="text-red-500">*</span>
                        </label>
                        <div id="unitSelection" class="grid grid-cols-5 gap-2">
                            <button type="button" class="unit-btn active-unit p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50 transition-all text-center" data-unit="pcs" data-pcs="1" onclick="selectIncomingUnit('pcs', 1)">
                                <div class="font-bold text-xs text-slate-800">Pcs</div>
                                <div class="text-xs text-slate-500">1 Pcs</div>
                            </button>
                        </div>
                        <input type="hidden" name="incoming_unit" id="incoming_unit" value="pcs">
                        <p class="text-xs text-slate-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Pilih satuan sesuai barang yang datang</p>
                    </div>

                    <!-- Jumlah -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="number" name="quantity" id="quantityInput" min="1" required
                                value="{{ old('quantity', 1) }}"
                                class="flex-1 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('quantity') border-red-500 @enderror"
                                placeholder="Masukkan jumlah" oninput="calculateTotalPcs()">
                            <div id="unitLabel" class="px-4 py-3 bg-slate-100 rounded-xl text-slate-700 font-medium">Pcs</div>
                        </div>
                        <div id="pcsEquivalent" class="text-sm text-emerald-600 mt-2 hidden">
                            <i class="fas fa-calculator mr-1"></i>
                            = <span id="totalPcsValue">0</span> Pcs
                        </div>
                        @error('quantity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga Beli -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Harga Beli <span id="purchasePriceUnitLabel" class="text-emerald-600">(per Pcs)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-slate-500">Rp</span>
                            <input type="number" name="purchase_price" id="purchasePrice" min="0" step="0.01"
                                value="{{ old('purchase_price') }}"
                                class="w-full border border-slate-200 rounded-xl pl-12 pr-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('purchase_price') border-red-500 @enderror"
                                placeholder="0">
                        </div>
                        <p id="defaultPriceHint" class="text-xs text-slate-500 mt-1">Kosongkan untuk menggunakan harga beli default</p>
                        @error('purchase_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Diterima -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Diterima <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_received" required
                            value="{{ old('date_received', date('Y-m-d')) }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('date_received') border-red-500 @enderror">
                        @error('date_received')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Kadaluarsa -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Kadaluarsa
                        </label>
                        <input type="date" name="expiration_date"
                            value="{{ old('expiration_date') }}"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('expiration_date') border-red-500 @enderror">
                        <p class="text-xs text-slate-500 mt-1">Kosongkan jika produk tidak memiliki tanggal kadaluarsa</p>
                        @error('expiration_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Catatan
                        </label>
                        <textarea name="notes" rows="2"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Simpan Batch
                        </button>
                        <button type="reset" class="px-6 py-3 border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition-colors font-medium">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info & Recent Batches -->
            <div class="space-y-6">
                <!-- Info Panel -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                    <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-blue-500"></i>
                        Sistem Satuan & Batch
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-3">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                            <span><strong>Karton</strong> → berisi beberapa satuan lain (Box/Renteng/Pack/Pcs)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                            <span><strong>Box</strong> → berisi beberapa satuan lain (Renteng/Pack/Pcs)</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                            <span><strong>Pack/Renteng</strong> → berisi beberapa Pcs</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                            <span><strong>Pcs</strong> → satuan terkecil (dasar)</span>
                        </li>
                    </ul>
                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <p class="text-xs text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Stok akan dikonversi ke Pcs untuk perhitungan inventory
                        </p>
                    </div>
                </div>

                <!-- Recent Batches -->
                <div class="bg-white border border-slate-200 rounded-xl p-6">
                    <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-slate-500"></i>
                        Batch Terbaru
                    </h4>
                    
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
    const unitSelection = document.getElementById('unitSelection');
    const quantityInput = document.getElementById('quantityInput');
    const purchasePriceUnitLabel = document.getElementById('purchasePriceUnitLabel');
    const defaultPriceHint = document.getElementById('defaultPriceHint');
    
    let currentUnits = [];
    let selectedPcsEquivalent = 1;
    let currentUnitPrices = {};

    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const stock = selectedOption.getAttribute('data-stock');
            const unit = selectedOption.getAttribute('data-unit') || 'pcs';
            const defaultPrice = selectedOption.getAttribute('data-purchase-price');
            const unitsData = selectedOption.getAttribute('data-units');
            const unitPricesData = selectedOption.getAttribute('data-unit-prices');
            
            currentStock.textContent = stock;
            stockUnit.textContent = 'pcs';
            currentStockInfo.classList.remove('hidden');
            
            // Parse unit prices
            try {
                currentUnitPrices = JSON.parse(unitPricesData) || {};
            } catch (e) {
                currentUnitPrices = {};
            }
            
            // Parse and render unit options
            try {
                currentUnits = JSON.parse(unitsData) || [];
                renderUnitButtons(currentUnits);
            } catch (e) {
                currentUnits = [{type: 'pcs', label: 'Pcs', contents: '1 Pcs', pcs_equivalent: 1}];
                renderUnitButtons(currentUnits);
            }
        } else {
            currentStockInfo.classList.add('hidden');
            currentUnits = [];
            currentUnitPrices = {};
            renderUnitButtons([{type: 'pcs', label: 'Pcs', contents: '1 Pcs', pcs_equivalent: 1}]);
        }
    });

    function renderUnitButtons(units) {
        if (units.length === 0) {
            units = [{type: 'pcs', label: 'Pcs', contents: '1 Pcs', pcs_equivalent: 1}];
        }
        
        const colors = {
            'pcs': 'emerald',
            'pack': 'teal',
            'renteng': 'purple',
            'box': 'amber',
            'karton': 'rose'
        };
        
        let html = '';
        units.forEach((unit, index) => {
            const color = colors[unit.type] || 'slate';
            const isFirst = index === 0;
            html += `
                <button type="button" 
                        class="unit-btn p-2 rounded-xl border-2 transition-all text-center ${isFirst ? 'border-' + color + '-500 bg-' + color + '-50' : 'border-slate-200 hover:border-' + color + '-300'}" 
                        data-unit="${unit.type}" 
                        data-pcs="${unit.pcs_equivalent}"
                        onclick="selectIncomingUnit('${unit.type}', ${unit.pcs_equivalent})">
                    <div class="font-bold text-xs text-slate-800">${unit.label}</div>
                    <div class="text-xs text-slate-500">${unit.contents}</div>
                </button>
            `;
        });
        
        unitSelection.innerHTML = html;
        
        // Auto-select first unit
        if (units.length > 0) {
            selectIncomingUnit(units[0].type, units[0].pcs_equivalent);
        }
    }

    // Trigger change event if there's an old value
    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
});

// Global functions
let selectedPcsEquivalent = 1;
let currentSelectedUnit = 'pcs';

function selectIncomingUnit(unitType, pcsEquivalent) {
    selectedPcsEquivalent = pcsEquivalent;
    currentSelectedUnit = unitType;
    document.getElementById('incoming_unit').value = unitType;
    document.getElementById('unitLabel').textContent = unitType.charAt(0).toUpperCase() + unitType.slice(1);
    
    // Update purchase price label
    const unitLabel = unitType.charAt(0).toUpperCase() + unitType.slice(1);
    document.getElementById('purchasePriceUnitLabel').textContent = `(per ${unitLabel})`;
    
    // Update purchase price with default price for this unit
    const purchasePrice = document.getElementById('purchasePrice');
    const productSelect = document.getElementById('product_id');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        try {
            const unitPrices = JSON.parse(selectedOption.getAttribute('data-unit-prices') || '{}');
            const defaultUnitPrice = unitPrices[unitType] || 0;
            
            if (defaultUnitPrice > 0) {
                purchasePrice.value = defaultUnitPrice;
                document.getElementById('defaultPriceHint').innerHTML = `<i class="fas fa-check-circle text-emerald-500 mr-1"></i>Harga default: Rp ${Number(defaultUnitPrice).toLocaleString('id-ID')}`;
            } else {
                purchasePrice.value = '';
                document.getElementById('defaultPriceHint').textContent = 'Belum ada harga default untuk satuan ini';
            }
        } catch (e) {
            purchasePrice.value = '';
        }
    }
    
    // Update button styles
    document.querySelectorAll('.unit-btn').forEach(btn => {
        if (btn.dataset.unit === unitType) {
            btn.classList.add('border-emerald-500', 'bg-emerald-50');
            btn.classList.remove('border-slate-200');
        } else {
            btn.classList.remove('border-emerald-500', 'bg-emerald-50');
            btn.classList.add('border-slate-200');
        }
    });
    
    calculateTotalPcs();
}

function calculateTotalPcs() {
    const quantity = parseInt(document.getElementById('quantityInput').value) || 0;
    const totalPcs = quantity * selectedPcsEquivalent;
    const pcsEquivalentDiv = document.getElementById('pcsEquivalent');
    const totalPcsValue = document.getElementById('totalPcsValue');
    
    if (selectedPcsEquivalent > 1 && quantity > 0) {
        pcsEquivalentDiv.classList.remove('hidden');
        totalPcsValue.textContent = totalPcs.toLocaleString('id-ID');
    } else {
        pcsEquivalentDiv.classList.add('hidden');
    }
}
</script>
@endpush
