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
                                <div class="bg-gray-50 border rounded-lg p-3 product-card" data-name="{{ strtolower($product->name) }}">
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
                                        </div>

                                        <div class="w-full mt-3 flex items-center justify-between">
                                            <form id="addForm-{{ $product->id }}" method="POST" action="{{ route('cashier.pos.add') }}" class="inline-flex items-center">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                                <button type="button" class="qty-decrease inline-flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-700 rounded-l" data-target="qty-input-{{ $product->id }}">-</button>

                                                <input type="number" name="quantity" id="qty-input-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}" class="w-12 text-center border-t border-b py-1 text-sm" />

                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-r">+
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
                        <div class="bg-white rounded shadow p-4 top-6" style="height:80vh;">
                            <h2 class="text-lg font-semibold mb-4">Pesanan</h2>

                            <div class="space-y-3" style="height:30vh; overflow:auto;">
                                @php $subtotal = 0; @endphp
                                @if(empty($cart))
                                    <div class="text-gray-500">No items in cart</div>
                                @else
                                    @foreach($cart as $item)
                                        @php 
                                            $line = $item['price'] * $item['quantity']; 
                                            $subtotal += $line;
                                            $cartProduct = \App\Models\Product::find($item['id']);
                                            $cartImage = $cartProduct ? $cartProduct->image : null;
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
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <form method="POST" action="{{ route('cashier.pos.update') }}" class="inline-flex">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                    <input type="hidden" name="quantity" value="{{ max(0, $item['quantity'] - 1) }}">
                                                    <button class="px-2 py-1 bg-gray-200 rounded-l">-</button>
                                                </form>

                                                <div class="px-3">{{ $item['quantity'] }}</div>

                                                <form method="POST" action="{{ route('cashier.pos.add') }}" class="inline-block">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
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
                                        <input id="discountInput" type="number" name="discount" value="0" min="0" class="w-full border rounded p-2">
                                    </div>

                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600">Pajak (%)</label>
                                        <input id="taxInput" type="number" name="tax" value="0" min="0" class="w-full border rounded p-2">
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

@endsection

@push('scripts')
<script>
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

            input.addEventListener('keydown', function(e){
                if(e.key === 'Enter'){
                    e.preventDefault();
                    submitQtyFromInput(this);
                }
            });

            input.addEventListener('blur', function(){
                submitQtyFromInput(this);
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
