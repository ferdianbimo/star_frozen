<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\ActivityLogService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * PosController - Mengelola operasional Point of Sale (POS).
 *
 * Controller ini menangani semua operasi POS termasuk:
 * - Manajemen keranjang belanja (cart)
 * - Proses checkout dan pembayaran
 * - Generate struk/receipt transaksi
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class PosController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CART MANAGEMENT (PRIVATE METHODS)
    |--------------------------------------------------------------------------
    */

    /**
     * Mengambil data keranjang dari session.
     *
     * @return array<string, array> Data keranjang belanja
     */
    protected function getCart(): array
    {
        return session('cart', []);
    }

    /**
     * Menyimpan data keranjang ke session.
     *
     * @param  array<string, array> $cart Data keranjang
     * @return void
     */
    protected function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Mendapatkan label unit untuk ditampilkan.
     *
     * @param  string  $unitType Tipe unit (pcs/renteng/pack/box/karton)
     * @param  Product $product  Model product (unused, for future extension)
     * @return string            Label unit yang readable
     */
    protected function getUnitLabel(string $unitType, Product $product): string
    {
        return match($unitType) {
            'karton' => 'Karton',
            'box' => 'Box',
            'pack' => 'Pack',
            'renteng' => 'Renteng',
            'pcs' => 'Pcs',
            default => 'Pcs',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - DISPLAY
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan halaman POS utama.
     *
     * @param  Request $request Request HTTP
     * @return View             View halaman POS dengan produk dan cart
     */
    public function index(Request $request): View
    {
        $products = Product::where('is_active', 1)->orderBy('name')->get();
        $cart = $this->getCart();

        return view('cashier.pos', compact('products', 'cart'));
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - CART OPERATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Menambahkan produk ke keranjang.
     *
     * Validasi batch dan stok sebelum menambahkan.
     * Mendukung multiple unit type (pcs, renteng, pack, box, karton).
     *
     * @param  Request                       $request Request dengan product_id, batch_id, quantity, unit_type
     * @return RedirectResponse|JsonResponse Response redirect atau JSON
     */
    public function addToCart(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_id' => 'required|exists:product_batches,id',
            'quantity' => 'nullable|integer|min:1',
            'unit_type' => 'nullable|string|in:pcs,pack,renteng,box,karton'
        ], [
            'batch_id.required' => 'Batch harus dipilih untuk melakukan transaksi.',
            'batch_id.exists' => 'Batch yang dipilih tidak valid.'
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qty = isset($data['quantity']) ? (int)$data['quantity'] : 1;
        $batchId = $data['batch_id'];
        $unitType = $data['unit_type'] ?? 'pcs';
        
        // Get price for selected unit
        $unitPrice = $product->getPriceForUnit($unitType);
        
        // Calculate quantity in base unit (pcs) for stock validation
        $qtyInPcs = $product->convertToBaseUnit($qty, $unitType);
        
        // Validate batch belongs to product and has enough stock
        $batch = ProductBatch::where('id', $batchId)
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->first();
        
        if (!$batch) {
            return Redirect::back()->with('error', 'Batch tidak valid atau tidak aktif');
        }
        
        if ($batch->quantity < $qtyInPcs) {
            return Redirect::back()->with('error', 'Stok batch tidak mencukupi');
        }

        $cart = $this->getCart();
        
        // Create unique cart key based on product_id, batch_id, and unit_type (batch is always required)
        $cartKey = "{$product->id}_{$batchId}_{$unitType}";

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $qty;
        } else {
            $cart[$cartKey] = [
                'id' => $product->id,
                'batch_id' => $batchId,
                'batch_code' => $batch->batch_code,
                'expiration_date' => $batch->expiration_date ? $batch->expiration_date->format('d/m/Y') : null,
                'name' => $product->name,
                'price' => $unitPrice,
                'unit_type' => $unitType,
                'unit_label' => $this->getUnitLabel($unitType, $product),
                'quantity' => $qty
            ];
        }

        $this->saveCart($cart);

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return Redirect::back()->with('success', 'Berhasil ditambahkan');
    }

    /**
     * Menghapus produk dari keranjang.
     *
     * @param  Request          $request Request dengan product_id
     * @return RedirectResponse          Response redirect
     */
    public function removeFromCart(Request $request): RedirectResponse
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

    /**
     * Mengupdate kuantitas produk di keranjang.
     *
     * Jika quantity = 0, produk akan dihapus dari keranjang.
     *
     * @param  Request                       $request Request dengan product_id, batch_id, quantity
     * @return RedirectResponse|JsonResponse          Response redirect atau JSON
     */
    public function updateCart(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_id' => 'required|exists:product_batches,id',
            'cart_key' => 'nullable|string',
            'unit_type' => 'nullable|string|in:pcs,pack,renteng,box,karton',
            'quantity' => 'required|integer|min:0'
        ], [
            'batch_id.required' => 'Batch harus dipilih untuk melakukan transaksi.'
        ]);

        $cart = $this->getCart();

        $productId = $data['product_id'];
        $batchId = $data['batch_id'];
        $unitType = $data['unit_type'] ?? 'pcs';
        $newQty = (int) $data['quantity'];
        
        // Determine cart key - always includes batch_id since it's required
        $cartKey = $data['cart_key'] ?? "{$productId}_{$batchId}_{$unitType}";
        
        $oldQty = isset($cart[$cartKey]) ? (int) $cart[$cartKey]['quantity'] : 0;

        if ($newQty <= 0) {
            if (isset($cart[$cartKey])) {
                unset($cart[$cartKey]);
            }
            $message = 'Berhasil dihapus';
        } else {
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] = $newQty;
            } else {
                // If it wasn't present, we add minimal info
                $product = Product::find($productId);
                $batch = ProductBatch::find($batchId);
                $unitPrice = $product->getPriceForUnit($unitType);
                $cart[$cartKey] = [
                    'id' => $product->id,
                    'batch_id' => $batchId,
                    'batch_code' => $batch->batch_code,
                    'expiration_date' => $batch->expiration_date ? $batch->expiration_date->format('d/m/Y') : null,
                    'name' => $product->name,
                    'price' => $unitPrice,
                    'unit_type' => $unitType,
                    'unit_label' => $this->getUnitLabel($unitType, $product),
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

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - CHECKOUT & TRANSACTION
    |--------------------------------------------------------------------------
    */

    /**
     * Memproses checkout transaksi.
     *
     * Proses meliputi:
     * 1. Validasi stok dan batch
     * 2. Kalkulasi subtotal, diskon, pajak, total
     * 3. Buat record Transaction dan TransactionItem
     * 4. Update stok produk dan batch
     * 5. Buat StockLog untuk audit trail
     * 6. Log aktivitas
     *
     * @param  Request          $request Request dengan discount, tax, payment_method, paid_amount
     * @return RedirectResponse          Redirect ke halaman receipt atau back dengan error
     */
    public function checkout(Request $request): RedirectResponse
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

        // Validate stock availability (including batch stock) - convert to base unit (pcs)
        foreach ($cart as $item) {
            // Batch is now required for all transactions
            if (empty($item['batch_id'])) {
                return Redirect::back()->with('error', "Batch harus dipilih untuk {$item['name']}");
            }
            
            $product = Product::find($item['id']);
            $unitType = $item['unit_type'] ?? 'pcs';
            $qtyInPcs = $product->convertToBaseUnit($item['quantity'], $unitType);
            
            if (!$product || $product->effective_stock < $qtyInPcs) {
                return Redirect::back()->with('error', "Stok tidak mencukupi untuk {$item['name']}");
            }
            
            // Validate batch stock
            $batch = ProductBatch::find($item['batch_id']);
            if (!$batch || !$batch->is_active) {
                return Redirect::back()->with('error', "Batch tidak valid untuk {$item['name']}");
            }
            if ($batch->quantity < $qtyInPcs) {
                return Redirect::back()->with('error', "Stok batch tidak mencukupi untuk {$item['name']}");
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
                
                // Use batch purchase price if available
                $cost = $product->purchase_price ?? 0;
                if (!empty($item['batch_id'])) {
                    $batch = ProductBatch::find($item['batch_id']);
                    if ($batch && $batch->purchase_price) {
                        $cost = $batch->purchase_price;
                    }
                }
                
                // Calculate profit based on unit sold
                $unitType = $item['unit_type'] ?? 'pcs';
                $qtyInPcs = $product->convertToBaseUnit($item['quantity'], $unitType);
                $itemProfit = ($item['price'] * $item['quantity']) - ($cost * $qtyInPcs);
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
                $unitType = $item['unit_type'] ?? 'pcs';
                $qtyInPcs = $product->convertToBaseUnit($item['quantity'], $unitType);
                
                $previous = $product->stock;
                $product->decrement('stock', $qtyInPcs);
                $product->save();
                
                $batchId = $item['batch_id'] ?? null;
                $batch = null;
                $itemCost = ($product->purchase_price ?? 0);
                
                // Reduce batch stock if batch is specified
                if ($batchId) {
                    $batch = ProductBatch::find($batchId);
                    if ($batch) {
                        $batch->decrement('quantity', $qtyInPcs);
                        $itemCost = $batch->purchase_price ?? $itemCost;
                    }
                }

                // Create transaction item
                $itemSubtotal = $item['price'] * $item['quantity'];
                $itemCostTotal = $itemCost * $qtyInPcs;
                $itemProfit = $itemSubtotal - $itemCostTotal;
                
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'batch_id' => $batchId,
                    'quantity' => $item['quantity'],
                    'unit_sold' => $unitType,
                    'quantity_in_base_unit' => $qtyInPcs,
                    'price' => $item['price'],
                    'cost' => $itemCost,
                    'subtotal' => $itemSubtotal,
                    'profit' => $itemProfit,
                ]);

                // Create stock log (always in base unit - pcs)
                $unitPrice = $item['price'];
                $totalValue = $item['quantity'] * $unitPrice;

                StockLog::create([
                    'product_id' => $product->id,
                    'batch_id' => $batchId,
                    'user_id' => Auth::id(),
                    'previous_stock' => $previous,
                    'new_stock' => $product->stock,
                    'change' => -$qtyInPcs,
                    'unit_type' => 'pcs', // Stock log always in base unit
                    'unit_price' => $unitPrice,
                    'total_value' => $totalValue,
                    'transaction_type' => 'sale',
                    'note' => 'Sale via POS - ' . $invoiceNumber . ' (' . $item['quantity'] . ' ' . $unitType . ')' . ($batch ? ' Batch: ' . $batch->batch_code : '')
                ]);
            }

            DB::commit();

            \Log::info('Transaction completed successfully', [
                'transaction_id' => $transaction->id,
                'invoice_number' => $invoiceNumber,
                'total' => $total
            ]);

            // Log activity
            ActivityLogService::logCheckout(
                "Transaksi {$invoiceNumber} selesai - Total: Rp " . number_format($total, 0, ',', '.'),
                $transaction,
                [
                    'invoice_number' => $invoiceNumber,
                    'total' => $total,
                    'items_count' => count($cart),
                    'payment_method' => $paymentMethod,
                ]
            );

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

    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS - RECEIPT & NEW TRANSACTION
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan struk/receipt transaksi.
     *
     * @param  int|string $transaction ID transaksi
     * @return View                    View receipt
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function receipt($transaction): View
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

        return view('cashier.receipt', [
            'transaction' => $tx,
            'cashier' => auth()->user()
        ]);
    }

    /**
     * Memulai transaksi baru.
     *
     * Menghapus data last_transaction dan cart dari session,
     * kemudian redirect ke halaman POS utama.
     *
     * @return RedirectResponse Redirect ke halaman POS
     */
    public function newTransaction(): RedirectResponse
    {
        session()->forget('last_transaction');
        session()->forget('cart');
        return redirect()->route('cashier.pos.index');
    }
}
