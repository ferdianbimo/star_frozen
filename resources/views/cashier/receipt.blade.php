<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction['invoice_number'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-start justify-center p-6">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-6 pb-4 border-b-2 border-green-600">
                <h1 class="text-3xl font-bold text-green-800">Star Frozen</h1>
                <p class="text-sm text-gray-600 mt-1">Point of Sale System</p>
            </div>

            <!-- Transaction Info -->
            <div class="mb-6 bg-gray-50 p-4 rounded">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Invoice Number:</p>
                        <p class="font-semibold">{{ $transaction['invoice_number'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Transaction ID:</p>
                        <p class="font-semibold">#{{ $transaction['id'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Cashier:</p>
                        <p class="font-semibold">{{ optional(auth()->user())->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Date & Time:</p>
                        <p class="font-semibold">{{ \Carbon\Carbon::parse($transaction['created_at'])->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-600">Payment Method:</p>
                        <p class="font-semibold uppercase">{{ $transaction['payment_method'] ?? 'Cash' }}</p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="border-b-2 border-gray-300 text-gray-700">
                        <th class="text-left py-2">Product</th>
                        <th class="text-center py-2">Qty</th>
                        <th class="text-right py-2">Price</th>
                        <th class="text-right py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    @php $grand = 0; @endphp
                    @foreach($transaction['items'] as $item)
                        @php $line = $item['price'] * $item['quantity']; $grand += $line; @endphp
                        <tr class="border-b border-gray-200">
                            <td class="py-3">{{ $item['name'] }}</td>
                            <td class="py-3 text-center">{{ $item['quantity'] }}</td>
                            <td class="py-3 text-right">Rp {{ number_format($item['price'],0,',','.') }}</td>
                            <td class="py-3 text-right font-semibold">Rp {{ number_format($line,0,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <div class="border-t-2 border-gray-300 pt-4 mb-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">Rp {{ number_format($transaction['subtotal'] ?? $grand,0,',','.') }}</span>
                    </div>
                    @if(($transaction['discount_amount'] ?? 0) > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Discount ({{ $transaction['discount_pct'] ?? 0 }}%):</span>
                        <span class="font-semibold">- Rp {{ number_format($transaction['discount_amount'] ?? 0,0,',','.') }}</span>
                    </div>
                    @endif
                    @if(($transaction['tax_amount'] ?? 0) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax ({{ $transaction['tax_pct'] ?? 0 }}%):</span>
                        <span class="font-semibold">Rp {{ number_format($transaction['tax_amount'] ?? 0,0,',','.') }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="flex justify-between mt-4 pt-4 border-t-2 border-green-600 text-lg">
                    <span class="font-bold text-gray-800">TOTAL:</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($transaction['total'],0,',','.') }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-green-50 p-4 rounded mb-6 text-sm">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Payment Amount:</span>
                    <span class="font-semibold">Rp {{ number_format($transaction['payment_amount'] ?? $transaction['total'],0,',','.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Change:</span>
                    <span class="font-semibold">Rp {{ number_format(($transaction['payment_amount'] ?? $transaction['total']) - $transaction['total'],0,',','.') }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mb-6">
                <p class="text-gray-600 text-sm mb-2">Thank you for your purchase!</p>
                <p class="text-gray-500 text-xs">Please keep this receipt for your records</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-center">
                <button onclick="window.print()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2">
                    <i class="fas fa-print"></i>
                    Print Receipt
                </button>
                <a href="{{ route('cashier.pos.index') }}" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2">
                    <i class="fas fa-cash-register"></i>
                    New Transaction
                </a>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body { background: white; }
            .no-print { display: none; }
            button, a { display: none; }
        }
    </style>
</body>
</html>
