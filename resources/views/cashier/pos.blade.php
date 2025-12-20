@extends('layouts.cashier')

@section('title','Point of Sale')

@section('content')
                <!-- Modern Header -->
                <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-xl lg:rounded-2xl p-4 lg:p-5 mb-4 lg:mb-6 shadow-xl">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 lg:gap-4">
                        <div class="flex items-center gap-3 lg:gap-4">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                <i class="fas fa-cash-register text-lg lg:text-xl text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg lg:text-xl font-bold text-white">Point of Sale</h2>
                                <p class="text-slate-400 text-xs lg:text-sm">Transaksi penjualan cepat</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <div class="flex-1 sm:flex-none px-3 lg:px-4 py-2 bg-slate-700/50 rounded-xl border border-slate-600">
                                <span id="realtimeClock" class="text-slate-300 text-xs lg:text-sm font-mono"><i class="fas fa-clock mr-1 lg:mr-2"></i>{{ now()->format('d M Y, H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-exclamation-triangle text-red-500"></i>
                            <span class="font-medium">Terjadi kesalahan:</span>
                        </div>
                        <ul class="list-disc pl-8 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input id="search" type="text" placeholder="Cari produk..." class="w-full border border-slate-200 rounded-xl pl-11 pr-4 py-3 bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all" oninput="filterProducts()">
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6">
                    <!-- Left: products -->
                    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-100 shadow-sm p-4 lg:p-5 overflow-auto order-2 lg:order-1 max-h-[60vh] lg:max-h-[80vh] min-h-[300px]">
                        

                        <div id="products" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 lg:gap-4">
                            @foreach($products as $product)
                                <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-200 rounded-xl p-3 lg:p-4 product-card hover:border-blue-300 hover:shadow-lg transition-all duration-300" data-name="{{ strtolower($product->name) }}" data-product-id="{{ $product->id }}">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 lg:w-28 lg:h-28 bg-white rounded-xl border border-slate-100 flex items-center justify-center mb-2 lg:mb-3 overflow-hidden">
                                            @if($product->image)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="max-h-20 lg:max-h-28 object-contain">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                                                    <i class="fas fa-box-open text-3xl text-slate-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="w-full text-left">
                                            <h3 class="font-semibold text-slate-700 text-sm mb-1 line-clamp-2">{{ $product->name }}</h3>
                                            <div class="text-blue-600 font-bold">Rp {{ number_format($product->price,0,',','.') }}</div>
                                            @php 
                                                $availableBatches = $product->batches()->available()->notExpired();
                                                $batchStock = $availableBatches->sum('quantity');
                                                $batchCount = $availableBatches->count();
                                                $hasBatches = $batchCount > 0;
                                            @endphp
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($hasBatches)
                                                    <span class="text-xs px-2 py-0.5 {{ $batchStock <= 10 ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-600' }} rounded-full">Stock: {{ $batchStock }}</span>
                                                    <span class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-600 rounded-full">{{ $batchCount }} batch</span>
                                                @else
                                                    <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-600 rounded-full"><i class="fas fa-exclamation-triangle mr-1"></i>Belum ada batch</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="w-full mt-2 lg:mt-3">
                                            @if($hasBatches && $batchStock > 0)
                                            <button type="button" onclick="openBatchModal({{ $product->id }}, '{{ $product->name }}')" class="w-full inline-flex items-center justify-center gap-1 lg:gap-2 px-3 lg:px-4 py-2 lg:py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all text-sm">
                                                <i class="fas fa-plus text-xs lg:text-sm"></i>
                                                <span class="hidden sm:inline">Tambah</span>
                                                <span class="sm:hidden">+</span>
                                            </button>
                                            @else
                                            <button type="button" disabled class="w-full inline-flex items-center justify-center gap-1 lg:gap-2 px-3 lg:px-4 py-2 lg:py-2.5 bg-slate-300 text-slate-500 rounded-xl font-medium cursor-not-allowed text-xs lg:text-sm">
                                                <i class="fas fa-ban text-xs"></i>
                                                <span>{{ !$hasBatches ? 'No Batch' : 'Habis' }}</span>
                                            </button>
                                            @endif
                                            <form id="addForm-{{ $product->id }}" method="POST" action="{{ route('cashier.pos.add') }}" class="hidden">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="batch_id" id="batch-input-{{ $product->id }}" value="">
                                                <input type="hidden" name="quantity" id="qty-input-{{ $product->id }}" value="1">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right: current order -->
                    <div class="lg:col-span-4 order-1 lg:order-2">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 lg:p-5 mx-auto lg:sticky lg:top-4" style="max-height: 80vh;">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                    <i class="fas fa-shopping-cart text-white"></i>
                                </div>
                                <h2 class="text-lg font-bold text-slate-800">Pesanan</h2>
                            </div>

                            <div class="space-y-3 custom-scrollbar overflow-auto max-h-[200px] lg:max-h-[28vh]">
                                @php $subtotal = 0; @endphp
                                @if(empty($cart))
                                    <div class="flex flex-col items-center justify-center h-full text-slate-400">
                                        <i class="fas fa-shopping-basket text-4xl mb-2"></i>
                                        <span>Keranjang kosong</span>
                                    </div>
                                @else
                                    @foreach($cart as $cartKey => $item)
                                        @php 
                                            $line = $item['price'] * $item['quantity']; 
                                            $subtotal += $line;
                                            $cartProduct = \App\Models\Product::find($item['id']);
                                            $cartImage = $cartProduct ? $cartProduct->image : null;
                                            $cartBatch = isset($item['batch_id']) ? \App\Models\ProductBatch::find($item['batch_id']) : null;
                                            $unitType = $item['unit_type'] ?? 'pcs';
                                            $unitLabel = $item['unit_label'] ?? 'Pcs';
                                        @endphp
                                        <div class="flex items-center justify-between border border-slate-200 rounded-xl p-3 hover:bg-slate-50 transition-colors">
                                            <div class="flex items-start">
                                                <div class="w-12 h-12 bg-slate-100 rounded-lg mr-3 flex items-center justify-center overflow-hidden">
                                                    @if($cartImage)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($cartImage) }}" alt="" class="max-h-10 object-contain">
                                                    @else
                                                        <i class="fas fa-box text-slate-400"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium text-sm text-slate-700">{{ $item['name'] }}</div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-blue-600 font-semibold">Rp {{ number_format($item['price'],0,',','.') }}</span>
                                                        <span class="text-xs px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded font-medium">{{ $unitLabel }}</span>
                                                    </div>
                                                    @if($cartBatch)
                                                        <div class="text-xs text-emerald-600 flex items-center gap-1 mt-0.5">
                                                            <i class="fas fa-tag text-xs"></i>
                                                            {{ $cartBatch->batch_code }}
                                                            @if($cartBatch->expiration_date)
                                                                ({{ $cartBatch->expiration_date->format('d/m/Y') }})
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <form method="POST" action="{{ route('cashier.pos.update') }}" class="inline-flex">
                                                    @csrf
                                                    <input type="hidden" name="cart_key" value="{{ $cartKey }}">
                                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="batch_id" value="{{ $item['batch_id'] ?? '' }}">
                                                    <input type="hidden" name="unit_type" value="{{ $unitType }}">
                                                    <input type="hidden" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}">
                                                    <button class="w-7 h-7 flex items-center justify-center bg-slate-200 rounded-l-lg hover:bg-slate-300 transition-colors text-sm">-</button>
                                                </form>

                                                <div class="w-8 h-7 flex items-center justify-center bg-slate-100 text-sm font-medium">{{ $item['quantity'] }}</div>

                                                <form method="POST" action="{{ route('cashier.pos.add') }}" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="batch_id" value="{{ $item['batch_id'] ?? '' }}">
                                                    <input type="hidden" name="unit_type" value="{{ $unitType }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button class="w-7 h-7 flex items-center justify-center bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors text-sm">+</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="mt-4 border-t border-slate-200 pt-4">
                                <form id="checkoutForm" method="POST" action="{{ route('cashier.pos.checkout') }}">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Diskon (%)</label>
                                            <input id="discountInput" type="number" name="discount" value="0" min="0" max="100" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 focus:bg-white focus:border-blue-500 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Pajak (%)</label>
                                            <input id="taxInput" type="number" name="tax" value="0" min="0" max="100" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 focus:bg-white focus:border-blue-500 transition-all">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-slate-600 mb-2">Metode Pembayaran</label>
                                        <div class="flex items-center gap-3">
                                            <label class="flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 cursor-pointer hover:border-blue-300 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 transition-all">
                                                <input id="pm_cash" type="radio" name="payment_method" value="cash" checked class="text-blue-600">
                                                <i class="fas fa-money-bill-wave text-emerald-500"></i>
                                                <span class="text-sm font-medium">Cash</span>
                                            </label>
                                            <label class="flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 cursor-pointer hover:border-blue-300 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 transition-all">
                                                <input id="pm_qris" type="radio" name="payment_method" value="qris" class="text-blue-600">
                                                <i class="fas fa-qrcode text-purple-500"></i>
                                                <span class="text-sm font-medium">Qris</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-200 rounded-xl p-4">
                                        <div class="text-sm text-slate-600 space-y-2">
                                            <div class="flex justify-between"> <div>Total Belanja</div> <div id="subtotalValue" class="font-medium">Rp {{ number_format($subtotal,0,',','.') }}</div> </div>
                                            <div class="flex justify-between"> <div>Diskon</div> <div id="discountValue" class="text-red-500">-Rp 0</div> </div>
                                            <div class="flex justify-between"> <div>Pajak</div> <div id="taxValue" class="text-amber-600">+Rp 0</div> </div>
                                            <div class="flex justify-between pt-2 border-t border-slate-200 font-bold text-lg text-slate-800"> <div>Total</div> <div id="totalValue">Rp {{ number_format($subtotal,0,',','.') }}</div> </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="paid_amount" id="paidAmountInput" value="0">
                                    <input type="hidden" name="checkout_time" id="checkoutTimeInput" value="">

                                    <div class="mt-4">
                                        <button id="openCheckoutModal" type="button" class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" {{ empty($cart) ? 'disabled' : '' }}>
                                            <i class="fas fa-cash-register"></i>
                                            Bayar Sekarang
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
<!-- Checkout Modal -->
<div id="checkoutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-4 sm:p-6 my-auto">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-receipt text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Rincian Pembayaran</h3>
            </div>
            <button id="checkoutModalClose" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="receiptDetails" class="text-sm text-slate-700">
            <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-200 rounded-xl p-4 mb-4">
                <div class="space-y-2">
                    <div class="flex justify-between"><div>Total Belanja</div><div id="modalSubtotal" class="font-medium">-</div></div>
                    <div class="flex justify-between"><div>Diskon</div><div id="modalDiscount" class="text-red-500">-</div></div>
                    <div class="flex justify-between"><div>Pajak</div><div id="modalTax" class="text-amber-600">-</div></div>
                    <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t border-slate-200 text-slate-800"><div>Total</div><div id="modalTotal">-</div></div>
                </div>
            </div>

            <div id="paidInputWrapper" class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Uang Customer</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-500">Rp</span>
                    </div>
                    <input id="modalPaidInput" type="number" min="0" class="w-full border border-slate-200 rounded-xl pl-12 pr-4 py-3 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all text-lg font-medium" />
                </div>
                <div id="paidError" class="text-sm text-red-600 mt-2 flex items-center gap-2" style="display:none">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Uang kurang</span>
                </div>
            </div>

            <div id="changeWrapper" class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-4 flex justify-between items-center">
                <div class="text-sm font-medium text-slate-700">Kembalian</div>
                <div id="modalChange" class="font-bold text-xl text-emerald-600">Rp 0</div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button id="checkoutCancel" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium transition-colors">Batal</button>
            <button id="checkoutConfirm" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-medium hover:shadow-lg hover:shadow-emerald-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2" disabled>
                <i class="fas fa-print"></i>
                <span>Bayar & Cetak</span>
            </button>
        </div>
    </div>
