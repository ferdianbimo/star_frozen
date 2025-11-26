<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Star Frozen POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-900 text-white flex flex-col">
            <div class="p-6 bg-blue-950">
                <h1 class="text-2xl font-bold">Star Frozen</h1>
                <p class="text-blue-300 text-sm">POS Manager</p>
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-800 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    Dashboard
                </a>
                
                <a href="{{ route('manager.inventory.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-800 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Inventory
                </a>
                
                <a href="{{ route('manager.finance.index') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-800 rounded-lg font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/></svg>
                    Keuangan
                </a>
                
                <a href="{{ route('manager.access.index') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-blue-800 rounded-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                    Hak Akses
                </a>
            </nav>
            
            <div class="p-4 border-t border-blue-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-300 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium transition">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-8 py-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Laporan Keuangan</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                    <div class="flex gap-3">
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
                        <p class="text-gray-600 text-sm font-medium mb-2">Persentase Keuntungan</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ number_format($profitPercentage, 1) }}%
                        </h3>
                        <p class="text-gray-500 text-sm">Dari total pemasukan</p>
                    </div>
                </div>

                <!-- Charts and Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Income vs Expense Chart -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Tren Pemasukan vs Pengeluaran</h2>
                        <canvas id="financeChart" height="80"></canvas>
                    </div>

                    <!-- Top Products by Revenue -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Top Produk Penjualan</h2>
                        <div class="space-y-4">
                            @forelse($incomeByProduct as $item)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="font-medium text-gray-900 text-sm">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</p>
                                <div class="w-full h-2 bg-gray-200 rounded-full mt-2">
                                    @php
                                        $maxSales = $incomeByProduct->max('total_sales');
                                        $percentage = $maxSales > 0 ? ($item->total_sales / $maxSales) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-gray-400 py-8">Tidak ada data penjualan</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Chart Script -->
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
    </script>
</body>
</html>
