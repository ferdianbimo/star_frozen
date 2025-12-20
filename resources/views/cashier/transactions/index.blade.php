@extends('layouts.cashier')

@section('title','Transaction History')

@section('content')
                <!-- Modern Header -->
                <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-6 mb-8 shadow-xl">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                <i class="fas fa-history text-2xl text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Riwayat Transaksi</h2>
                                <p class="text-slate-400 text-sm">Pantau semua transaksi Anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-slate-700/50 rounded-xl border border-slate-600">
                                <span class="text-slate-400 text-sm"><i class="fas fa-clock mr-2"></i>{{ now()->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-8">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 lg:p-6 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs lg:text-sm text-slate-500 font-medium">Total Transaksi</p>
                                <p class="text-lg lg:text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats->total_transactions ?? 0) }}</p>
                            </div>
                            <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                <i class="fas fa-shopping-cart text-base lg:text-xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 lg:p-6 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs lg:text-sm text-slate-500 font-medium">Total Penjualan</p>
                                <p class="text-lg lg:text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats->total_sales ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <i class="fas fa-money-bill-wave text-base lg:text-xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 lg:p-6 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs lg:text-sm text-slate-500 font-medium">Penjualan Bersih</p>
                                <p class="text-lg lg:text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats->net_sales ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                                <i class="fas fa-chart-line text-base lg:text-xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 lg:p-6 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs lg:text-sm text-slate-500 font-medium">Rata-rata/Transaksi</p>
                                <p class="text-lg lg:text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats->average_transaction ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-10 h-10 lg:w-14 lg:h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                                <i class="fas fa-calculator text-base lg:text-xl text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-filter text-white text-xs"></i>
                        </div>
                        <h3 class="font-semibold text-slate-800">Filter Transaksi</h3>
                    </div>
                    <form method="GET" action="{{ route('cashier.transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div class="sm:col-span-2 lg:col-span-1">
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                                Cari
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                    <i class="fas fa-search text-slate-400 text-sm"></i>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="ID atau Invoice..." 
                                   class="w-full pl-11 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300">
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                                Dari Tanggal
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300">
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                                Sampai Tanggal
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300">
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                                Urutkan
                            </label>
                            <div class="relative">
                                <select name="sort" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer hover:border-slate-300">
                                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                                    <option value="total_besar" {{ request('sort') == 'total_besar' ? 'selected' : '' }}>Total Terbesar</option>
                                    <option value="total_kecil" {{ request('sort') == 'total_kecil' ? 'selected' : '' }}>Total Terkecil</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-1">
                            <button type="submit" class="flex-1 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:shadow-lg hover:shadow-blue-500/25 text-white rounded-xl font-semibold transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-search"></i>
                                <span class="hidden sm:inline">Cari</span>
                            </button>
                            <a href="{{ route('cashier.transactions.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition-all" title="Reset">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Transactions Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Invoice</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tanggal & Waktu</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Subtotal</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Diskon</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Pajak</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
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
                                    @php 
                                        $currentGroup = $groupKey;
                                        $monthData = $monthlyTotals[$groupKey] ?? null;
                                        $monthTotal = $monthData ? $monthData->total : 0;
                                        $monthCount = $monthData ? $monthData->count : 0;
                                    @endphp
                                    <tr>
                                        <td colspan="9" class="px-6 py-3 bg-gradient-to-r from-indigo-50 via-blue-50 to-indigo-50 border-l-4 border-indigo-500">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/30">
                                                        <i class="fas fa-calendar-alt text-white text-xs"></i>
                                                    </div>
                                                    @php
                                                        try {
                                                            $label = \Carbon\Carbon::createFromFormat('Y-m', $groupKey)->locale('id')->isoFormat('MMMM YYYY');
                                                        } catch (\Exception $e) {
                                                            $label = $groupKey;
                                                        }
                                                    @endphp
                                                    <span class="text-sm font-bold text-slate-800">{{ $label }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-lg font-bold text-indigo-600">Rp {{ number_format($monthTotal, 0, ',', '.') }}</div>
                                                    <div class="text-xs text-slate-500">{{ $monthCount }} transaksi</div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-mono font-medium text-slate-700 bg-slate-100 px-2 py-1 rounded">#{{ $transaction->id }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-blue-600">{{ $transaction->invoice_number ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
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
                                            <div class="text-xs text-slate-500">{{ $displayTime }}</div>
                                        @else
                                            <div class="font-medium">{{ $transaction->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-slate-500">{{ $transaction->created_at->format('H:i:s') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 border border-blue-200">
                                            <i class="fas fa-box mr-1"></i>{{ $transaction->items->count() }} item(s)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-700">
                                        Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($transaction->discount > 0)
                                            <span class="text-red-600 font-medium">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if(($transaction->tax ?? 0) > 0)
                                            <span class="text-amber-600 font-medium">+Rp {{ number_format($transaction->tax, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('cashier.transactions.show', $transaction->id) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:shadow-lg hover:shadow-indigo-500/30 transition-all text-xs">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center mb-4">
                                                <i class="fas fa-receipt text-4xl text-slate-400"></i>
                                            </div>
                                            <p class="text-slate-600 text-lg font-medium">Belum ada transaksi</p>
                                            <p class="text-slate-400 text-sm mt-1">Riwayat transaksi Anda akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination -->
                    @if($transactions->hasPages())
                        <div class="px-4 lg:px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="text-sm text-slate-600 text-center sm:text-left">
                                Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari {{ $transactions->total() }}
                            </div>
                            <div class="flex items-center gap-1 sm:gap-2 flex-wrap justify-center">
                                {{-- Previous Button --}}
                                @if ($transactions->onFirstPage())
                                    <span class="px-3 py-2 bg-slate-200 text-slate-400 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </span>
                                @else
                                    <a href="{{ $transactions->previousPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </a>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach(range(1, $transactions->lastPage()) as $page)
                                    @if($page == $transactions->currentPage())
                                        <span class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium shadow-lg shadow-blue-500/30">{{ $page }}</span>
                                    @elseif($page == 1 || $page == $transactions->lastPage() || abs($page - $transactions->currentPage()) <= 2)
                                        <a href="{{ $transactions->url($page) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">{{ $page }}</a>
                                    @elseif(abs($page - $transactions->currentPage()) == 3)
                                        <span class="px-2 py-2 text-slate-400">...</span>
                                    @endif
                                @endforeach

                                {{-- Next Button --}}
                                @if ($transactions->hasMorePages())
                                    <a href="{{ $transactions->nextPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </a>
                                @else
                                    <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
@endsection

@push('scripts')
<script>
    // Auto-refresh setiap 30 detik untuk update data transaksi otomatis
    // Hanya refresh pada page 1 untuk menghindari gangguan saat navigasi pagination
    @if(request()->input('page', 1) == 1)
    setInterval(function() {
        console.log('Auto-refreshing transaction history...');
        location.reload();
    }, 30000); // 30 detik
    @endif
</script>
@endpush
