@extends('layouts.manager')

@section('title','History Pemasukan')

@section('content')
    <main class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-8 py-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">History Pemasukan</h1>
                        <p class="text-sm text-gray-500 mt-1">Log penjualan dan stok keluar</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('manager.finance.index') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition">
                            Kembali ke Dashboard
                        </a>
                        <a href="{{ route('manager.finance.expenses') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Kelola Pengeluaran
                        </a>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-8">
                <!-- Summary Card -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 mb-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-green-100 text-sm font-medium mb-2">Total Pemasukan (Filtered)</p>
                            <h2 class="text-4xl font-bold">Rp {{ number_format($totalFilteredValue, 0, ',', '.') }}</h2>
                        </div>
                        <div class="bg-white/20 p-4 rounded-lg">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                    <form method="GET" action="{{ route('manager.finance.income') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kasir</label>
                                <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Semua Kasir</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                        
                        <!-- Export Buttons -->
                        <div class="flex gap-3 pt-2 border-t">
                            <button type="submit" name="export" value="pdf" class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Export PDF
                            </button>
                            <button type="submit" name="export" value="excel" class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export Excel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Income Logs Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Terjual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pemasukan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($incomeLogs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $log->product->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->product->code ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold text-red-600">{{ abs($log->change) }} pack</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($log->unit_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-lg font-bold text-green-600">Rp {{ number_format($log->total_value, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $log->note ?? '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-gray-500 text-lg font-medium">Belum ada data penjualan</p>
                                            <p class="text-gray-400 text-sm mt-1">Transaksi penjualan akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($incomeLogs->hasPages())
                <div class="mt-6">
                    {{ $incomeLogs->links() }}
                </div>
                @endif
            </div>
        </main>
    </div>

@endsection
