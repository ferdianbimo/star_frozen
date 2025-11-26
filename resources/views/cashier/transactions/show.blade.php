<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi {{ $transaction->id }} - Star Frozen POS</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-green-800 text-white w-64 py-4 flex flex-col">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-bold">Star Frozen POS</h1>
                <p class="text-sm text-green-200">{{ auth()->user()->name }}</p>
            </div>
            
            <nav class="flex-1">
                <a href="{{ route('cashier.dashboard') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                
                <a href="{{ route('cashier.pos.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-cash-register mr-2"></i> Point of Sale
                </a>
                
                <a href="{{ route('cashier.inventory.index') }}" class="block py-2 px-4 hover:bg-green-700 text-white">
                    <i class="fas fa-boxes mr-2"></i> Inventory
                </a>
                
                <a href="{{ route('cashier.transactions.index') }}" class="block py-2 px-4 bg-green-900 text-white">
                    <i class="fas fa-history mr-2"></i> Riwayat Transaksi
                </a>
            </nav>
            
            <div class="px-4 py-2 mt-auto border-t border-green-700">
                <div class="flex items-center mb-2">
                    <span class="rounded-full bg-green-600 w-8 h-8 flex items-center justify-center mr-2">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                    <span class="text-sm">{{ auth()->user()->name }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-sm text-green-300 hover:text-white">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow">
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <a href="{{ route('cashier.transactions.index') }}" class="text-green-600 hover:text-green-700 text-sm">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
                            </a>
                            <h1 class="text-3xl font-bold text-gray-900 mt-2">Detail Transaksi</h1>
                            <p class="text-sm text-gray-500 mt-1">ID: <span class="font-mono font-medium">{{ $transaction->id }}</span></p>
                        </div>
                        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            <i class="fas fa-print mr-2"></i> Cetak
                        </button>
                    </div>
                </div>
            </header>

            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Transaction Info -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6 mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Transaksi
                            </h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">ID Transaksi</p>
                                    <p class="text-base font-mono font-medium text-gray-900">#{{ $transaction->id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Invoice Number</p>
                                    <p class="text-base font-medium text-blue-600">{{ $transaction->invoice_number ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Tanggal & Waktu</p>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Kasir</p>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-shopping-cart mr-2 text-green-500"></i> Daftar Item
                                </h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($transaction->items as $index => $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    @if($item->product && $item->product->image)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->image) }}" 
                                                             alt="{{ $item->product->name ?? 'Product' }}" 
                                                             class="h-10 w-10 rounded object-cover mr-3">
                                                    @else
                                                        <div class="h-10 w-10 rounded bg-gray-200 mr-3 flex items-center justify-center">
                                                            <i class="fas fa-box text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Product #'.$item->product_id }}</div>
                                                        <div class="text-xs text-gray-500">{{ $item->product->category ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $item->quantity }} pack
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calculator mr-2 text-purple-500"></i> Ringkasan
                            </h2>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-500">Subtotal</span>
                                    <span class="text-base font-medium text-gray-900">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                                </div>
                                
                                @if($transaction->discount > 0)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-500">Diskon</span>
                                    <span class="text-base font-medium text-red-600">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center pt-3">
                                    <span class="text-lg font-medium text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-green-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Pembayaran</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Metode</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            <i class="fas fa-money-bill-wave mr-1 text-green-500"></i> Cash
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Status</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Lunas
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <a href="{{ route('cashier.pos.receipt', $transaction->id) }}" target="_blank" 
                                   class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                                    <i class="fas fa-receipt mr-2"></i> Lihat Struk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</body>
</html>
