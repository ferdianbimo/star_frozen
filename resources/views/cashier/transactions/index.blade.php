@extends('layouts.cashier')

@section('title','Riwayat Transaksi')

@section('content')
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3 mr-4">
                                <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Transaksi</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats->total_transactions ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-3 mr-4">
                                <i class="fas fa-money-bill-wave text-2xl text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Penjualan</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats->total_sales ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-3 mr-4">
                                <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Penjualan Bersih</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats->net_sales ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-3 mr-4">
                                <i class="fas fa-calculator text-2xl text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Rata-rata/Transaksi</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($stats->average_transaction ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <form method="GET" action="{{ route('cashier.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search mr-1"></i> Cari
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Cari ID atau Invoice Number..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1"></i> Dari Tanggal
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1"></i> Sampai Tanggal
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sort mr-1"></i> Urutkan
                            </label>
                            <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                                <option value="total_besar" {{ request('sort') == 'total_besar' ? 'selected' : '' }}>Total Terbesar</option>
                                <option value="total_kecil" {{ request('sort') == 'total_kecil' ? 'selected' : '' }}>Total Terkecil</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                            <a href="{{ route('cashier.transactions.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition">
                                <i class="fas fa-redo mr-1"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Transactions Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pajak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $currentGroup = null; @endphp
                                @forelse($transactions as $transaction)
                                @php
                                    // Determine grouping key (YYYY-MM) using checkout_time if present, otherwise created_at
                                    $groupKey = null;
                                    if(!empty($transaction->checkout_time) && strpos($transaction->checkout_time, ' ') !== false){
                                        [$d, $t] = explode(' ', $transaction->checkout_time);
                                        [$y, $m, $day] = explode('-', $d);
                                        $groupKey = $y . '-' . $m;
                                    } else {
                                        $groupKey = $transaction->created_at->format('Y-m');
                                    }
                                @endphp
                                @if($groupKey !== $currentGroup)
                                    @php $currentGroup = $groupKey; @endphp
                                    <tr>
                                        <td colspan="9" class="px-6 py-2 bg-gray-50 text-sm font-medium text-gray-700">
                                            @php
                                                try {
                                                    $label = \Carbon\Carbon::createFromFormat('Y-m', $groupKey)->locale('id')->isoFormat('MMMM YYYY');
                                                } catch (\Exception $e) {
                                                    $label = $groupKey;
                                                }
                                            @endphp
                                            {{ $label }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-mono font-medium text-gray-900">#{{ $transaction->id }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-blue-600">{{ $transaction->invoice_number ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if(!empty($transaction->checkout_time))
                                            @php
                                                $ct = $transaction->checkout_time;
                                                $displayDate = $transaction->created_at->format('d/m/Y');
                                                $displayTime = $transaction->created_at->format('H:i:s');
                                                try {
                                                    if(strpos($ct, ' ') !== false){
                                                        [$d, $t] = explode(' ', $ct);
                                                        [$y, $m, $day] = explode('-', $d);
                                                        $displayDate = $day . '/' . $m . '/' . $y;
                                                        $displayTime = substr($t,0,8);
                                                    } else {
                                                        // if stored in alternative format, just show it
                                                        $displayDate = $ct;
                                                    }
                                                } catch (\Exception $e) {
                                                    // fallback to created_at
                                                }
                                            @endphp
                                            <div class="font-medium">{{ $displayDate }}</div>
                                            <div class="text-xs text-gray-500">{{ $displayTime }}</div>
                                        @else
                                            <div class="font-medium">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $transaction->created_at->format('H:i:s') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $transaction->items->count() }} item(s)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($transaction->discount > 0)
                                            <span class="text-red-600">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if(($transaction->tax ?? 0) > 0)
                                            <span class="text-gray-800">Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold text-green-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('cashier.transactions.show', $transaction->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                                            <p class="text-gray-500 text-lg font-medium">Belum ada transaksi</p>
                                            <p class="text-gray-400 text-sm mt-1">Riwayat transaksi Anda akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-white border-t border-gray-200">
                        {{ $transactions->links() }}
                    </div>
                </div>
@endsection
