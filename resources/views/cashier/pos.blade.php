@extends('layouts.cashier')

@section('title','Point of Sale')

@section('content')
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div id="flashToast" class="fixed right-6 top-6 z-50 bg-green-600 text-white px-4 py-2 rounded shadow">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4">
                            <input id="search" type="text" placeholder="Search products..." class="w-full border rounded p-3" oninput="filterProducts()">
                        </div>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full">
                    <!-- Left: products -->
                    <div class="lg:col-span-8 bg-white rounded shadow p-4 overflow-auto" style="max-height:80vh;">
                        

                        <div id="products" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
                            @foreach($products as $product)
                                <div class="bg-gray-50 border rounded-lg p-3 product-card" data-name="{{ strtolower($product->name) }}" data-product-id="{{ $product->id }}">
                                    <div class="flex flex-col items-center">
                                        <div class="w-28 h-28 bg-white rounded-md flex items-center justify-center mb-3">
                                            @if($product->image)
                                                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="max-h-28">
                                            @else
                                                <img src="https://via.placeholder.com/120" alt="{{ $product->name }}" class="max-h-28">
                                            @endif
                                        </div>
                                        <div class="w-full text-left">
                                            <h3 class="font-semibold text-sm mb-1">{{ $product->name }}</h3>
                                            <div class="text-blue-600 font-semibold">Rp {{ number_format($product->price,0,',','.') }}</div>
                                            <div class="text-xs text-gray-500">Stock: {{ $product->stock }}</div>
                                            @if($product->batches()->available()->count() > 0)
                                                <div class="text-xs text-green-600">{{ $product->batches()->available()->count() }} batch tersedia</div>
                                            @endif
                                        </div>

                                        <div class="w-full mt-3 flex items-center justify-between">
                                            <form id="addForm-{{ $product->id }}" method="POST" action="{{ route('cashier.pos.add') }}" class="inline-flex items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="batch_id" id="batch-input-{{ $product->id }}" value="">

                                                <button type="button" class="qty-decrease inline-flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-700 rounded-l" data-target="qty-input-{{ $product->id }}">-</button>

                                                <input type="number" name="quantity" id="qty-input-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}" class="w-12 text-center border-t border-b py-1 text-sm" />

                                                <button type="button" onclick="openBatchModal({{ $product->id }}, '{{ $product->name }}')" class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-r" title="Pilih Batch">+
                                                </button>
                                            </form>

                                            <div class="text-right text-sm">
                                                <div class="text-gray-700">{{ $product->unit ?? 'pcs' }}</div>
                                                <div class="text-xs text-gray-500">Stock: {{ $product->stock }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right: current order -->
                    <div class="lg:col-span-4">
                        <div class="bg-white rounded shadow p-4 top-6 mx-auto" style="height:80vh; max-width: 80vh;">
                            <h2 class="text-lg font-semibold mb-4">Pesanan</h2>

                            <div class="space-y-3" style="height:30vh; overflow:auto;">
                                @php $subtotal = 0; @endphp
                                @if(empty($cart))
                                    <div class="text-gray-500">No items in cart</div>
                                @else
                                    @foreach($cart as $cartKey => $item)
                                        @php 
                                            $line = $item['price'] * $item['quantity']; 
                                            $subtotal += $line;
                                            $cartProduct = \App\Models\Product::find($item['id']);
                                            $cartImage = $cartProduct ? $cartProduct->image : null;
                                            $cartBatch = isset($item['batch_id']) ? \App\Models\ProductBatch::find($item['batch_id']) : null;
                                        @endphp
                                        <div class="flex items-center justify-between border rounded p-2">
                                            <div class="flex items-start">
                                                <div class="w-12 h-12 bg-gray-100 rounded mr-3 flex items-center justify-center">
                                                    @if($cartImage)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($cartImage) }}" alt="" class="max-h-10">
                                                    @else
                                                        <img src="https://via.placeholder.com/60" alt="" class="max-h-10">
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium text-sm">{{ $item['name'] }}</div>
                                                    <div class="text-xs text-gray-500">Rp {{ number_format($item['price'],0,',','.') }}</div>
                                                    @if($cartBatch)
                                                        <div class="text-xs text-green-600">
                                                            Batch: {{ $cartBatch->batch_code }}
                                                            @if($cartBatch->expiration_date)
                                                                (Exp: {{ $cartBatch->expiration_date->format('d/m/Y') }})
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
                                                    <input type="hidden" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}">
                                                    <button class="px-2 py-1 bg-gray-200 rounded-l">-</button>
                                                </form>

                                                <div class="px-3">{{ $item['quantity'] }}</div>

                                                <form method="POST" action="{{ route('cashier.pos.add') }}" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="batch_id" value="{{ $item['batch_id'] ?? '' }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button class="px-2 py-1 bg-blue-600 text-white rounded-r">+</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="mt-4 border-t pt-4">
                                <form id="checkoutForm" method="POST" action="{{ route('cashier.pos.checkout') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600">Diskon (%)</label>
                                        <input id="discountInput" type="number" name="discount" value="0" min="0" max="100" class="w-full border rounded p-2">
                                    </div>

                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600">Pajak (%)</label>
                                        <input id="taxInput" type="number" name="tax" value="0" min="0" max="100" class="w-full border rounded p-2">
                                    </div>

                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600">Metode Pembayaran</label>
                                        <div class="flex items-center space-x-3 mt-2">
                                            <label class="inline-flex items-center"><input id="pm_cash" type="radio" name="payment_method" value="cash" checked class="mr-2">Cash</label>
                                            <label class="inline-flex items-center"><input id="pm_qris" type="radio" name="payment_method" value="qris" class="mr-2">Qris</label>
                                        </div>
                                    </div>

                                    <div class="text-sm text-gray-600">
                                        <div class="flex justify-between py-1"> <div>Total Belanja</div> <div id="subtotalValue">Rp {{ number_format($subtotal,0,',','.') }}</div> </div>
                                        <div class="flex justify-between py-1"> <div>Diskon</div> <div id="discountValue">Rp 0</div> </div>
                                        <div class="flex justify-between py-1"> <div>Pajak</div> <div id="taxValue">Rp 0</div> </div>
                                        <div class="flex justify-between py-2 font-semibold text-lg"> <div>Total</div> <div id="totalValue">Rp {{ number_format($subtotal,0,',','.') }}</div> </div>
                                    </div>

                                    <input type="hidden" name="paid_amount" id="paidAmountInput" value="0">
                                    <input type="hidden" name="checkout_time" id="checkoutTimeInput" value="">

                                    <div class="mt-4">
                                        <button id="openCheckoutModal" type="button" class="w-full py-3 bg-blue-600 text-white rounded" {{ empty($cart) ? 'disabled' : '' }}>Bayar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
