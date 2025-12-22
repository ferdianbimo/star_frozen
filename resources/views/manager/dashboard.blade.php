@extends('layouts.manager')

@section('title','Dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="text-xl font-bold text-slate-800">Dashboard</h1>
    </div>

    <!-- Main Grid: Stats + Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <!-- Left Column: Stats Cards -->
        <div class="space-y-3">
            <!-- Daily Sales Card -->
            <div class="bg-white rounded-lg p-4 border border-slate-200 border-l-4 border-l-blue-500">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Daily Sales</p>
                        <h3 class="text-xl font-bold text-slate-900">Rp {{ number_format($dailySales ?? 2450000, 0, ',', '.') }}</h3>
                    </div>
                    <button class="text-blue-500 hover:text-blue-600">
                        <i class="fas fa-download text-sm"></i>
                    </button>
                </div>
                <div class="flex items-center gap-1 text-green-600 text-xs">
                    <i class="fas fa-arrow-up"></i>
                    <span class="font-medium">+{{ number_format(abs($dailyPercentage ?? 12.5), 1) }}% dari kemarin</span>
                </div>
            </div>

            <!-- Monthly Sales Card -->
            <div class="bg-white rounded-lg p-4 border border-slate-200 border-l-4 border-l-blue-500">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Monthly Sales</p>
                        <h3 class="text-xl font-bold text-slate-900">Rp {{ number_format($monthlySales ?? 68750000, 0, ',', '.') }}</h3>
                    </div>
                    <button class="text-blue-500 hover:text-blue-600">
                        <i class="fas fa-download text-sm"></i>
                    </button>
                </div>
                <div class="flex items-center gap-1 text-green-600 text-xs">
                    <i class="fas fa-arrow-up"></i>
                    <span class="font-medium">+{{ number_format(abs($monthlyPercentage ?? 8.2), 1) }}% dari bulan lalu</span>
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200 border-l-4 border-l-yellow-500">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-xs text-yellow-800 mb-1">Low Stock</p>
                        <h3 class="text-2xl font-bold text-yellow-900">{{ $lowStockCount ?? 15 }}</h3>
                    </div>
                    <div class="text-2xl">📦</div>
                </div>
                <p class="text-xs text-yellow-700 flex items-center gap-1">
                    <i class="fas fa-exclamation-triangle text-[10px]"></i>
                    Perlu restok
                </p>
            </div>

            <!-- Expiring Soon Card -->
            <div class="bg-red-50 rounded-lg p-4 border border-red-200 border-l-4 border-l-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-red-800 mb-1">Expiring Soon</p>
                        <h3 class="text-2xl font-bold text-red-900">{{ $expiringCount ?? 8 }}</h3>
                        <p class="text-xs text-red-700 mt-1">
                            <i class="fas fa-clock text-[10px]"></i> Dalam 7 hari
                        </p>
                    </div>
                    <div class="text-2xl">⏳</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sales Trend Chart -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg p-4 border border-slate-200">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-slate-800">Sales Trend</h2>
                    <div class="relative">
                        <select id="periodSelect" onchange="changePeriod(this.value)" class="appearance-none bg-white border border-slate-300 text-slate-700 rounded-md pl-3 pr-8 py-1 text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 cursor-pointer hover:border-slate-400">
                            <option value="7" {{ ($period ?? 7) == 7 ? 'selected' : '' }}>Last 7 days</option>
                            <option value="30" {{ ($period ?? 7) == 30 ? 'selected' : '' }}>Last 30 days</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                        </div>
                    </div>
                </div>
                <div style="height: 320px; position: relative;">
                    <canvas id="salesChartLarge"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
        <!-- Expired Soon Table -->
        <div class="bg-white rounded-lg p-4 border border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Expired Soon</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left py-2 px-1 text-xs font-semibold text-slate-600">Produk</th>
                            <th class="text-left py-2 px-1 text-xs font-semibold text-slate-600">Expired</th>
                            <th class="text-right py-2 px-1 text-xs font-semibold text-slate-600">Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($expiringProducts) && $expiringProducts->count())
                            @foreach($expiringProducts as $p)
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-2 px-1 text-xs text-slate-700">{{ $p->name }}</td>
                                    <td class="py-2 px-1 text-xs text-slate-600">{{ optional($p->expiry_date)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-1 text-right">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($p->remaining_days ?? 0) <= 2 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $p->remaining_days ?? '-' }}hari
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="py-6 text-center text-xs text-slate-500">
                                    Tidak ada produk yang akan kadaluarsa
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Table -->
        <div class="bg-white rounded-lg p-4 border border-slate-200">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Stok Hampir Habis</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left py-2 px-1 text-xs font-semibold text-slate-600">Produk</th>
                            <th class="text-center py-2 px-1 text-xs font-semibold text-slate-600">Stok</th>
                            <th class="text-right py-2 px-1 text-xs font-semibold text-slate-600">Sisa Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($stockAlmostOut) && $stockAlmostOut->count())
                            @foreach($stockAlmostOut as $p)
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-2 px-1 text-xs text-slate-700">{{ $p->name }}</td>
                                    <td class="py-2 px-1 text-xs text-center font-medium text-slate-800">{{ $p->stock }} pcs</td>
                                    <td class="py-2 px-1 text-right">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium {{ $p->stock <= 2 ? 'bg-red-100 text-red-700' : 'bg-pink-100 text-pink-700' }}">
                                            {{ $p->stock <= 5 ? 'Kritis' : 'Rendah' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="py-6 text-center text-xs text-slate-500">
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
