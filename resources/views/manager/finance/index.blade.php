@extends('layouts.manager')

@section('title','Laporan Keuangan')

@section('content')
    <!-- Finance Header -->
    <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl shadow-xl mb-8 overflow-hidden">
        <div class="relative px-6 py-8">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/10 to-blue-600/10"></div>
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-wallet text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">Laporan Keuangan</h1>
                        <p class="text-slate-400 mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                    <a href="{{ route('manager.dashboard') }}" class="bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 px-3 sm:px-4 py-2 rounded-xl font-medium text-sm transition-all duration-200 flex items-center gap-2 border border-white/20">
                        <i class="fas fa-arrow-left"></i>
                        <span class="hidden sm:inline">Kembali</span>
                    </a>
                    <a href="{{ route('manager.finance.income') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-3 sm:px-4 py-2 rounded-xl font-medium text-sm shadow-lg shadow-green-500/30 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-chart-line"></i>
                        <span class="hidden sm:inline">History</span> Pemasukan
                    </a>
                    <a href="{{ route('manager.finance.expenses') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 sm:px-4 py-2 rounded-xl font-medium text-sm shadow-lg shadow-blue-500/30 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-receipt"></i>
                        <span class="hidden sm:inline">Kelola</span> Pengeluaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Badge - Data dari Stock Logs -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600 text-xl"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-blue-800">Data Otomatis dari Stok Keluar</p>
            <p class="text-xs text-blue-600">Setiap transaksi POS dan stok keluar inventory akan langsung terupdate dalam 30 detik</p>
        </div>
    </div>
    
    <!-- Filter Period -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-6 mb-6">
        <form method="GET" action="{{ route('manager.finance.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-end gap-3 lg:gap-4">
            <div class="flex-1 w-full">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <i class="fas fa-calendar-alt mr-1 text-slate-400"></i>
                    Periode Laporan
                </label>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <span class="text-slate-400 self-center font-medium hidden sm:block">sampai</span>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>
            <button type="submit" class="w-full lg:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium shadow-lg shadow-blue-500/30 transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-filter"></i>
                Terapkan Filter
            </button>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-8">
        <!-- Total Income -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-arrow-down text-white text-sm lg:text-lg"></i>
                </div>
                @if($incomePercentage >= 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <i class="fas fa-arrow-up text-[10px]"></i> {{ number_format($incomePercentage, 1) }}%
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                        <i class="fas fa-arrow-down text-[10px]"></i> {{ number_format(abs($incomePercentage), 1) }}%
                    </span>
                @endif
            </div>
            <p class="text-xs lg:text-sm text-slate-500 font-medium">Total Pemasukan</p>
            <h3 class="text-lg lg:text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-1 hidden sm:block">vs periode lalu</p>
        </div>

        <!-- Total Expenses -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-arrow-up text-white text-sm lg:text-lg"></i>
                </div>
                @if($expensePercentage >= 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                        <i class="fas fa-arrow-up text-[10px]"></i> {{ number_format($expensePercentage, 1) }}%
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <i class="fas fa-arrow-down text-[10px]"></i> {{ number_format(abs($expensePercentage), 1) }}%
                    </span>
                @endif
            </div>
            <p class="text-xs lg:text-sm text-slate-500 font-medium">Total Pengeluaran</p>
            <h3 class="text-lg lg:text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-1 hidden sm:block">vs periode lalu</p>
        </div>

        <!-- Net Profit -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chart-pie text-white text-sm lg:text-lg"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-slate-500 font-medium">Laba Bersih</p>
            <h3 class="text-lg lg:text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </h3>
            <p class="text-xs text-slate-400 mt-1 hidden sm:block">Pemasukan - Pengeluaran</p>
        </div>

        <!-- Profit Percentage -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 lg:p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-amber-500 to-yellow-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-percentage text-white text-sm lg:text-lg"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-slate-500 font-medium">Perubahan Laba</p>
            <h3 class="text-lg lg:text-2xl font-bold mt-1">
                @if($profitPercentage >= 0)
                    <span class="text-green-600">+{{ number_format($profitPercentage, 1) }}%</span>
                @else
                    <span class="text-red-600">{{ number_format($profitPercentage, 1) }}%</span>
                @endif
            </h3>
            <p class="text-xs text-slate-400 mt-1 hidden sm:block">Dari periode sebelumnya</p>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Income vs Expense Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-chart-area text-white"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800">Tren Pemasukan vs Pengeluaran (7 Hari Terakhir)</h2>
            </div>
            <canvas id="financeChart" height="80"></canvas>
        </div>

        <!-- Top Products by Revenue -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-trophy text-white"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800">Top Produk Penjualan</h2>
            </div>
            <div class="space-y-4 max-h-80 overflow-y-auto">
                @forelse($incomeByProduct as $index => $item)
                <div class="p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg flex items-center justify-center text-xs font-bold shadow-md">{{ $index + 1 }}</span>
                        <p class="font-semibold text-slate-700 text-sm flex-1 truncate">{{ $item->name }}</p>
                    </div>
                    <p class="text-xs text-slate-500 mb-2">{{ number_format($item->total_quantity) }} unit • Rp {{ number_format($item->total_sales, 0, ',', '.') }}</p>
                    <div class="w-full h-2 bg-slate-200 rounded-full">
                        @php
                            $maxSales = $incomeByProduct->max('total_sales');
                            $percentage = $maxSales > 0 ? ($item->total_sales / $maxSales) * 100 : 0;
                        @endphp
                        <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-shopping-bag text-2xl text-slate-400"></i>
                    </div>
                    <p class="text-slate-500 text-sm">Tidak ada data penjualan</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-receipt text-white"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800">Transaksi Penjualan Terbaru</h2>
            </div>
            <a href="{{ route('manager.finance.income') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Produk</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Qty</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Harga Satuan</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Total</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Kasir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentSales as $sale)
                    <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $sale->created_at->format('d/m/Y') }}<br>
                                        <span class="text-xs text-gray-500">{{ $sale->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->product->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-900">{{ abs($sale->change) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">Rp {{ number_format($sale->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">Rp {{ number_format($sale->total_value, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->user->name ?? 'System' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-400">Tidak ada transaksi dalam periode ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('financeChart').getContext('2d');
        const chartData = @json($chartData);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.day),
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: chartData.map(d => d.income),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 2
                    },
                    {
                        label: 'Pengeluaran',
                        data: chartData.map(d => d.expense),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
        
        // Auto-refresh untuk update data otomatis setiap 30 detik
        setInterval(function() {
            console.log('Memeriksa update data keuangan...');
            // Reload halaman untuk mendapatkan data terbaru
            location.reload();
        }, 30000); // 30 detik
        
        console.log('Auto-refresh aktif: Data akan diperbarui setiap 30 detik');
    </script>
@endpush
