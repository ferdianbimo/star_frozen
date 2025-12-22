@extends('layouts.cashier')

@section('title','Dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Dashboard Kasir</h1>
            <p class="text-sm text-slate-600 mt-1">Ringkasan penjualan dan inventaris hari ini</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-white border border-slate-200 rounded-lg">
                <div class="flex items-center gap-2 text-slate-600">
                    <i class="fas fa-calendar-day text-sm"></i>
                    <span class="text-sm font-medium">{{ now()->format('d M Y') }}</span>
                </div>
            </div>
            <a href="{{ route('cashier.pos.index') }}" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium text-sm flex items-center gap-2">
                <i class="fas fa-shopping-cart"></i>
                Mulai Transaksi
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Daily Sales Card -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Penjualan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ 'Rp ' . number_format($dailySales ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-blue-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                @php $dp = $dailyPercentage ?? 0; @endphp
                @if($dp > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full font-medium">
                        <i class="fas fa-arrow-up text-[10px]"></i>
                        +{{ abs($dp) }}%
                    </span>
                @elseif($dp < 0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                        <i class="fas fa-arrow-down text-[10px]"></i>
                        {{ abs($dp) }}%
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-600 rounded-full font-medium">0%</span>
                @endif
            </div>
        </div>

        <!-- Monthly Sales Card -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">30 Hari Terakhir</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ 'Rp ' . number_format($monthlySales ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                @php $mp = $monthlyPercentage ?? 0; @endphp
                @if($mp > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full font-medium">
                        <i class="fas fa-arrow-up text-[10px]"></i>
                        +{{ abs($mp) }}%
                    </span>
                @elseif($mp < 0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                        <i class="fas fa-arrow-down text-[10px]"></i>
                        {{ abs($mp) }}%
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-600 rounded-full font-medium">0%</span>
                @endif
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Stok Menipis</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $lowStockCount ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box-open text-amber-600"></i>
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
                    <p class="text-sm text-slate-600 mb-1">Segera Kadaluarsa</p>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $expiringCount ?? 0 }}</h3>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-red-600"></i>
                </div>
            </div>
            <div class="flex items-center gap-1 text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full font-medium">
                    <i class="fas fa-clock text-[10px]"></i>
                    Dalam 7 hari
                </span>
            </div>
        </div>
    </div>

    <!-- Sales Trend Chart -->
    <div class="bg-white rounded-lg p-5 border border-slate-200 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-slate-800">Tren Penjualan</h3>
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
        <!-- Expiring Soon Table -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h4 class="text-base font-semibold text-slate-800">Segera Kadaluarsa</h4>
                <p class="text-xs text-slate-500 mt-1">Produk yang akan kadaluarsa dalam 7 hari</p>
            </div>
            <div class="overflow-x-auto">
                @if(isset($expiringProducts) && $expiringProducts->count())
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600">Produk</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600">Kadaluarsa</th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-slate-600">Sisa Hari</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($expiringProducts as $p)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-sm text-slate-700">{{ $p->name }}</td>
                                    <td class="py-3 px-4 text-sm text-slate-600">{{ optional($p->expiry_date)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ ($p->remaining_days ?? 0) <= 3 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $p->remaining_days ?? '-' }} hari
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="py-8 text-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                        </div>
                        <p class="text-sm text-slate-500">Tidak ada produk yang akan kadaluarsa</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Low Stock Table -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-200">
                <h4 class="text-base font-semibold text-slate-800">Stok Menipis</h4>
                <p class="text-xs text-slate-500 mt-1">Produk yang perlu segera di-restock</p>
            </div>
            <div class="overflow-x-auto">
                @if(isset($stockAlmostOut) && $stockAlmostOut->count())
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600">Produk</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-slate-600">Stok</th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-slate-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($stockAlmostOut as $p)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-3 px-4 text-sm text-slate-700">{{ $p->name }}</td>
                                    <td class="py-3 px-4 text-sm text-slate-600">{{ $p->stock }}</td>
                                    <td class="py-3 px-4 text-right">
                                        @if($p->stock == 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle text-[10px]"></i> Habis
                                            </span>
                                        @elseif($p->stock <= 5)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                                <i class="fas fa-exclamation-circle text-[10px]"></i> Kritis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                <i class="fas fa-exclamation-triangle text-[10px]"></i> Rendah
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="py-8 text-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-circle text-xl text-emerald-500"></i>
                        </div>
                        <p class="text-slate-500 text-sm">Semua stok dalam kondisi baik</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function changePeriod(days) {
        window.location.href = '{{ route('cashier.dashboard') }}?period=' + days;
    }

    (function(){
        const salesTrend = @json($salesTrend ?? []);
        const labels = salesTrend.map(s => s.day);
        const data = salesTrend.map(s => Number(s.sales) || 0);

        const canvas = document.getElementById('salesChartLarge');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, 'rgba(59,130,246,0.25)');
        gradient.addColorStop(0.5, 'rgba(99,102,241,0.10)');
        gradient.addColorStop(1, 'rgba(99,102,241,0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data,
                    borderColor: 'rgba(59,130,246,1)',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(59,130,246,1)',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: 'rgba(59,130,246,1)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    borderWidth: 3
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
                        backgroundColor: 'rgba(30,41,59,0.95)',
                        titleColor: '#fff',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(59,130,246,0.3)',
                        borderWidth: 1,
                        cornerRadius: 12,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11, weight: '500' }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(226,232,240,0.8)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value/1000000).toFixed(1) + ' jt';
                                if (value >= 1000) return (value/1000).toFixed(0) + ' rb';
                                return value;
                            },
                            color: '#64748b',
                            font: { size: 11, weight: '500' }
                        }
                    }
                }
            }
        });
    })();

    // Auto-refresh setiap 30 detik untuk update data otomatis
    setInterval(function() {
        console.log('Auto-refreshing cashier dashboard...');
        location.reload();
    }, 30000);
</script>
<style>
    /* Custom scrollbar styling */
    .scrollbar-custom::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-custom::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .scrollbar-custom::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #3b82f6 0%, #6366f1 100%);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .scrollbar-custom::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #2563eb 0%, #4f46e5 100%);
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.5);
    }

    /* Firefox scrollbar */
    .scrollbar-custom {
        scrollbar-width: thin;
        scrollbar-color: #3b82f6 #f1f5f9;
    }
</style>
@endpush

