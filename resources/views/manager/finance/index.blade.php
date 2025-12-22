@extends('layouts.manager')

@section('title','Laporan Keuangan')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Laporan Keuangan</h1>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg p-4 border border-slate-200 mb-4">
        <form method="GET" action="{{ route('manager.finance.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700 mb-2">Periode Laporan</label>
                <select name="period" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
                    <option value="daily">Harian</option>
                    <option value="weekly">Mingguan</option>
                    <option value="monthly">Bulanan</option>
                    <option value="yearly">Tahunan</option>
                </select>
            </div>
            
            <div class="flex-1">
                <input type="date" name="start_date" value="{{ $startDate ?? date('Y-m-01') }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
            </div>
            
            <div class="flex-1">
                <input type="date" name="end_date" value="{{ $endDate ?? date('Y-m-d') }}"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 bg-white">
            </div>
            
            <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium whitespace-nowrap">
                Terapkan Filter
            </button>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Pemasukan -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Total Pemasukan</p>
                    <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalIncome ?? 125400000, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-trend-up text-emerald-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-medium">
                    <i class="fas fa-arrow-up text-[10px] mr-1"></i> +3.5%
                </span>
                <span class="text-xs text-slate-500">vs bulan lalu</span>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Total Pengeluaran</p>
                    <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalExpenses ?? 87200000, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-trend-down text-red-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">
                    <i class="fas fa-arrow-down text-[10px] mr-1"></i> -1.5%
                </span>
                <span class="text-xs text-slate-500">vs bulan lalu</span>
            </div>
        </div>

        <!-- Laba Bersih -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Laba Bersih</p>
                    <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($netProfit ?? 38200000, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                    <i class="fas fa-arrow-up text-[10px] mr-1"></i> +7.2%
                </span>
                <span class="text-xs text-slate-500">vs bulan lalu</span>
            </div>
        </div>

        <!-- Perubahan Bulan Lalu -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Perubahan Bulan Lalu</p>
                    <h3 class="text-2xl font-bold text-slate-800">+18.4%</h3>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">vs bulan lalu</span>
            </div>
        </div>
    </div>

    <!-- Chart & Export Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-slate-800">Trend Pemasukan vs Pengeluaran</h3>
                <div class="flex gap-2">
                    <button class="px-3 py-1 bg-blue-500 text-white rounded text-xs font-medium">Harian</button>
                    <button class="px-3 py-1 text-slate-600 hover:bg-slate-100 rounded text-xs">Bulanan</button>
                    <button class="px-3 py-1 text-slate-600 hover:bg-slate-100 rounded text-xs">Tahunan</button>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <!-- Export Laporan -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <h3 class="text-base font-semibold text-slate-800 mb-4">Export Laporan</h3>
            <div class="space-y-3">
                <a href="{{ route('manager.finance.income') }}?export=pdf" 
                    class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-pdf text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">PDF Report</p>
                            <p class="text-xs text-slate-500">Format: PDF</p>
                        </div>
                    </div>
                    <i class="fas fa-download text-slate-400"></i>
                </a>

                <a href="{{ route('manager.finance.income') }}?export=excel" 
                    class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-excel text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-800">Excel File</p>
                            <p class="text-xs text-slate-500">Format: XLSX</p>
                        </div>
                    </div>
                    <i class="fas fa-download text-slate-400"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs & Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <!-- Tabs -->
        <div class="flex border-b border-slate-200">
            <button onclick="switchTab('pemasukan')" id="tab-pemasukan" 
                class="px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-blue-50">
                Pemasukan
            </button>
            <button onclick="switchTab('pengeluaran')" id="tab-pengeluaran" 
                class="px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50">
                Pengeluaran
            </button>
        </div>

        <!-- Table Pemasukan -->
        <div id="content-pemasukan">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Kategori</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($incomes ?? [] as $income)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $income->date ?? '15 Jan 2024' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $income->description ?? 'Penjualan Produk A' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                    {{ $income->category ?? 'Penjualan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-semibold text-emerald-600">
                                Rp {{ number_format($income->amount ?? 4500000, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                Tidak ada data pemasukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Pengeluaran -->
        <div id="content-pengeluaran" class="hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600">Kategori</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($expenses ?? [] as $expense)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $expense->date ?? '15 Jan 2024' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $expense->description ?? 'Pembelian Bahan' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    {{ $expense->category ?? 'Operasional' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-semibold text-red-600">
                                Rp {{ number_format($expense->amount ?? 2500000, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                Tidak ada data pengeluaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tab switching
function switchTab(tab) {
    // Reset all tabs
    document.getElementById('tab-pemasukan').className = 'px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50';
    document.getElementById('tab-pengeluaran').className = 'px-6 py-3 text-sm font-medium text-slate-600 hover:text-slate-800 hover:bg-slate-50';
    
    // Hide all content
    document.getElementById('content-pemasukan').classList.add('hidden');
    document.getElementById('content-pengeluaran').classList.add('hidden');
    
    // Show selected
    if (tab === 'pemasukan') {
        document.getElementById('tab-pemasukan').className = 'px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-blue-50';
        document.getElementById('content-pemasukan').classList.remove('hidden');
    } else {
        document.getElementById('tab-pengeluaran').className = 'px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600 bg-blue-50';
        document.getElementById('content-pengeluaran').classList.remove('hidden');
    }
}

// Chart
const ctx = document.getElementById('financeChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan 1', 'Jan 5', 'Jan 10'],
        datasets: [{
            label: 'Pemasukan',
            data: [15000000, 18000000, 22000000],
            backgroundColor: 'rgb(16, 185, 129)',
            borderRadius: 6,
        }, {
            label: 'Pengeluaran',
            data: [12000000, 14000000, 16000000],
            backgroundColor: 'rgb(239, 68, 68)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000) + 'M';
                    }
                }
            }
        }
    }
});
</script>
@endsection
