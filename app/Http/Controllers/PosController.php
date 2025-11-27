<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
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

        return Redirect::back()->with('success', 'Berhasil ditambahkan');
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

        $productId = $data['product_id'];
        $newQty = (int) $data['quantity'];
        $oldQty = isset($cart[$productId]) ? (int) $cart[$productId]['quantity'] : 0;

        if ($newQty <= 0) {
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
            }
            $message = 'Berhasil dihapus';
        } else {
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $newQty;
            } else {
                // If it wasn't present, we add minimal info—backend will recalculated on next render
                $product = Product::find($productId);
                $cart[$productId] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $newQty
                ];
            }

            if ($newQty > $oldQty) {
                $message = 'Produk Berhasil ditambahkan';
            } elseif ($newQty < $oldQty) {
                $message = 'Produk Berhasil dikurangi';
            } else {
                $message = 'Produk Berhasil diperbarui';
            }
        }

        $this->saveCart($cart);

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return Redirect::back()->with('success', $message);
    }

    public function checkout(Request $request)
    {
        \Log::info('Checkout started', ['request_data' => $request->all()]);
        
        $cart = $this->getCart();

        if (empty($cart)) {
            \Log::warning('Checkout failed: Cart is empty');
            return Redirect::back()->with('error', 'Cart is empty');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discountPct = (float) $request->input('discount', 0);
        $taxPct = (float) $request->input('tax', 0);
        $paymentMethod = $request->input('payment_method', 'cash');
        $paymentAmount = (float) $request->input('paid_amount', 0);

        // compute amounts: discount applied on subtotal, tax applied after discount
        $discountAmount = (int) round($subtotal * ($discountPct / 100));
        $taxable = max(0, $subtotal - $discountAmount);
        $taxAmount = (int) round($taxable * ($taxPct / 100));
        $total = (int) round($taxable + $taxAmount);
        $changeAmount = max(0, $paymentAmount - $total);

        // Validate stock availability
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (!$product || $product->stock < $item['quantity']) {
                return Redirect::back()->with('error', "Insufficient stock for {$item['name']}");
            }
        }

        DB::beginTransaction();
        try {
            // Create transaction in database
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $totalProfit = 0;
            
            // Calculate total profit
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    throw new \Exception("Product not found: {$item['name']}");
                }
                $cost = $product->purchase_price ?? 0;
                $itemProfit = ($item['price'] - $cost) * $item['quantity'];
                $totalProfit += $itemProfit;
            }
            
            \Log::info('Creating transaction', [
                'invoice' => $invoiceNumber,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_amount' => $paymentAmount
            ]);
            
            // Determine checkout_time (device-provided) or fallback to server now
            $checkoutTime = $request->input('checkout_time') ?? now()->toDateTimeString();

            $transaction = Transaction::create([
                'transaction_number' => strtoupper(Str::random(10)),
                'invoice_number' => $invoiceNumber,
                'user_id' => Auth::id(),
                'checkout_time' => $checkoutTime,
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'discount' => $discountAmount,
                'total' => $total,
                'profit' => $totalProfit,
                'payment_method' => $paymentMethod,
                'payment_amount' => $paymentAmount,
                'change_amount' => $changeAmount,
                'total_amount' => $total,
                'status' => 'completed',
                // set DB timestamps to the same checkout time so transaction.created_at matches device time
                'created_at' => $checkoutTime,
                'updated_at' => $checkoutTime,
            ]);

            // Reduce stock and create transaction items
            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                $previous = $product->stock;
                $product->decrement('stock', $item['quantity']);
                $product->save();

                // Create transaction item
                $itemSubtotal = $item['price'] * $item['quantity'];
                $itemCost = ($product->purchase_price ?? 0) * $item['quantity'];
                $itemProfit = $itemSubtotal - $itemCost;
                
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'cost' => $product->purchase_price ?? 0,
                    'subtotal' => $itemSubtotal,
                    'profit' => $itemProfit,
                ]);

                // Create stock log
                $unitPrice = $item['price'];
                $totalValue = $item['quantity'] * $unitPrice;

                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'previous_stock' => $previous,
                    'new_stock' => $product->stock,
                    'change' => -$item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_value' => $totalValue,
                    'transaction_type' => 'sale',
                    'note' => 'Sale via POS - ' . $invoiceNumber
                ]);
            }

            DB::commit();

            \Log::info('Transaction completed successfully', [
                'transaction_id' => $transaction->id,
                'invoice_number' => $invoiceNumber,
                'total' => $total
            ]);

            // Store for receipt
            $transactionData = [
                'id' => $transaction->id,
                'invoice_number' => $invoiceNumber,
                'cashier_id' => Auth::id(),
                'items' => $cart,
                'subtotal' => $subtotal,
                'discount_pct' => $discountPct,
                'discount_amount' => $discountAmount,
                'tax_pct' => $taxPct,
                'tax_amount' => $taxAmount,
                'payment_method' => $paymentMethod,
                'payment_amount' => $paymentAmount,
                'change_amount' => $changeAmount,
                'total' => $total,
                'created_at' => $transaction->created_at->toDateTimeString(),
                // Keep the client-provided checkout_time (device time) as-is so the receipt displays exactly what the device sent
                'checkout_time' => $request->input('checkout_time')
            ];

            session(['last_transaction' => $transactionData]);
            session()->forget('cart');

            return redirect()->route('cashier.pos.receipt', ['transaction' => $transaction->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Redirect::back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    public function receipt($transaction)
    {
        $tx = session('last_transaction');
        
        \Log::info('Receipt requested', [
            'transaction_param' => $transaction,
            'session_id' => $tx['id'] ?? null,
            'has_session' => !empty($tx)
        ]);
        
        if (!$tx || (int)$tx['id'] !== (int)$transaction) {
            \Log::warning('Receipt not found', [
                'requested_id' => $transaction,
                'session_id' => $tx['id'] ?? null
            ]);
            abort(404, 'Receipt not found. Transaction may have expired.');
        }

        return view('cashier.receipt', ['transaction' => $tx]);
    }

    /**
     * Start a new transaction: clear last transaction and cart from session.
     */
    public function newTransaction()
    {
        session()->forget('last_transaction');
        session()->forget('cart');
        return redirect()->route('cashier.pos.index');
    }
}
