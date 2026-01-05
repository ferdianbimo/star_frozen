@extends('layouts.cashier')

@section('title','Dashboard')

@section('content')
    <!-- Dashboard Header -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-xl mb-8 overflow-hidden">
        <div class="relative px-6 py-8">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-transparent"></div>
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-cash-register text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-white">Cashier Dashboard</h1>
                        <p class="text-blue-100 mt-1">Ringkasan penjualan dan inventaris hari ini</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                        <div class="flex items-center gap-2 text-white">
                            <i class="fas fa-calendar-day text-blue-200"></i>
                            <span class="text-sm font-medium">{{ now()->format('d M Y') }}</span>
                        </div>
                    </div>
                    <a href="{{ route('cashier.pos.index') }}" class="bg-white text-blue-600 hover:bg-blue-50 px-5 py-2.5 rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-shopping-cart"></i>
                        Mulai Transaksi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Daily Sales Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-coins text-white text-lg"></i>
                </div>
                @php $dp = $dailyPercentage ?? 0; @endphp
                @if($dp > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <i class="fas fa-arrow-up text-[10px]"></i> {{ abs($dp) }}%
                    </span>
                @elseif($dp < 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                        <i class="fas fa-arrow-down text-[10px]"></i> {{ abs($dp) }}%
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">0%</span>
                @endif
            </div>
            <p class="text-sm text-slate-500 font-medium">Penjualan Hari Ini</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ 'Rp ' . number_format($dailySales ?? 0, 0, ',', '.') }}</h3>
        </div>

        <!-- Monthly Sales Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chart-line text-white text-lg"></i>
                </div>
                @php $mp = $monthlyPercentage ?? 0; @endphp
                @if($mp > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <i class="fas fa-arrow-up text-[10px]"></i> {{ abs($mp) }}%
                    </span>
                @elseif($mp < 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                        <i class="fas fa-arrow-down text-[10px]"></i> {{ abs($mp) }}%
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">0%</span>
                @endif
            </div>
            <p class="text-sm text-slate-500 font-medium">30 Hari Terakhir</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ 'Rp ' . number_format($monthlySales ?? 0, 0, ',', '.') }}</h3>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-box-open text-white text-lg"></i>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    <i class="fas fa-exclamation-triangle text-[10px]"></i> Alert
                </span>
            </div>
            <p class="text-sm text-slate-500 font-medium">Stok Menipis</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $lowStockCount ?? 0 }} <span class="text-sm font-normal text-slate-500">produk</span></h3>
        </div>

        <!-- Expiring Soon Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-hourglass-half text-white text-lg"></i>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                    <i class="fas fa-clock text-[10px]"></i> 7 hari
                </span>
            </div>
            <p class="text-sm text-slate-500 font-medium">Segera Kadaluarsa</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $expiringCount ?? 0 }} <span class="text-sm font-normal text-slate-500">produk</span></h3>
        </div>
    </div>

    <!-- Sales Chart Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-chart-area text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Tren Penjualan</h3>
                    <p class="text-sm text-slate-500" id="periodLabel">{{ $period ?? 7 }} hari terakhir</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <select id="periodSelect" onchange="changePeriod(this.value)" class="custom-select appearance-none bg-white border-2 border-slate-200 text-slate-700 rounded-xl pl-4 pr-10 py-2.5 text-sm font-medium focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 cursor-pointer hover:border-slate-300">
                        <option value="7" {{ ($period ?? 7) == 7 ? 'selected' : '' }}>📅 7 Hari Terakhir</option>
                        <option value="30" {{ ($period ?? 7) == 30 ? 'selected' : '' }}>📅 30 Hari Terakhir</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>
        </div>
        <div style="min-height: 320px; position: relative;">
            <canvas id="salesChartLarge"></canvas>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Expiring Soon Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-red-50 to-orange-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-calendar-times text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-800">Segera Kadaluarsa</h4>
                        <p class="text-xs text-slate-500">Produk yang akan kadaluarsa dalam 7 hari</p>
                    </div>
                </div>
            </div>
            <div class="p-4 max-h-64 overflow-y-auto scrollbar-custom">
                @if(isset($expiringProducts) && $expiringProducts->count())
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="pb-3 px-2">Produk</th>
                                <th class="pb-3 px-2">Batch</th>
                                <th class="pb-3 px-2">Kadaluarsa</th>
                                <th class="pb-3 px-2 text-right">Sisa Hari</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($expiringProducts as $p)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-2">
                                        <span class="font-medium text-slate-700">{{ $p->name }}</span>
                                        @if(isset($p->batch_quantity))
                                            <span class="block text-xs text-slate-400 mt-0.5">Stok: {{ $p->batch_quantity }} pcs</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2">
                                        @if(isset($p->batch_code))
                                            <span class="text-xs font-mono text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ $p->batch_code }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 text-slate-500 text-sm">{{ optional($p->expiry_date)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-2 text-right">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ ($p->remaining_days ?? 0) <= 3 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                            <i class="fas fa-clock text-[10px]"></i>
                                            {{ $p->remaining_days ?? '-' }} hari
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-check-circle text-2xl text-green-500"></i>
                        </div>
                        <p class="text-slate-500 text-sm">Tidak ada produk yang akan kadaluarsa</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Low Stock Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-yellow-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-md">
                        <i class="fas fa-boxes text-white"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-slate-800">Stok Menipis</h4>
                        <p class="text-xs text-slate-500">Produk yang perlu segera di-restock</p>
                    </div>
                </div>
            </div>
            <div class="p-4 max-h-96 overflow-y-auto scrollbar-custom">
                @if(isset($stockAlmostOut) && $stockAlmostOut->count())
                    <div class="space-y-4">
                        @foreach($stockAlmostOut as $p)
                            <div class="border border-slate-200 rounded-xl p-4 hover:border-amber-300 hover:bg-amber-50/30 transition-all {{ $p->stock == 0 ? 'bg-red-50 border-red-200' : '' }}">
                                <!-- Product Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex-1">
                                        <h5 class="font-bold {{ $p->stock == 0 ? 'text-red-700' : 'text-slate-800' }}">{{ $p->name }}</h5>
                                        @if($p->batch_count > 0)
                                            <span class="text-xs text-slate-500 mt-1 block">{{ $p->batch_count }} batch tersedia</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($p->stock == 0)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle"></i> Habis
                                            </span>
                                        @elseif($p->stock <= 5)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                                                <i class="fas fa-exclamation-circle"></i> Kritis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                                <i class="fas fa-exclamation-triangle"></i> Rendah
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Batch Details -->
                                @if($p->active_batches && $p->active_batches->count() > 0)
                                    <div class="bg-white rounded-lg p-3 border border-slate-100">
                                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1">
                                            <i class="fas fa-layer-group"></i>
                                            Detail Batch
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($p->active_batches as $batch)
                                                <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-b-0">
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs font-mono font-semibold bg-slate-100 text-slate-700 px-2 py-1 rounded">
                                                            {{ $batch->batch_code }}
                                                        </span>
                                                        <span class="text-sm text-slate-600">
                                                            {{ $batch->quantity }} pcs
                                                        </span>
                                                    </div>
                                                    <div class="text-right">
                                                        @if($batch->expiration_date)
                                                            @php
                                                                $expiryDate = \Carbon\Carbon::parse($batch->expiration_date);
                                                                $daysLeft = now()->diffInDays($expiryDate, false);
                                                            @endphp
                                                            <div class="text-xs text-slate-500">
                                                                Exp: {{ $expiryDate->format('d/m/Y') }}
                                                            </div>
                                                            @if($daysLeft <= 7 && $daysLeft > 0)
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 mt-1">
                                                                    <i class="fas fa-clock text-[9px]"></i>
                                                                    {{ $daysLeft }} hari
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="text-xs text-slate-400">No expiry</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="text-xs text-slate-400 italic">Tidak ada batch aktif</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-check-circle text-2xl text-green-500"></i>
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

