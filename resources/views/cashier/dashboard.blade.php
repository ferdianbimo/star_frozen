@extends('layouts.cashier')

@section('title','Dashboard')

@section('content')
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                    <div class="lg:col-span-1 space-y-4">
                        <div class="bg-white rounded-lg shadow p-5 flex items-start">
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Daily Sales</p>
                                <h3 class="text-2xl font-bold mt-2">{{ 'Rp ' . number_format($dailySales ?? 0, 0, ',', '.') }}</h3>
                                    @php
                                        $dp = $dailyPercentage ?? 0;
                                    @endphp
                                    <p class="text-sm mt-1">
                                        @if($dp > 0)
                                            <span class="text-green-600">&uarr; {{ abs($dp) }}% dari kemarin</span>
                                        @elseif($dp < 0)
                                            <span class="text-red-600">&darr; {{ abs($dp) }}% dari kemarin</span>
                                        @else
                                            <span class="text-gray-500">0% dari kemarin</span>
                                        @endif
                                    </p>
                            </div>
                            <div class="ml-4 bg-blue-50 rounded p-2">
                                <i class="fas fa-download text-blue-500"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-5 flex items-start">
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Monthly Sales</p>
                                <h3 class="text-2xl font-bold mt-2">{{ 'Rp ' . number_format($monthlySales ?? 0, 0, ',', '.') }}</h3>
                                <p class="text-sm text-green-500 mt-1">@if(isset($monthlyPercentage)) {{ ($monthlyPercentage >= 0 ? '↑ ' : '↓ ') . abs($monthlyPercentage) . '% dari bulan lalu' }} @endif</p>
                            </div>
                            <div class="ml-4 bg-yellow-50 rounded p-2">
                                <i class="fas fa-chart-line text-yellow-500"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-5 flex items-start">
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Low Stock</p>
                                <h3 class="text-2xl font-bold mt-2">{{ $lowStockCount ?? 0 }}</h3>
                                <p class="text-sm text-yellow-500 mt-1">Perlu restok</p>
                            </div>
                            <div class="ml-4 bg-yellow-100 rounded p-2">
                                <i class="fas fa-box-open text-yellow-600"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-5 flex items-start">
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Expiring Soon</p>
                                <h3 class="text-2xl font-bold mt-2">{{ $expiringCount ?? 0 }}</h3>
                                <p class="text-sm text-red-500 mt-1">Dalam 7 hari</p>
                            </div>
                            <div class="ml-4 bg-red-50 rounded p-2">
                                <i class="fas fa-hourglass-half text-red-500"></i>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-3">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Sales Trend</h3>
                                    <p class="text-sm text-gray-500">Last 7 days</p>
                                </div>
                                <div>
                                    <select class="border rounded px-3 py-1 text-sm">
                                        <option>Last 7 days</option>
                                        <option>Last 30 days</option>
                                    </select>
                                </div>
                            </div>
                            <div style="height:320px;">
                                <canvas id="salesChartLarge" style="width:100%;height:100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h4 class="text-base font-medium mb-3">Expired Soon</h4>
                        <div class="max-h-48 overflow-auto">
                            @if(isset($expiringProducts) && $expiringProducts->count())
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-500">
                                            <th>Produk</th>
                                            <th>Expired</th>
                                            <th class="text-right">Sisa Hari</th>
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
                        <h4 class="text-base font-medium mb-3">Stok Hampir Habis</h4>
                        <div class="max-h-48 overflow-auto">
                            @if(isset($stockAlmostOut) && $stockAlmostOut->count())
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-500">
                                            <th>Produk</th>
                                            <th>Stok</th>
                                            <th class="text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockAlmostOut as $p)
                                            <tr class="border-t">
                                                <td class="py-2">{{ $p->name }}</td>
                                                <td class="py-2">{{ $p->stock }}</td>
                                                <td class="py-2 text-right"><span class="text-xs text-yellow-600">{{ $p->stock <= 5 ? 'Kritis' : 'Rendah' }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-sm text-gray-500">No low-stock items.</p>
                            @endif
                        </div>
                    </div>
                </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
@endpush

