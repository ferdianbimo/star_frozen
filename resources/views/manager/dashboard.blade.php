@extends('layouts.manager')

@section('title','Dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Dashboard</h1>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Daily Sales Card -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Penjualan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($dailySales ?? 2450000, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cash-register text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full font-medium">
                    <i class="fas fa-arrow-up text-[10px]"></i>
                    +{{ number_format(abs($dailyPercentage ?? 12.5), 1) }}%
                </span>
            </div>
        </div>

        <!-- Monthly Sales Card -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Penjualan Bulan Ini</p>
                    <h3 class="text-2xl font-bold text-slate-800">Rp {{ number_format($monthlySales ?? 68750000, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full font-medium">
                    <i class="fas fa-arrow-up text-[10px]"></i>
                    +{{ number_format(abs($monthlyPercentage ?? 8.2), 1) }}%
                </span>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Stok Rendah</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $lowStockCount ?? 15 }}</h3>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-amber-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-700 rounded-full font-medium">
                    <i class="fas fa-exclamation-triangle text-[10px]"></i>
                    Perlu restok
                </span>
            </div>
        </div>

        <!-- Expiring Soon Card -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Akan Kadaluarsa</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $expiringCount ?? 8 }}</h3>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-red-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    Dalam 7 hari
                </span>
            </div>
        </div>
    </div>

    <!-- Sales Trend Chart -->
    <div class="bg-white rounded-lg p-5 border border-slate-200 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-slate-800">Trend Penjualan</h2>
            <div class="flex items-center gap-2">
                <button onclick="changePeriod(7)" class="px-3 py-1.5 text-xs font-medium rounded-lg {{ ($period ?? 7) == 7 ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    7 Hari
                </button>
                <button onclick="changePeriod(30)" class="px-3 py-1.5 text-xs font-medium rounded-lg {{ ($period ?? 7) == 30 ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    30 Hari
                </button>
            </div>
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="salesChartLarge"></canvas>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Expired Soon Table -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-800">Produk Akan Kadaluarsa</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600">Produk</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600">Expired</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-slate-600">Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if(isset($expiringProducts) && $expiringProducts->count())
                            @foreach($expiringProducts as $p)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-sm text-slate-700">{{ $p->name }}</td>
                                    <td class="py-3 px-4 text-sm text-slate-600">{{ optional($p->expiry_date)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ ($p->remaining_days ?? 0) <= 2 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $p->remaining_days ?? '-' }} hari
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="py-8 text-center text-sm text-slate-500">
                                    Tidak ada produk yang akan kadaluarsa
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Table -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-800">Stok Hampir Habis</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600">Produk</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-slate-600">Stok</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-slate-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if(isset($stockAlmostOut) && $stockAlmostOut->count())
                            @foreach($stockAlmostOut as $p)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-sm text-slate-700">{{ $p->name }}</td>
                                    <td class="py-3 px-4 text-sm text-center font-medium text-slate-800">{{ $p->stock }} {{ $p->unit ?? 'pcs' }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $p->stock <= 2 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ $p->stock <= 5 ? 'Kritis' : 'Rendah' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="py-8 text-center text-sm text-slate-500">
                                    Semua stok dalam kondisi baik
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function changePeriod(days) {
        window.location.href = '{{ route('manager.dashboard') }}?period=' + days;
    }

    (function(){
        const salesTrend = @json($salesTrend ?? []);
        const labels = salesTrend.map(s => s.day);
        const data = salesTrend.map(s => Number(s.sales) || 0);

        const canvas = document.getElementById('salesChartLarge');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(147, 197, 253, 0.5)');
        gradient.addColorStop(1, 'rgba(147, 197, 253, 0.05)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(59, 130, 246, 1)',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1e293b',
                        bodyColor: '#475569',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        displayColors: false,
                        titleFont: {
                            size: 13,
                            weight: '600'
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: true,
                            color: 'rgba(226, 232, 240, 0.5)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(226, 232, 240, 0.5)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value/1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return (value/1000).toFixed(0) + ' rb';
                                return value;
                            },
                            color: '#94a3b8',
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
