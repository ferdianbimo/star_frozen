@extends('layouts.cashier')

@section('title', 'POS')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">POS</h1>
</div>

<!-- Search Bar -->
<div class="mb-4">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i class="fas fa-search text-slate-400"></i>
        </div>
        <input
            id="searchInput"
            type="text"
            placeholder="Search products..."
            class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            oninput="filterProducts()">
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Products Grid -->
    <div class="lg:col-span-2">
        <div id="productsGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
            @foreach($products as $product)
                @php
                    $availableBatches = $product->batches()->available()->notExpired();
                    $batchStock = $availableBatches->sum('quantity');
                    $hasBatches = $availableBatches->count() > 0;
                @endphp
                <div class="bg-white border border-slate-200 rounded-lg p-4 product-card" data-name="{{ strtolower($product->name) }}">
                    <!-- Product Image -->
                    <div class="w-full h-32 bg-slate-50 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                        @if($product->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="max-h-32 object-contain"
                                 onerror="this.parentElement.innerHTML='<i class=\'fas fa-box-open text-3xl text-slate-300\'></i>'">
                        @else
                            <i class="fas fa-box-open text-3xl text-slate-300"></i>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <h3 class="font-semibold text-slate-800 mb-1 text-sm line-clamp-2">{{ $product->name }}</h3>
                    <p class="text-blue-600 font-bold mb-2">Rp. {{ number_format($product->price, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-500 mb-3">Stock: {{ $batchStock }}</p>

                    <!-- Quantity Controls -->
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="decreaseQuantity({{ $product->id }})"
                            class="w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded text-slate-600 transition-colors"
                            {{ !$hasBatches || $batchStock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-minus text-xs"></i>
                        </button>

                        <input
                            type="number"
                            id="qty-{{ $product->id }}"
                            value="0"
                            min="0"
                            max="{{ $batchStock }}"
                            class="w-12 h-8 text-center border border-slate-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updateQuantity({{ $product->id }})"
                            {{ !$hasBatches || $batchStock <= 0 ? 'disabled' : '' }}>

                        <button
                            type="button"
                            onclick="increaseQuantity({{ $product->id }})"
                            class="w-8 h-8 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors"
                            {{ !$hasBatches || $batchStock <= 0 ? 'disabled' : '' }}>
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Current Order -->
    <div class="lg:col-span-1">
        <div class="bg-white border border-slate-200 rounded-lg p-4 sticky top-4">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Current Order</h2>

            <!-- Order Items -->
            <div id="orderItems" class="space-y-3 mb-4 max-h-48 overflow-y-auto">
                <!-- Items will be added here via JavaScript -->
                <div id="emptyCart" class="text-center py-8 text-slate-400">
                    <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                    <p class="text-sm">No items yet</p>
                </div>
            </div>

            <form method="POST" action="{{ route('cashier.pos.checkout') }}" id="checkoutForm">
                @csrf

                <!-- Discount & Tax -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Discount (%)</label>
                        <input
                            type="number"
                            name="discount"
                            id="discountInput"
                            value="0"
                            min="0"
                            max="100"
                            class="w-full px-3 py-2 border border-slate-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="calculateTotal()">
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Tax (%)</label>
                        <input
                            type="number"
                            name="tax"
                            id="taxInput"
                            value="0"
                            min="0"
                            max="100"
                            class="w-full px-3 py-2 border border-slate-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="calculateTotal()">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                    <label class="block text-sm text-slate-600 mb-2">Payment Method</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" checked class="text-blue-500">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-money-bill-wave text-emerald-500"></i>
                                Cash
                            </span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="payment_method" value="qris" class="text-blue-500">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-qrcode text-blue-500"></i>
                                Qris
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Summary -->
                <div class="border-t border-slate-200 pt-4 space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Subtotal:</span>
                        <span class="font-semibold" id="subtotalDisplay">Rp. 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Discount:</span>
                        <span class="font-semibold" id="discountDisplay">0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Tax:</span>
                        <span class="font-semibold" id="taxDisplay">0</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-slate-200">
                        <span>Total:</span>
                        <span id="totalDisplay">Rp. 0</span>
                    </div>
                </div>

                <input type="hidden" name="items" id="itemsInput">
                <input type="hidden" name="paid_amount" id="paidAmountInput">

                <!-- Checkout Button -->
                <button
                    type="button"
                    onclick="openCheckoutModal()"
                    id="checkoutBtn"
                    class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                    Checkout
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-slate-800 mb-4">Payment</h3>

        <div class="space-y-3 mb-4">
            <div class="flex justify-between">
                <span class="text-slate-600">Total:</span>
                <span class="font-bold" id="modalTotal">Rp. 0</span>
            </div>

            <div id="cashPaymentSection">
                <label class="block text-sm text-slate-600 mb-1">Amount Paid</label>
                <input
                    type="number"
                    id="paidInput"
                    class="w-full px-3 py-2 border border-slate-200 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="calculateChange()">

                <div id="changeDisplay" class="mt-2 p-3 bg-emerald-50 rounded hidden">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Change:</span>
                        <span class="font-bold text-emerald-600" id="changeAmount">Rp. 0</span>
                    </div>
                </div>

                <div id="insufficientError" class="mt-2 text-red-500 text-sm hidden">
                    Insufficient payment amount
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button
                type="button"
                onclick="closeCheckoutModal()"
                class="flex-1 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                Cancel
            </button>
            <button
                type="button"
                onclick="confirmCheckout()"
                id="confirmBtn"
                class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors disabled:opacity-50"
                disabled>
                Confirm & Print
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let cart = {};
let products = {!! json_encode($products->map(function($p) {
    $batches = $p->batches()->available()->notExpired()->get();
    return [
        'id' => $p->id,
        'name' => $p->name,
        'price' => $p->price,
        'stock' => $batches->sum('quantity'),
        'batch_id' => optional($batches->first())->id
    ];
})) !!};

// Filter products
function filterProducts() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(search)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// Quantity controls
function increaseQuantity(productId) {
    const input = document.getElementById(`qty-${productId}`);
    const max = parseInt(input.max);
    const current = parseInt(input.value);

    if (current < max) {
        input.value = current + 1;
        updateCart(productId, current + 1);
    }
}

function decreaseQuantity(productId) {
    const input = document.getElementById(`qty-${productId}`);
    const current = parseInt(input.value);

    if (current > 0) {
        input.value = current - 1;
        updateCart(productId, current - 1);
    }
}

function updateQuantity(productId) {
    const input = document.getElementById(`qty-${productId}`);
    const quantity = parseInt(input.value) || 0;
    updateCart(productId, quantity);
}

function updateCart(productId, quantity) {
    const product = products.find(p => p.id === productId);

    if (quantity > 0 && quantity <= product.stock) {
        cart[productId] = {
            id: productId,
            name: product.name,
            price: product.price,
            quantity: quantity,
            batch_id: product.batch_id
        };
    } else {
        delete cart[productId];
    }

    renderCart();
    calculateTotal();
}

function removeFromCart(productId) {
    delete cart[productId];
    document.getElementById(`qty-${productId}`).value = 0;
    renderCart();
    calculateTotal();
}

function renderCart() {
    const container = document.getElementById('orderItems');
    const emptyCart = document.getElementById('emptyCart');
    const checkoutBtn = document.getElementById('checkoutBtn');

    const items = Object.values(cart);

    if (items.length === 0) {
        emptyCart.classList.remove('hidden');
        checkoutBtn.disabled = true;
        return;
    }

    emptyCart.classList.add('hidden');
    checkoutBtn.disabled = false;

    container.innerHTML = items.map(item => `
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <div class="flex-1">
                <h4 class="font-semibold text-sm text-slate-800">${item.name}</h4>
                <p class="text-xs text-blue-600">Rp. ${formatNumber(item.price)}</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="updateCart(${item.id}, ${item.quantity - 1})"
                    class="w-6 h-6 flex items-center justify-center bg-slate-100 hover:bg-slate-200 rounded text-xs">
                    -
                </button>
                <span class="w-6 text-center text-sm font-semibold">${item.quantity}</span>
                <button
                    type="button"
                    onclick="updateCart(${item.id}, ${item.quantity + 1})"
                    class="w-6 h-6 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                    +
                </button>
            </div>
        </div>
    `).join('') + `<div id="emptyCart" class="hidden"></div>`;
}

function calculateTotal() {
    const items = Object.values(cart);
    const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    const discountPercent = parseFloat(document.getElementById('discountInput').value) || 0;
    const taxPercent = parseFloat(document.getElementById('taxInput').value) || 0;

    const discountAmount = subtotal * (discountPercent / 100);
    const taxAmount = (subtotal - discountAmount) * (taxPercent / 100);
    const total = subtotal - discountAmount + taxAmount;

    document.getElementById('subtotalDisplay').textContent = `Rp. ${formatNumber(subtotal)}`;
    document.getElementById('discountDisplay').textContent = discountAmount > 0 ? `-Rp. ${formatNumber(discountAmount)}` : '0';
    document.getElementById('taxDisplay').textContent = taxAmount > 0 ? `+Rp. ${formatNumber(taxAmount)}` : '0';
    document.getElementById('totalDisplay').textContent = `Rp. ${formatNumber(total)}`;
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Checkout Modal
function openCheckoutModal() {
    const modal = document.getElementById('checkoutModal');
    const items = Object.values(cart);
    const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discountPercent = parseFloat(document.getElementById('discountInput').value) || 0;
    const taxPercent = parseFloat(document.getElementById('taxInput').value) || 0;
    const discountAmount = subtotal * (discountPercent / 100);
    const taxAmount = (subtotal - discountAmount) * (taxPercent / 100);
    const total = subtotal - discountAmount + taxAmount;

    document.getElementById('modalTotal').textContent = `Rp. ${formatNumber(total)}`;
    document.getElementById('paidInput').value = '';
    document.getElementById('changeDisplay').classList.add('hidden');
    document.getElementById('insufficientError').classList.add('hidden');
    document.getElementById('confirmBtn').disabled = true;

    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const cashSection = document.getElementById('cashPaymentSection');

    if (paymentMethod === 'qris') {
        cashSection.classList.add('hidden');
        document.getElementById('confirmBtn').disabled = false;
    } else {
        cashSection.classList.remove('hidden');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCheckoutModal() {
    const modal = document.getElementById('checkoutModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function calculateChange() {
    const items = Object.values(cart);
    const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discountPercent = parseFloat(document.getElementById('discountInput').value) || 0;
    const taxPercent = parseFloat(document.getElementById('taxInput').value) || 0;
    const discountAmount = subtotal * (discountPercent / 100);
    const taxAmount = (subtotal - discountAmount) * (taxPercent / 100);
    const total = subtotal - discountAmount + taxAmount;

    const paid = parseFloat(document.getElementById('paidInput').value) || 0;
    const change = paid - total;

    const changeDisplay = document.getElementById('changeDisplay');
    const insufficientError = document.getElementById('insufficientError');
    const confirmBtn = document.getElementById('confirmBtn');

    if (paid >= total) {
        changeDisplay.classList.remove('hidden');
        insufficientError.classList.add('hidden');
        document.getElementById('changeAmount').textContent = `Rp. ${formatNumber(change)}`;
        confirmBtn.disabled = false;
    } else {
        changeDisplay.classList.add('hidden');
        insufficientError.classList.remove('hidden');
        confirmBtn.disabled = true;
    }
}

function confirmCheckout() {
    const items = Object.values(cart);
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const paidAmount = paymentMethod === 'cash' ? parseFloat(document.getElementById('paidInput').value) : 0;

    // Prepare form data
    document.getElementById('itemsInput').value = JSON.stringify(items);
    document.getElementById('paidAmountInput').value = paidAmount;

    // Submit form
    document.getElementById('checkoutForm').submit();
}

// Listen to payment method changes
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (document.getElementById('checkoutModal').classList.contains('flex')) {
            closeCheckoutModal();
            openCheckoutModal();
        }
    });
});

// ESC to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCheckoutModal();
    }
});
</script>
@endpush