</div>

<!-- Batch Selection Modal -->
<div id="batchModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-4 sm:p-6 my-auto">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                    <i class="fas fa-cubes text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Pilih Satuan & Batch</h3>
                    <p id="batchModalProductName" class="text-sm text-slate-500"></p>
                </div>
            </div>
            <button id="batchModalClose" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Unit Selection -->
        <div class="mb-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Pilih Satuan:</p>
            <div id="unitSelection" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <!-- Units will be loaded here -->
            </div>
        </div>

        <div class="mb-4">
            <p class="text-sm text-slate-600 mb-3">Pilih batch berdasarkan tanggal kadaluarsa:</p>
            <div id="batchList" class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                <!-- Batch items will be loaded here -->
                <div class="text-center text-slate-500 py-4 flex items-center justify-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Loading...
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                    <input type="number" id="batchQuantity" min="1" value="1" class="w-20 border border-slate-200 rounded-lg px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>
                <div class="text-right">
                    <div class="text-sm text-slate-500">Harga per satuan:</div>
                    <div id="selectedUnitPrice" class="font-bold text-emerald-600 text-lg">Rp 0</div>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-blue-200">
                <div class="text-sm text-slate-500">Batch dipilih:</div>
                <div id="selectedBatchCode" class="font-bold text-blue-600">-</div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button id="batchCancel" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium transition-colors">Batal</button>
            <button id="batchConfirm" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2" disabled>
                <i class="fas fa-cart-plus"></i>
                Tambah ke Keranjang
            </button>
        </div>

        <input type="hidden" id="batchModalProductId" value="">
        <input type="hidden" id="selectedBatchId" value="">
        <input type="hidden" id="selectedUnitType" value="pcs">
    </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Batch Modal Functions
    let currentProductId = null;
    let currentBatches = [];
    let currentUnits = [];
    let currentSelectedUnit = 'pcs';
    let currentUnitPrice = 0;

    function openBatchModal(productId, productName) {
        currentProductId = productId;
        document.getElementById('batchModalProductId').value = productId;
        document.getElementById('batchModalProductName').textContent = productName;
        document.getElementById('selectedBatchId').value = '';
        document.getElementById('selectedBatchCode').textContent = '-';
        document.getElementById('batchQuantity').value = 1;
        document.getElementById('batchConfirm').disabled = true;
        document.getElementById('selectedUnitType').value = 'pcs';
        document.getElementById('selectedUnitPrice').textContent = 'Rp 0';
        currentSelectedUnit = 'pcs';
        currentUnitPrice = 0;
        
        // Show modal
        const modal = document.getElementById('batchModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Load product data (units and batches)
        loadProductData(productId);
    }

    function closeBatchModal() {
        const modal = document.getElementById('batchModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentProductId = null;
        currentBatches = [];
        currentUnits = [];
    }

    async function loadProductData(productId) {
        const batchList = document.getElementById('batchList');
        const unitSelection = document.getElementById('unitSelection');
        batchList.innerHTML = '<div class="text-center text-gray-500 py-4">Loading...</div>';
        unitSelection.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-2">Loading...</div>';
        
        try {
            const response = await fetch(`{{ url('/cashier/api/products') }}/${productId}/batches`);
            const data = await response.json();
            currentBatches = data.batches || [];
            currentUnits = data.units || [{type: 'pcs', label: 'Pcs', price: data.default_price || 0}];
            
            // Render units
            renderUnits(currentUnits);
            
            if (currentBatches.length === 0) {
                batchList.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-2xl text-amber-500"></i>
                        </div>
                        <p class="text-amber-600 font-medium mb-2">Tidak ada batch tersedia</p>
                        <p class="text-sm text-gray-400">Produk ini belum memiliki batch yang aktif.</p>
                        <p class="text-sm text-gray-400">Silakan input batch terlebih dahulu di menu Inventory.</p>
                    </div>
                `;
                document.getElementById('batchConfirm').disabled = true;
                document.getElementById('selectedBatchCode').textContent = 'Pilih Batch';
            } else {
                renderBatches(currentBatches);
            }
        } catch (error) {
            console.error('Error loading product data:', error);
            batchList.innerHTML = '<div class="text-center text-red-500 py-4">Gagal memuat data</div>';
        }
    }

    function renderUnits(units) {
        const unitSelection = document.getElementById('unitSelection');
        
        if (units.length === 0) {
            unitSelection.innerHTML = '<div class="col-span-3 text-center text-gray-500 py-2">Pcs saja</div>';
            return;
        }
        
        // Color mapping for each unit type
        const colors = {
            'pcs': { bg: 'blue-50', border: 'blue-500', text: 'blue-800' },
            'pack': { bg: 'teal-50', border: 'teal-500', text: 'teal-800' },
            'renteng': { bg: 'purple-50', border: 'purple-500', text: 'purple-800' },
            'box': { bg: 'amber-50', border: 'amber-500', text: 'amber-800' },
            'karton': { bg: 'rose-50', border: 'rose-500', text: 'rose-800' }
        };
        
        let html = '';
        units.forEach((unit, index) => {
            const isFirst = index === 0;
            const color = colors[unit.type] || colors['pcs'];
            const contents = unit.contents || '';
            
            html += `
                <button type="button" onclick="selectUnit('${unit.type}', ${unit.price}, '${unit.label}')" 
                        class="unit-btn p-3 rounded-xl border-2 transition-all text-center ${isFirst ? 'border-' + color.border + ' bg-' + color.bg : 'border-slate-200 hover:border-' + color.border}"
                        data-unit="${unit.type}">
                    <div class="font-bold text-sm text-slate-800">${unit.label}</div>
                    ${contents ? `<div class="text-xs text-slate-500 mt-0.5">${contents}</div>` : ''}
                    <div class="text-xs text-emerald-600 font-semibold mt-1">Rp ${formatNumber(unit.price)}</div>
                </button>
            `;
        });
        
        unitSelection.innerHTML = html;
        
        // Auto-select first unit
        if (units.length > 0) {
            selectUnit(units[0].type, units[0].price, units[0].label);
        }
    }

    function selectUnit(unitType, price, label) {
        currentSelectedUnit = unitType;
        currentUnitPrice = price;
        document.getElementById('selectedUnitType').value = unitType;
        document.getElementById('selectedUnitPrice').textContent = 'Rp ' + formatNumber(price);
        
        // Update button styles
        document.querySelectorAll('.unit-btn').forEach(btn => {
            if (btn.dataset.unit === unitType) {
                btn.classList.add('border-blue-500', 'bg-blue-50');
                btn.classList.remove('border-slate-200');
            } else {
                btn.classList.remove('border-blue-500', 'bg-blue-50');
                btn.classList.add('border-slate-200');
            }
        });
        
        // Confirm button remains disabled until batch is selected
        // (batch is now required for all transactions)
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    async function loadBatches(productId) {
        // This is now handled by loadProductData
        await loadProductData(productId);
    }

    function renderBatches(batches) {
        const batchList = document.getElementById('batchList');
        
        if (batches.length === 0) {
            batchList.innerHTML = '<div class="text-center text-gray-500 py-4">Tidak ada batch tersedia</div>';
            return;
        }
        
        let html = '';
        batches.forEach(batch => {
            const expClass = batch.is_expired ? 'bg-red-50 border-red-200' : 
                            (batch.is_expiring_soon ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-gray-200');
            const expText = batch.is_expired ? 'text-red-600' : 
                           (batch.is_expiring_soon ? 'text-orange-600' : 'text-green-600');
            
            html += `
                <div class="batch-item p-3 rounded-lg border cursor-pointer hover:bg-blue-50 transition-colors ${expClass}" 
                     data-batch-id="${batch.id}" 
                     data-batch-code="${batch.batch_code}"
                     data-batch-qty="${batch.quantity}"
                     onclick="selectBatch(${batch.id}, '${batch.batch_code}', ${batch.quantity})">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-sm">${batch.batch_code}</div>
                            <div class="text-xs ${expText}">
                                ${batch.expiration_date ? 'Exp: ' + batch.expiration_date : 'Tidak ada exp date'}
                                ${batch.is_expiring_soon ? ' (Segera kadaluarsa!)' : ''}
                                ${batch.is_expired ? ' (KADALUARSA)' : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold">${batch.quantity}</div>
                            <div class="text-xs text-gray-500">tersedia</div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        batchList.innerHTML = html;
    }

    function selectBatch(batchId, batchCode, maxQty) {
        // Remove selection from all
        document.querySelectorAll('.batch-item').forEach(item => {
            item.classList.remove('ring-2', 'ring-blue-500');
        });
        
        // Add selection to clicked item
        const selectedItem = document.querySelector(`.batch-item[data-batch-id="${batchId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('ring-2', 'ring-blue-500');
        }
        
        document.getElementById('selectedBatchId').value = batchId;
        document.getElementById('selectedBatchCode').textContent = batchCode;
        document.getElementById('batchQuantity').max = maxQty;
        document.getElementById('batchConfirm').disabled = false;
    }

    function confirmBatchSelection() {
        const productId = document.getElementById('batchModalProductId').value;
        const batchId = document.getElementById('selectedBatchId').value;
        const quantity = document.getElementById('batchQuantity').value;
        const unitType = document.getElementById('selectedUnitType').value;
        
        // Validate batch is selected
        if (!batchId) {
            alert('Silakan pilih batch terlebih dahulu');
            return;
        }
        
        // Submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("cashier.pos.add") }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const productInput = document.createElement('input');
        productInput.type = 'hidden';
        productInput.name = 'product_id';
        productInput.value = productId;
        form.appendChild(productInput);
        
        const batchInput = document.createElement('input');
        batchInput.type = 'hidden';
        batchInput.name = 'batch_id';
        batchInput.value = batchId;
        form.appendChild(batchInput);
        
        const qtyInput = document.createElement('input');
        qtyInput.type = 'hidden';
        qtyInput.name = 'quantity';
        qtyInput.value = quantity;
        form.appendChild(qtyInput);
        
        const unitInput = document.createElement('input');
        unitInput.type = 'hidden';
        unitInput.name = 'unit_type';
        unitInput.value = unitType;
        form.appendChild(unitInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    // Event listeners for batch modal
    document.getElementById('batchModalClose')?.addEventListener('click', closeBatchModal);
    document.getElementById('batchCancel')?.addEventListener('click', closeBatchModal);
    document.getElementById('batchConfirm')?.addEventListener('click', confirmBatchSelection);

    function filterProducts() {
        const q = document.getElementById('search').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(q)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Quantity controls for product cards
    function initProductQtyControls(){
        document.querySelectorAll('.product-card').forEach(card => {
            const dec = card.querySelector('.qty-decrease');
            const input = card.querySelector('input[type="number"][name="quantity"]');

            if(!input) return;

            const min = parseInt(input.getAttribute('min')) || 1;
            const max = parseInt(input.getAttribute('max')) || 999;

            function setVal(v){
                let n = parseInt(v, 10) || min;
                if(n < min) n = min;
                if(n > max) n = max;
                input.value = n;
            }

            if(dec){
                dec.addEventListener('click', function(e){
                    e.preventDefault();
                    const cur = parseInt(input.value, 10) || min;
                    setVal(cur - 1);
                });
            }

            input.addEventListener('input', function(){
                let v = this.value;
                if(v === '' || isNaN(v)) return;
                setVal(v);
            });

            let isSubmitting = false;

            input.addEventListener('keydown', function(e){
                if(e.key === 'Enter'){
                    e.preventDefault();
                    if(!isSubmitting){
                        isSubmitting = true;
                        submitQtyFromInput(this);
                        // Reset flag after a short delay
                        setTimeout(() => { isSubmitting = false; }, 100);
                    }
                    this.blur(); // Remove focus to prevent blur event
                }
            });

            input.addEventListener('blur', function(){
                if(!isSubmitting){
                    isSubmitting = true;
                    submitQtyFromInput(this);
                    // Reset flag after a short delay
                    setTimeout(() => { isSubmitting = false; }, 100);
                }
            });
        });
    }

    // Build a quick lookup of items already in cart
    const CART_LOOKUP = (function(){
        const c = @json(array_keys($cart ?? []));
        const map = {};
        (c || []).forEach(id => map[id] = true);
        return map;
    })();

    function submitQtyFromInput(input){
        const idAttr = input.id || '';
        const m = idAttr.match(/qty-input-(\d+)/);
        if(!m) return;
        const pid = m[1];
        let qty = parseInt(input.value, 10) || 1;
        const max = parseInt(input.getAttribute('max')) || 999;
        if(qty < 1) qty = 1;
        if(qty > max) qty = max;

        if(CART_LOOKUP[pid]){
            postToUrl("{{ route('cashier.pos.update') }}", {
                product_id: pid,
                quantity: qty,
                _token: '{{ csrf_token() }}'
            });
        } else {
            postToUrl("{{ route('cashier.pos.add') }}", {
                product_id: pid,
                quantity: qty,
                _token: '{{ csrf_token() }}'
            });
        }
    }

    function postToUrl(url, params){
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        for(const k in params){
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = k;
            inp.value = params[k];
            form.appendChild(inp);
        }
        document.body.appendChild(form);
        form.submit();
    }

    // --- Live totals, modal and interactions ---
    document.addEventListener('DOMContentLoaded', function () {
        initProductQtyControls();

        // subtotal value from server
        const SUBTOTAL = Number(@json($subtotal ?? 0));

        const discountInput = document.getElementById('discountInput');
        const taxInput = document.getElementById('taxInput');
        const subtotalValue = document.getElementById('subtotalValue');
        const discountValue = document.getElementById('discountValue');
        const taxValue = document.getElementById('taxValue');
        const totalValue = document.getElementById('totalValue');

        function formatRp(n) {
            const v = Math.round(n || 0);
            return 'Rp ' + v.toLocaleString('id-ID');
        }

        function computeTotals(){
            const discPct = parseFloat(discountInput.value) || 0;
            const taxPct = parseFloat(taxInput.value) || 0;
            const discAmount = Math.round(SUBTOTAL * (discPct / 100));
            const taxable = Math.max(0, SUBTOTAL - discAmount);
            const taxAmount = Math.round(taxable * (taxPct / 100));
            const total = Math.round(taxable + taxAmount);
            return {discAmount, taxAmount, total};
        }

        function updateTotals() {
            const vals = computeTotals();
            if(subtotalValue) subtotalValue.innerText = formatRp(SUBTOTAL);
            if(discountValue) discountValue.innerText = formatRp(vals.discAmount);
            if(taxValue) taxValue.innerText = formatRp(vals.taxAmount);
            if(totalValue) totalValue.innerText = formatRp(vals.total);
        }

        // initialize
        updateTotals();

        // bind events
        if (discountInput) discountInput.addEventListener('input', updateTotals);
        if (taxInput) taxInput.addEventListener('input', updateTotals);

        // modal elements
        const modal = document.getElementById('checkoutModal');
        const openBtn = document.getElementById('openCheckoutModal');
        const closeBtn = document.getElementById('checkoutModalClose');
        const cancelBtn = document.getElementById('checkoutCancel');
        const paidInput = document.getElementById('modalPaidInput');
        const confirmBtn = document.getElementById('checkoutConfirm');
        const paidHidden = document.getElementById('paidAmountInput');

        function showModal(){ if(modal){ modal.classList.remove('hidden'); modal.classList.add('flex'); } }
        function hideModal(){ if(modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); } }

        function updateChange(){
            const vals = computeTotals();
            const paid = parseFloat(paidInput?.value) || 0;
            const change = Math.round(paid - vals.total);
            const changeEl = document.getElementById('modalChange');
            if(changeEl) changeEl.innerText = formatRp(Math.max(0, change));

            const paidError = document.getElementById('paidError');
            if(paid < vals.total){
                const lacking = Math.round(vals.total - paid);
                if(paidError) { 
                    paidError.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Uang kurang: ' + formatRp(lacking) + '</span>'; 
                    paidError.style.display = 'flex'; 
                }
                if(confirmBtn) confirmBtn.disabled = true;
            } else {
                if(paidError) paidError.style.display = 'none';
                if(confirmBtn) confirmBtn.disabled = false;
            }
        }

        function openHandler(){
            const vals = computeTotals();
            const modalSubtotal = document.getElementById('modalSubtotal');
            const modalDiscount = document.getElementById('modalDiscount');
            const modalTax = document.getElementById('modalTax');
            const modalTotal = document.getElementById('modalTotal');

            if(modalSubtotal) modalSubtotal.innerText = formatRp(SUBTOTAL);
            if(modalDiscount) modalDiscount.innerText = formatRp(vals.discAmount);
            if(modalTax) modalTax.innerText = formatRp(vals.taxAmount);
            if(modalTotal) modalTotal.innerText = formatRp(vals.total);

            // set modal date/time if element exists
            const modalDateEl = document.getElementById('modalDateTime');
            if(modalDateEl){
                const now = new Date();
                modalDateEl.innerText = now.toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }

            // set hidden checkout_time
            const checkoutInput = document.getElementById('checkoutTimeInput');
            if(checkoutInput){
                const now = new Date();
                const y = now.getFullYear();
                const m = String(now.getMonth()+1).padStart(2,'0');
                const d = String(now.getDate()).padStart(2,'0');
                const hh = String(now.getHours()).padStart(2,'0');
                const mm = String(now.getMinutes()).padStart(2,'0');
                const ss = String(now.getSeconds()).padStart(2,'0');
                checkoutInput.value = `${y}-${m}-${d} ${hh}:${mm}:${ss}`;
            }

            // payment method handling
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
            const paidInputWrapper = document.getElementById('paidInputWrapper');
            const changeWrapper = document.getElementById('changeWrapper');
            
            if(paymentMethod !== 'cash'){
                if(paidInputWrapper) paidInputWrapper.style.display = 'none';
                if(changeWrapper) changeWrapper.style.display = 'none';
                if(confirmBtn) confirmBtn.disabled = false;
                if(paidInput) paidInput.value = vals.total;
            } else {
                if(paidInputWrapper) paidInputWrapper.style.display = '';
                if(changeWrapper) changeWrapper.style.display = 'flex';
                if(paidInput) paidInput.value = 0;
                updateChange();
            }

            showModal();
        }

        if(openBtn) openBtn.addEventListener('click', openHandler);
        if(closeBtn) closeBtn.addEventListener('click', hideModal);
        if(cancelBtn) cancelBtn.addEventListener('click', hideModal);
        if(paidInput) paidInput.addEventListener('input', updateChange);

        if(confirmBtn){
            confirmBtn.addEventListener('click', function(e){
                e.preventDefault();
                const vals = computeTotals();
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
                let paid = vals.total;
                if(paymentMethod === 'cash'){
                    paid = parseFloat(paidInput?.value) || 0;
                    if(paid < vals.total){
                        alert('Jumlah pembayaran kurang dari total!');
                        return;
                    }
                }

                if(paidHidden) paidHidden.value = paid;
                const checkoutInput2 = document.getElementById('checkoutTimeInput');
                if(checkoutInput2){
                    const now = new Date();
                    const y = now.getFullYear();
                    const m = String(now.getMonth()+1).padStart(2,'0');
                    const d = String(now.getDate()).padStart(2,'0');
                    const hh = String(now.getHours()).padStart(2,'0');
                    const mm = String(now.getMinutes()).padStart(2,'0');
                    const ss = String(now.getSeconds()).padStart(2,'0');
                    checkoutInput2.value = `${y}-${m}-${d} ${hh}:${mm}:${ss}`;
                }

                // Disable button to prevent double submission
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';

                document.getElementById('checkoutForm').submit();
            });
        }

        // auto-hide flash toast after 1 second
        const toast = document.getElementById('flashToast');
        if(toast){
            setTimeout(() => {
                toast.style.transition = 'opacity 300ms ease, transform 300ms ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(20px)';
                setTimeout(() => { if(toast && toast.parentNode) toast.parentNode.removeChild(toast); }, 350);
            }, 1000);
        }

        // Realtime clock for header
        (function(){
            const el = document.getElementById('realtimeClock');
            function updateClock(){
                if(!el) return;
                const now = new Date();
                el.innerText = now.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
            updateClock();
            setInterval(updateClock, 1000);
        })();
    });
</script>
@endpush
