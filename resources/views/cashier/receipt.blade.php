<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction['id'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-start justify-center p-6">
        <div class="bg-white shadow rounded p-6 w-full max-w-xl">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-bold">Star Frozen</h2>
                    <p class="text-sm text-gray-600">Receipt ID: {{ $transaction['id'] }}</p>
                </div>
                <div class="text-right text-sm text-gray-600">
                    <div>Cashier: {{ optional(auth()->user())->name }}</div>
                    <div>{{ $transaction['created_at'] }}</div>
                </div>
            </div>

            <table class="w-full text-sm mb-4">
                <thead>
                    <tr class="text-left text-gray-600">
                        <th>Product</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grand = 0; @endphp
                        @foreach($transaction['items'] as $item)
                            @php $line = $item['price'] * $item['quantity']; $grand += $line; @endphp
                            <tr class="border-t">
                                <td class="py-2">{{ $item['name'] }}</td>
                                <td class="py-2 text-right">{{ $item['quantity'] }}</td>
                                <td class="py-2 text-right">Rp {{ number_format($line,0,',','.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-t">
                            <td class="py-2">Subtotal</td>
                            <td></td>
                            <td class="py-2 text-right">Rp {{ number_format($transaction['subtotal'] ?? $grand,0,',','.') }}</td>
                        </tr>
                        <tr class="">
                            <td class="py-1">Discount ({{ $transaction['discount_pct'] ?? 0 }}%)</td>
                            <td></td>
                            <td class="py-1 text-right">Rp {{ number_format($transaction['discount_amount'] ?? 0,0,',','.') }}</td>
                        </tr>
                        <tr class="">
                            <td class="py-1">Tax ({{ $transaction['tax_pct'] ?? 0 }}%)</td>
                            <td></td>
                            <td class="py-1 text-right">Rp {{ number_format($transaction['tax_amount'] ?? 0,0,',','.') }}</td>
                        </tr>
                        <tr class="border-t font-semibold">
                            <td class="py-2">Total</td>
                            <td></td>
                            <td class="py-2 text-right">Rp {{ number_format($transaction['total'],0,',','.') }}</td>
                        </tr>
                </tbody>
            </table>

            <div class="flex justify-end">
                <a href="{{ route('cashier.pos.index') }}" class="px-4 py-2 bg-green-600 text-white rounded">Back to POS</a>
            </div>
        </div>
    </div>
</body>
</html>