<!-- Checkout Modal -->
<div id="checkoutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-medium">Rincian Pembayaran</h3>
            <button id="checkoutModalClose" class="text-gray-600">&times;</button>
        </div>

        <div id="receiptDetails" class="text-sm text-gray-700">
            <div class="mb-3">
                <div class="flex justify-between"><div>Total Belanja</div><div id="modalSubtotal">-</div></div>
                <div class="flex justify-between"><div>Diskon</div><div id="modalDiscount">-</div></div>
                <div class="flex justify-between"><div>Pajak</div><div id="modalTax">-</div></div>
                <div class="flex justify-between font-semibold text-lg mt-2"><div>Total</div><div id="modalTotal">-</div></div>
            </div>

            <div class="mb-3">
                <label class="block text-sm text-gray-600">Jumlah Uang Customer</label>
                <input id="modalPaidInput" type="number" min="0" class="w-full border rounded p-2" />
                <div id="paidError" class="text-sm text-red-600 mt-2" style="display:none">Uang kurang</div>
            </div>

            <div class="flex justify-between items-center">
                <div class="text-sm">Kembalian</div>
                <div id="modalChange" class="font-semibold">Rp 0</div>
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-2">
            <button id="checkoutCancel" class="px-4 py-2 bg-gray-100 rounded">Batal</button>
            <button id="checkoutConfirm" class="px-4 py-2 bg-blue-600 text-white rounded" disabled>Bayar & Cetak</button>
        </div>
    </div>
