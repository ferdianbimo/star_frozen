<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class PosController extends Controller
{
    protected function getCart()
    {
        return session('cart', []);
    }

    protected function saveCart(array $cart)
    {
        session(['cart' => $cart]);
    }

    public function index(Request $request)
    {
        $products = Product::where('is_active', 1)->orderBy('name')->get();
        $cart = $this->getCart();

        return view('cashier.pos', compact('products', 'cart'));
    }

    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qty = isset($data['quantity']) ? (int)$data['quantity'] : 1;

        $cart = $this->getCart();

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $qty;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $qty
            ];
        }

        $this->saveCart($cart);

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return Redirect::back()->with('success', 'Product added to cart');
    }

    public function removeFromCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = $this->getCart();

        if (isset($cart[$data['product_id']])) {
            unset($cart[$data['product_id']]);
            $this->saveCart($cart);
        }

        return Redirect::back();
    }

    public function updateCart(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = $this->getCart();

        if ($data['quantity'] <= 0) {
            if (isset($cart[$data['product_id']])) {
                unset($cart[$data['product_id']]);
            }
        } else {
            if (isset($cart[$data['product_id']])) {
                $cart[$data['product_id']]['quantity'] = $data['quantity'];
            }
        }

        $this->saveCart($cart);

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return Redirect::back();
    }

    public function checkout(Request $request)
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return Redirect::back()->with('error', 'Cart is empty');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discountPct = (float) $request->input('discount', 0);
        $taxPct = (float) $request->input('tax', 0);
        $paymentMethod = $request->input('payment_method', 'cash');

        // compute amounts: discount applied on subtotal, tax applied after discount
        $discountAmount = (int) round($subtotal * ($discountPct / 100));
        $taxable = max(0, $subtotal - $discountAmount);
        $taxAmount = (int) round($taxable * ($taxPct / 100));
        $total = (int) round($taxable + $taxAmount);

        // Validate stock availability
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (!$product || $product->stock < $item['quantity']) {
                return Redirect::back()->with('error', "Insufficient stock for {$item['name']}");
            }
        }

        // Reduce stock and create stock logs
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            $previous = $product->stock;
            $product->decrement('stock', $item['quantity']);
            $product->save();

            StockLog::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'previous_stock' => $previous,
                'new_stock' => $product->stock,
                'change' => -$item['quantity'],
                'note' => 'Sale via POS'
            ]);
        }

        $transactionId = Str::upper(Str::random(8));
        $transaction = [
            'id' => $transactionId,
            'cashier_id' => Auth::id(),
            'items' => $cart,
            'subtotal' => $subtotal,
            'discount_pct' => $discountPct,
            'discount_amount' => $discountAmount,
            'tax_pct' => $taxPct,
            'tax_amount' => $taxAmount,
            'payment_method' => $paymentMethod,
            'total' => $total,
            'created_at' => Carbon::now()->toDateTimeString()
        ];

        session(['last_transaction' => $transaction]);
        session()->forget('cart');

        return redirect()->route('cashier.pos.receipt', ['transaction' => $transactionId]);
    }

    public function receipt($transaction)
    {
        $tx = session('last_transaction');
        if (!$tx || $tx['id'] !== $transaction) {
            abort(404);
        }

        return view('cashier.receipt', ['transaction' => $tx]);
    }
}
