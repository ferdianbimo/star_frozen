<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Star Frozen</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar (reuse simple sidebar) -->
        <div class="bg-green-800 text-white w-64 py-4 flex flex-col">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-green-200">Point of Sale</p>
            </div>
              <nav class="flex-1">
                <a href="{{ route('cashier.dashboard') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('cashier.pos.index') }}" class="block py-2 px-4 bg-green-900 text-white">
                    <i class="fas fa-cash-register mr-2"></i> Point of Sale
                </a>
                
                <a href="{{ route('cashier.inventory.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-boxes mr-2"></i> Inventory
                </a>
            </nav>
            <div class="px-4 py-2 mt-auto border-t border-green-700">
                <div class="flex items-center mb-2">
                    <span class="rounded-full bg-green-600 w-8 h-8 flex items-center justify-center mr-2">{{ substr(auth()->user()->name,0,1) }}</span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-sm text-green-300 hover:text-white">Log Out</button>
                </form>
            </div>
        </div>

        <div class="flex-1 overflow-hidden">
            <header class="bg-white shadow">
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">Point of Sale</h1>
                </div>
            </header>

            <main class="p-6 h-full">
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
                                            <form method="POST" action="{{ route('cashier.pos.add') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </form>

                                            <div class="text-right text-sm">
                                                <div class="text-gray-700">{{ $product->unit ?? 'pcs' }}</div>
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
                            <h2 class="text-lg font-semibold mb-4">Current Order</h2>

                            <div class="space-y-3" style="height:30vh; overflow:auto;">
                                @php $subtotal = 0; @endphp
                                @if(empty($cart))
                                    <div class="text-gray-500">No items in cart</div>
                                @else
                                    @foreach($cart as $item)
                                        @php $line = $item['price'] * $item['quantity']; $subtotal += $line; @endphp
                                        <div class="flex items-center justify-between border rounded p-2">
                                            <div class="flex items-start">
                                                <div class="w-12 h-12 bg-gray-100 rounded mr-3 flex items-center justify-center">
                                                    @php $cartImage = $item['image'] ?? null; @endphp
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
                                        <label class="block text-sm text-gray-600">Discount (%)</label>
                                        <input id="discountInput" type="number" name="discount" value="0" min="0" class="w-full border rounded p-2">
                                    </div>

                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600">Tax (%)</label>
                                        <input id="taxInput" type="number" name="tax" value="0" min="0" class="w-full border rounded p-2">
                                    </div>

                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600">Payment Method</label>
                                        <div class="flex items-center space-x-3 mt-2">
                                            <label class="inline-flex items-center"><input id="pm_cash" type="radio" name="payment_method" value="cash" checked class="mr-2">Cash</label>
                                            <label class="inline-flex items-center"><input id="pm_qris" type="radio" name="payment_method" value="qris" class="mr-2">Qris</label>
                                        </div>
                                    </div>

                                    <div class="text-sm text-gray-600">
                                        <div class="flex justify-between py-1"> <div>Subtotal</div> <div id="subtotalValue">Rp {{ number_format($subtotal,0,',','.') }}</div> </div>
                                        <div class="flex justify-between py-1"> <div>Discount</div> <div id="discountValue">Rp 0</div> </div>
                                        <div class="flex justify-between py-1"> <div>Tax</div> <div id="taxValue">Rp 0</div> </div>
                                        <div class="flex justify-between py-2 font-semibold text-lg"> <div>Total</div> <div id="totalValue">Rp {{ number_format($subtotal,0,',','.') }}</div> </div>
                                    </div>

                                    <input type="hidden" name="paid_amount" id="paidAmountInput" value="0">

                                    <div class="mt-4">
                                        <button id="openCheckoutModal" type="button" class="w-full py-3 bg-blue-600 text-white rounded">Checkout</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

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
        
        // --- Live totals: discount/tax/total ---
        document.addEventListener('DOMContentLoaded', function () {
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

            function updateTotals() {
                const discPct = parseFloat(discountInput.value) || 0;
                const taxPct = parseFloat(taxInput.value) || 0;

                const discAmount = Math.round(SUBTOTAL * (discPct / 100));
                const taxable = Math.max(0, SUBTOTAL - discAmount);
                const taxAmount = Math.round(taxable * (taxPct / 100));
                const total = Math.round(taxable + taxAmount);

                subtotalValue.innerText = formatRp(SUBTOTAL);
                discountValue.innerText = formatRp(discAmount);
                taxValue.innerText = formatRp(taxAmount);
                totalValue.innerText = formatRp(total);
            }

            // initialize
            updateTotals();

            // bind events
            if (discountInput) discountInput.addEventListener('input', updateTotals);
            if (taxInput) taxInput.addEventListener('input', updateTotals);
        });
    </script>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium">Rincian Pembayaran</h3>
                <button id="checkoutModalClose" class="text-gray-600">&times;</button>
            </div>

            <div id="receiptDetails" class="text-sm text-gray-700">
                <div class="mb-3">
                    <div class="flex justify-between"><div>Subtotal</div><div id="modalSubtotal">-</div></div>
                    <div class="flex justify-between"><div>Discount</div><div id="modalDiscount">-</div></div>
                    <div class="flex justify-between"><div>Tax</div><div id="modalTax">-</div></div>
                    <div class="flex justify-between font-semibold text-lg mt-2"><div>Total</div><div id="modalTotal">-</div></div>
                </div>

                <div class="mb-3">
                    <label class="block text-sm text-gray-600">Jumlah Uang Customer</label>
                    <input id="modalPaidInput" type="number" min="0" class="w-full border rounded p-2" />
                </div>

                <div class="flex justify-between items-center">
                    <div class="text-sm">Kembalian</div>
                    <div id="modalChange" class="font-semibold">Rp 0</div>
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button id="checkoutCancel" class="px-4 py-2 bg-gray-100 rounded">Batal</button>
                <button id="checkoutConfirm" class="px-4 py-2 bg-blue-600 text-white rounded" disabled>Bayar & Selesai</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const openBtn = document.getElementById('openCheckoutModal');
            const modal = document.getElementById('checkoutModal');
            const closeBtn = document.getElementById('checkoutModalClose');
            const cancelBtn = document.getElementById('checkoutCancel');
            const confirmBtn = document.getElementById('checkoutConfirm');
            const paidInput = document.getElementById('modalPaidInput');
            const paidHidden = document.getElementById('paidAmountInput');
            const discountInput = document.getElementById('discountInput');
            const taxInput = document.getElementById('taxInput');
            const subtotal = Number(@json($subtotal ?? 0));

            function formatRp(n){
                const v = Math.round(n || 0);
                return 'Rp ' + v.toLocaleString('id-ID');
            }

            function showModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
            function hideModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }

            function computeTotals(){
                const discPct = parseFloat(discountInput.value) || 0;
                const taxPct = parseFloat(taxInput.value) || 0;
                const discAmount = Math.round(subtotal * (discPct / 100));
                const taxable = Math.max(0, subtotal - discAmount);
                const taxAmount = Math.round(taxable * (taxPct / 100));
                const total = Math.round(taxable + taxAmount);
                return {discAmount, taxAmount, total};
            }

            function openHandler(){
                const vals = computeTotals();
                document.getElementById('modalSubtotal').innerText = formatRp(subtotal);
                document.getElementById('modalDiscount').innerText = formatRp(vals.discAmount);
                document.getElementById('modalTax').innerText = formatRp(vals.taxAmount);
                document.getElementById('modalTotal').innerText = formatRp(vals.total);

                // payment method handling
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
                if(paymentMethod !== 'cash'){
                    // hide paid input and auto-enable confirm
                    paidInput.closest('div.mb-3').style.display = 'none';
                    confirmBtn.disabled = false;
                    paidInput.value = '';
                } else {
                    paidInput.closest('div.mb-3').style.display = '';
                    paidInput.value = vals.total; // pre-fill with total amount
                    // compute change immediately
                    updateChange();
                }

                showModal();
            }

            function updateChange(){
                const vals = computeTotals();
                const paid = parseFloat(paidInput.value) || 0;
                const change = Math.max(0, Math.round(paid - vals.total));
                document.getElementById('modalChange').innerText = formatRp(change);
                confirmBtn.disabled = (paid < vals.total);
            }

            if(openBtn) openBtn.addEventListener('click', openHandler);
            if(closeBtn) closeBtn.addEventListener('click', hideModal);
            if(cancelBtn) cancelBtn.addEventListener('click', hideModal);

            if(paidInput) paidInput.addEventListener('input', updateChange);

            if(confirmBtn){
                confirmBtn.addEventListener('click', function(){
                    const vals = computeTotals();
                    const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
                    let paid = vals.total;
                    if(paymentMethod === 'cash'){
                        paid = parseFloat(paidInput.value) || 0;
                    }
                    // set hidden field then submit
                    paidHidden.value = paid;
                    document.getElementById('checkoutForm').submit();
                });
            }
        });
    </script>
</body>
</html>