</div>

<!-- Batch Selection Modal -->
<div id="batchModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium">Pilih Batch - <span id="batchModalProductName"></span></h3>
            <button id="batchModalClose" class="text-gray-600 text-2xl">&times;</button>
        </div>

        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Pilih batch berdasarkan tanggal kadaluarsa:</p>
            <div id="batchList" class="space-y-2 max-h-60 overflow-y-auto">
                <!-- Batch items will be loaded here -->
                <div class="text-center text-gray-500 py-4">Loading...</div>
            </div>
        </div>

        <div class="mb-4 bg-blue-50 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                    <input type="number" id="batchQuantity" min="1" value="1" class="w-20 border rounded p-2 mt-1">
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Batch dipilih:</div>
                    <div id="selectedBatchCode" class="font-semibold text-blue-600">-</div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button id="batchCancel" class="px-4 py-2 bg-gray-100 rounded">Batal</button>
            <button id="batchConfirm" class="px-4 py-2 bg-blue-600 text-white rounded" disabled>Tambah ke Keranjang</button>
        </div>

        <input type="hidden" id="batchModalProductId" value="">
        <input type="hidden" id="selectedBatchId" value="">
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Batch Modal Functions
    let currentProductId = null;
    let currentBatches = [];

    function openBatchModal(productId, productName) {
        currentProductId = productId;
        document.getElementById('batchModalProductId').value = productId;
        document.getElementById('batchModalProductName').textContent = productName;
        document.getElementById('selectedBatchId').value = '';
        document.getElementById('selectedBatchCode').textContent = '-';
        document.getElementById('batchQuantity').value = document.getElementById('qty-input-' + productId)?.value || 1;
        document.getElementById('batchConfirm').disabled = true;
        
        // Show modal
        const modal = document.getElementById('batchModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Load batches
        loadBatches(productId);
    }

    function closeBatchModal() {
        const modal = document.getElementById('batchModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentProductId = null;
        currentBatches = [];
    }

    async function loadBatches(productId) {
        const batchList = document.getElementById('batchList');
        batchList.innerHTML = '<div class="text-center text-gray-500 py-4">Loading...</div>';
        
        try {
            const response = await fetch(`{{ url('/cashier/api/products') }}/${productId}/batches`);
            const data = await response.json();
            currentBatches = data.batches || [];
            
            if (currentBatches.length === 0) {
                // No batches available, submit directly without batch
                batchList.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-gray-500 mb-3">Tidak ada batch tersedia untuk produk ini.</p>
                        <p class="text-sm text-gray-400">Produk akan ditambahkan tanpa informasi batch.</p>
                    </div>
                `;
                // Enable confirm button to add without batch
                document.getElementById('batchConfirm').disabled = false;
                document.getElementById('selectedBatchCode').textContent = 'Tanpa Batch';
            } else {
                renderBatches(currentBatches);
            }
        } catch (error) {
            console.error('Error loading batches:', error);
            batchList.innerHTML = '<div class="text-center text-red-500 py-4">Gagal memuat batch</div>';
        }
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
                if(paidError) { paidError.innerText = 'Uang kurang: ' + formatRp(lacking); paidError.style.display = ''; }
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
            if(paymentMethod !== 'cash'){
                if(paidInput) paidInput.closest('div.mb-3').style.display = 'none';
                const modalChangeParent = document.getElementById('modalChange')?.parentElement;
                if(modalChangeParent) modalChangeParent.style.display = 'none';
                if(confirmBtn) confirmBtn.disabled = false;
                if(paidInput) paidInput.value = vals.total;
            } else {
                if(paidInput) paidInput.closest('div.mb-3').style.display = '';
                const modalChangeParent = document.getElementById('modalChange')?.parentElement;
                if(modalChangeParent) modalChangeParent.style.display = 'flex';
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
                confirmBtn.textContent = 'Processing...';

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
