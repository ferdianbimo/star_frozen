@extends('layouts.manager')

@section('title','Laporan Keuangan')

@section('content')
<div class="spacing-page">
    <x-page-header
        icon="fa-wallet"
        title="Laporan Keuangan"
        subtitle="{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}"
        iconColor="success">
        <x-slot name="actions">
            <a href="{{ route('manager.dashboard') }}" class="btn-outline btn-md">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline">Kembali</span>
            </a>
            <a href="{{ route('manager.finance.income') }}" class="btn-success btn-md">
                <i class="fas fa-chart-line"></i>
                <span class="hidden sm:inline">History</span> Pemasukan
            </a>
            <a href="{{ route('manager.finance.expenses') }}" class="btn-primary btn-md">
                <i class="fas fa-receipt"></i>
                <span class="hidden sm:inline">Kelola</span> Pengeluaran
            </a>
        </x-slot>
    </x-page-header>

    <!-- Info Badge - Data dari Stock Logs -->
    <div class="badge-info-lg spacing-section flex items-center gap-4">
        <div class="icon-container-md icon-info">
            <i class="fas fa-info-circle"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-blue-800">Data Otomatis dari Stok Keluar</p>
            <p class="text-xs text-blue-600">Setiap transaksi POS dan stok keluar inventory akan langsung terupdate dalam 30 detik</p>
        </div>
    </div>

    <!-- Filter Period -->
    <div class="card-section spacing-section">
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
            <button type="submit" class="btn-primary btn-md w-full lg:w-auto">
                <i class="fas fa-filter"></i>
                Terapkan Filter
            </button>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid-stats-4 spacing-section">
        <x-stat-card
            icon="fa-arrow-down"
            iconColor="success"
            label="Total Pemasukan"
            :value="'Rp ' . number_format($totalIncome, 0, ',', '.')"
            :trend="$incomePercentage >= 0 ? 'up' : 'down'"
            :percentage="number_format(abs($incomePercentage), 1)">
            <x-slot name="footer">
                <p class="text-xs text-slate-400">vs periode lalu</p>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            icon="fa-arrow-up"
            iconColor="danger"
            label="Total Pengeluaran"
            :value="'Rp ' . number_format($totalExpenses, 0, ',', '.')"
            :trend="$expensePercentage >= 0 ? 'up' : 'down'"
            :percentage="number_format(abs($expensePercentage), 1)">
            <x-slot name="footer">
                <p class="text-xs text-slate-400">vs periode lalu</p>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            icon="fa-chart-pie"
            iconColor="primary"
            label="Laba Bersih"
            :value="'Rp ' . number_format($netProfit, 0, ',', '.')">
            <x-slot name="footer">
                <p class="text-xs text-slate-400">Pemasukan - Pengeluaran</p>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            icon="fa-percentage"
            iconColor="warning"
            label="Perubahan Laba"
            :value="($profitPercentage >= 0 ? '+' : '') . number_format($profitPercentage, 1) . '%'">
            <x-slot name="footer">
                <p class="text-xs text-slate-400">Dari periode sebelumnya</p>
            </x-slot>
        </x-stat-card>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 spacing-section">
        <!-- Income vs Expense Chart -->
        <div class="lg:col-span-2">
            <x-section-card icon="fa-chart-area" iconColor="primary" title="Tren Pemasukan vs Pengeluaran (7 Hari Terakhir)">
                <canvas id="financeChart" height="80"></canvas>
            </x-section-card>
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
    <x-section-card icon="fa-receipt" iconColor="primary" title="Transaksi Penjualan Terbaru">
        <x-slot name="actions">
            <a href="{{ route('manager.finance.income') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1 transition-default">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </x-slot>
        <div class="table-container">
            <table class="min-w-full">
                <thead class="table-header">
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
                    <tr class="hover:bg-slate-50 transition-default">
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
    </x-section-card>
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
</div>
