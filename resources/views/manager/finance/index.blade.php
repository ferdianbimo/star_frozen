@extends('layouts.manager')

@section('title','Laporan Keuangan')

@section('content')
    <main class="flex-1 overflow-auto bg-gray-50">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-8 py-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Laporan Keuangan</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('manager.dashboard') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition">
                            Kembali ke Dashboard
                        </a>
                        <a href="{{ route('manager.finance.income') }}" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                            History Pemasukan
                        </a>
                        <a href="{{ route('manager.finance.expenses') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Kelola Pengeluaran
                        </a>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-8">
                <!-- Info Badge - Data dari Stock Logs -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Data Otomatis dari Stok Keluar</p>
                            <p class="text-xs text-blue-600">Setiap transaksi POS dan stok keluar inventory akan langsung terupdate dalam 30 detik</p>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Period -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('manager.finance.index') }}" class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Periode Laporan</label>
                            <div class="flex gap-3">
                                <input type="date" name="start_date" value="{{ $startDate }}" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <span class="text-gray-500 self-center">-</span>
                                <input type="date" name="end_date" value="{{ $endDate }}" 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Terapkan Filter
                        </button>
                    </form>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Total Income -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Pemasukan</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </h3>
                        <div class="flex items-center">
                            @if($incomePercentage >= 0)
                                <span class="text-green-600 text-sm font-medium">↑ {{ number_format($incomePercentage, 1) }}%</span>
                            @else
                                <span class="text-red-600 text-sm font-medium">↓ {{ number_format(abs($incomePercentage), 1) }}%</span>
                            @endif
                            <span class="text-gray-400 text-sm ml-2">vs periode lalu</span>
                        </div>
                    </div>

                    <!-- Total Expenses -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                        <p class="text-gray-600 text-sm font-medium mb-2">Total Pengeluaran</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                        </h3>
                        <div class="flex items-center">
                            @if($expensePercentage >= 0)
                                <span class="text-red-600 text-sm font-medium">↑ {{ number_format($expensePercentage, 1) }}%</span>
                            @else
                                <span class="text-green-600 text-sm font-medium">↓ {{ number_format(abs($expensePercentage), 1) }}%</span>
                            @endif
                            <span class="text-gray-400 text-sm ml-2">vs periode lalu</span>
                        </div>
                    </div>

                    <!-- Net Profit -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                        <p class="text-gray-600 text-sm font-medium mb-2">Laba Bersih</p>
                        <h3 class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2">
                            Rp {{ number_format($netProfit, 0, ',', '.') }}
                        </h3>
                        <p class="text-gray-500 text-sm">Pemasukan - Pengeluaran</p>
                    </div>

                    <!-- Profit Percentage -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                        <p class="text-gray-600 text-sm font-medium mb-2">Perubahan Laba</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            @if($profitPercentage >= 0)
                                <span class="text-green-600">+{{ number_format($profitPercentage, 1) }}%</span>
                            @else
                                <span class="text-red-600">{{ number_format($profitPercentage, 1) }}%</span>
                            @endif
                        </h3>
                        <p class="text-gray-500 text-sm">Dari periode sebelumnya</p>
                    </div>
                </div>

                <!-- Charts and Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Income vs Expense Chart -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Tren Pemasukan vs Pengeluaran (7 Hari Terakhir)</h2>
                        <canvas id="financeChart" height="80"></canvas>
                    </div>

                    <!-- Top Products by Revenue -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Top Produk Penjualan</h2>
                        <div class="space-y-4">
                            @forelse($incomeByProduct as $index => $item)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                                    <p class="font-medium text-gray-900 text-sm flex-1">{{ $item->name }}</p>
                                </div>
                                <p class="text-xs text-gray-500">{{ number_format($item->total_quantity) }} unit • Rp {{ number_format($item->total_sales, 0, ',', '.') }}</p>
                                <div class="w-full h-2 bg-gray-200 rounded-full mt-2">
                                    @php
                                        $maxSales = $incomeByProduct->max('total_sales');
                                        $percentage = $maxSales > 0 ? ($item->total_sales / $maxSales) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-gray-400 py-8">Tidak ada data penjualan</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Sales Table -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Transaksi Penjualan Terbaru</h2>
                        <a href="{{ route('manager.finance.income') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Lihat Semua →
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Produk</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Harga Satuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Kasir</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentSales as $sale)
                                <tr class="hover:bg-gray-50">
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
