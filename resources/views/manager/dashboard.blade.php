@extends('layouts.manager')

@section('title','Dashboard')

@section('content')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow p-4 md:p-5 flex items-start h-24 md:h-24">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Daily Sales</p>
                                <h3 class="text-lg md:text-xl font-bold mt-1 truncate">{{ 'Rp ' . number_format($dailySales ?? 0, 0, ',', '.') }}</h3>
                                    @php
                                        $dp = $dailyPercentage ?? 0;
                                    @endphp
                                    <p class="text-xs mt-1">
                                        @if($dp > 0)
                                            <span class="text-green-600">&uarr; {{ abs($dp) }}%</span>
                                        @elseif($dp < 0)
                                            <span class="text-red-600">&darr; {{ abs($dp) }}%</span>
                                        @else
                                            <span class="text-gray-500">0%</span>
                                        @endif
                                    </p>
                            </div>
                            <div class="ml-2 md:ml-4 bg-blue-50 rounded p-2 flex-shrink-0">
                                <i class="fas fa-download text-blue-500 text-sm md:text-base"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4 md:p-5 flex items-start h-24 md:h-24">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Monthly Sales</p>
                                <h3 class="text-lg md:text-xl font-bold mt-1 truncate">{{ 'Rp ' . number_format($monthlySales ?? 0, 0, ',', '.') }}</h3>
                                <p class="text-xs text-green-500 mt-1">@if(isset($monthlyPercentage)) {{ ($monthlyPercentage >= 0 ? '↑ ' : '↓ ') . abs($monthlyPercentage) . '%' }} @endif</p>
                            </div>
                            <div class="ml-2 md:ml-4 bg-yellow-50 rounded p-2 flex-shrink-0">
                                <i class="fas fa-chart-line text-yellow-500 text-sm md:text-base"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4 md:p-5 flex items-start h-24 md:h-24">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Low Stock</p>
                                <h3 class="text-lg md:text-xl font-bold mt-1">{{ $lowStockCount ?? 0 }}</h3>
                                <p class="text-xs text-yellow-500 mt-1">Perlu restok</p>
                            </div>
                            <div class="ml-2 md:ml-4 bg-yellow-100 rounded p-2 flex-shrink-0">
                                <i class="fas fa-box-open text-yellow-600 text-sm md:text-base"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4 md:p-5 flex items-start h-24 md:h-24">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Expiring Soon</p>
                                <h3 class="text-lg md:text-xl font-bold mt-1">{{ $expiringCount ?? 0 }}</h3>
                                <p class="text-xs text-red-500 mt-1">Dalam 7 hari</p>
                            </div>
                            <div class="ml-2 md:ml-4 bg-red-50 rounded p-2 flex-shrink-0">
                                <i class="fas fa-hourglass-half text-red-500 text-sm md:text-base"></i>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 lg:col-span-3">
                        <div class="bg-white rounded-lg shadow p-4 md:p-6 h-full flex flex-col">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Sales Trend</h3>
                                    <p class="text-sm text-gray-500" id="periodLabel">Last {{ $period ?? 7 }} days</p>
                                </div>
                                <div>
                                    <select id="periodSelect" class="border rounded px-3 py-1 text-sm w-full md:w-auto" onchange="changePeriod(this.value)">
                                        <option value="7" {{ ($period ?? 7) == 7 ? 'selected' : '' }}>Last 7 days</option>
                                        <option value="30" {{ ($period ?? 7) == 30 ? 'selected' : '' }}>Last 30 days</option>
                                    </select>
                                </div>
                            </div>
                            <div style="flex: 1; min-height: 300px; position: relative;">
                                <canvas id="salesChartLarge"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h4 class="text-base font-medium mb-3">Expired Soon</h4>
                        <div class="max-h-48 overflow-y-auto scrollbar-custom">
                            @if(isset($expiringProducts) && $expiringProducts->count())
                                <table class="w-full text-xs md:text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-500">
                                            <th class="truncate">Produk</th>
                                            <th class="truncate">Expired</th>
                                            <th class="text-right truncate">Sisa Hari</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expiringProducts as $p)
                                            <tr class="border-t">
                                                <td class="py-2">{{ $p->name }}</td>
                                                <td class="py-2 text-gray-500">{{ optional($p->expiry_date)->format('d/m/Y') }}</td>
                                                <td class="py-2 text-right">
                                                    <span class="inline-block bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs">{{ $p->remaining_days ?? '-' }} hari</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-sm text-gray-500">No expiring items.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <h4 class="text-base font-medium mb-3">Stock is Almost Out</h4>
                        <div class="max-h-48 overflow-y-auto scrollbar-custom">
                            @if(isset($stockAlmostOut) && $stockAlmostOut->count())
                                <table class="w-full text-xs md:text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-500">
                                            <th class="truncate">Produk</th>
                                            <th class="truncate">Stok</th>
                                            <th class="text-right truncate">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockAlmostOut as $p)
                                            <tr class="border-t {{ $p->stock == 0 ? 'bg-red-50' : '' }}">
                                                <td class="py-2 truncate {{ $p->stock == 0 ? 'text-red-700 font-medium' : '' }}">{{ $p->name }}</td>
                                                <td class="py-2 {{ $p->stock == 0 ? 'text-red-700 font-semibold' : '' }}">{{ $p->stock }}</td>
                                                <td class="py-2 text-right">
                                                    @if($p->stock == 0)
                                                        <span class="inline-block bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">Habis</span>
                                                    @elseif($p->stock <= 5)
                                                        <span class="inline-block bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-semibold">Kritis</span>
                                                    @else
                                                        <span class="inline-block bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-semibold">Rendah</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-xs md:text-sm text-gray-500">No low-stock items.</p>
                            @endif
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

        const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
        gradient.addColorStop(0, 'rgba(59,130,246,0.15)');
        gradient.addColorStop(1, 'rgba(59,130,246,0.03)');

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
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(59,130,246,1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' }
                    },
                    y: {
                        grid: { color: 'rgba(229,231,235,0.6)' },
                        ticks: {
                            callback: function(value) {
                                try { return (value/1000000).toFixed(1) + ' jt'; } catch(e) { return value; }
                            },
                            color: '#6b7280'
                        }
                    }
                }
            }
        });
    })();
</script>
<style>
    /* Custom scrollbar styling */
    .scrollbar-custom::-webkit-scrollbar {
        width: 8px;
    }

    .scrollbar-custom::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .scrollbar-custom::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #3b82f6 0%, #1e40af 100%);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .scrollbar-custom::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 100%);
        box-shadow: 0 0 6px rgba(59, 130, 246, 0.4);
    }

    /* Firefox scrollbar */
    .scrollbar-custom {
        scrollbar-width: thin;
        scrollbar-color: #3b82f6 #f1f5f9;
    }
</style>
@endpush
