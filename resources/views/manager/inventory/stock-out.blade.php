@extends('layouts.manager')

@section('title','Stok Keluar')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 p-6">
    <!-- Header -->
    <div class="mb-8 bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-8 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-5 border border-white/20">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight">Stok Keluar</h2>
                    <p class="text-slate-400 mt-1">Monitoring dan riwayat stok keluar dari sistem</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-4 py-2 bg-red-500/20 text-red-400 rounded-xl text-sm font-medium border border-red-500/30">
                    <span class="w-2 h-2 bg-red-400 rounded-full mr-2 animate-pulse"></span>
                    Stok Keluar
                </span>
                <button onclick="location.reload()" class="px-5 py-2.5 bg-white/10 backdrop-blur-sm border border-white/20 text-white rounded-xl hover:bg-white/20 transition-all flex items-center font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Keluar Hari Ini</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">{{ number_format($stats['total_out_today'] ?? 0) }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Keluar Minggu Ini</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">{{ number_format($stats['total_out_week'] ?? 0) }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Keluar Bulan Ini</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">{{ number_format($stats['total_out_month'] ?? 0) }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Transaksi Hari Ini</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">{{ number_format($stats['transactions_today'] ?? 0) }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Nilai Hari Ini</div>
                    <div class="text-2xl font-bold mt-2 text-slate-800">{{ number_format($stats['total_value_today'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Nilai Bulan Ini</div>
                    <div class="text-2xl font-bold mt-2 text-slate-800">{{ number_format($stats['total_value_month'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
         <div class="bg-white rounded-2xl shadow-sm mb-6 border border-slate-100">
            <div class="flex overflow-x-auto">
                <a href="{{ route('manager.inventory.index') }}" class="flex items-center px-6 py-4 text-slate-600 hover:text-blue-600 hover:bg-slate-50 whitespace-nowrap transition-all group">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5  bg-slate-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    Stok Produk
                </a>
                <a href="{{ route('manager.inventory.stock-out') }}" class="flex items-center px-6 py-4 text-blue-600 border-b-3 border-blue-600 font-semibold bg-gradient-to-t from-blue-50 to-transparent whitespace-nowrap transition-all">
                    <div class="w-8 h-8 bg-slate-100 group-hover:bg-red-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                         <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    Stok Keluar
                </a>
                <a href="{{ route('manager.inventory.batches') }}" class="flex items-center px-6 py-4 text-slate-600 hover:text-blue-600 hover:bg-slate-50 whitespace-nowrap transition-all group">
                    <div class="w-8 h-8 bg-slate-100 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    Daftar Batch
                </a>
            </div>
        </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-6 border border-slate-100">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-rose-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-filter text-white text-xs"></i>
            </div>
            <h3 class="font-semibold text-slate-800">Filter Stok Keluar</h3>
        </div>
        <form method="GET" action="{{ route('manager.inventory.stock-out') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk, barcode, kategori..." 
                            class="w-full pl-12 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all hover:border-slate-300">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Kasir</label>
                    <div class="relative">
                        <select name="user" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="">Semua Kasir</option>
                            @if(isset($users))
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                        class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all hover:border-slate-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                        class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all hover:border-slate-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Urutkan</label>
                    <div class="relative">
                        <select name="sort" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="qty_high" {{ request('sort') == 'qty_high' ? 'selected' : '' }}>Qty Tertinggi</option>
                            <option value="qty_low" {{ request('sort') == 'qty_low' ? 'selected' : '' }}>Qty Terendah</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-5 gap-3">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-xl hover:from-red-600 hover:to-rose-700 transition-all flex items-center font-semibold shadow-lg shadow-red-500/30">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
                @if(request('search') || request('user') || request('date_from') || request('date_to') || request('sort'))
                <a href="{{ route('manager.inventory.stock-out') }}" class="px-6 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all font-medium border-2 border-slate-200 hover:border-slate-300">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Stock Out History Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mr-3 shadow-lg shadow-red-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Riwayat Stok Keluar</h3>
                    <p class="text-sm text-slate-500">Total {{ $stockLogs->total() }} data ditemukan</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-red-100 text-red-700 text-sm font-semibold rounded-full">
                    Hal {{ $stockLogs->currentPage() }}/{{ $stockLogs->lastPage() }}
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Produk</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Stok</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Batch</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Catatan</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockLogs as $log)
                    <tr class="hover:bg-slate-50/80 transition-all">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-100 to-red-50 flex items-center justify-center mr-4 border border-red-200">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $log->product->name ?? 'Unknown' }}</div>
                                    @if($log->product && $log->product->barcode)
                                        <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $log->product->barcode }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold bg-red-100 text-red-700 border border-red-200">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                                {{ abs($log->change) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-1 text-sm">
                                <span class="text-slate-500 font-medium">{{ $log->previous_stock }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <span class="font-bold text-slate-900">{{ $log->new_stock }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if($log->batch)
                                <span class="px-3 py-1.5 text-xs font-semibold bg-indigo-100 text-indigo-700 rounded-lg border border-indigo-200">
                                    {{ $log->batch->batch_code }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm text-slate-600 truncate max-w-xs block">{{ $log->note ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center mr-3 border border-slate-200">
                                    <span class="text-xs font-bold text-slate-600">{{ substr($log->user->name ?? 'S', 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-slate-900">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $log->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </div>
                                <p class="text-slate-600 text-lg font-medium">Belum ada riwayat stok keluar</p>
                                <p class="text-slate-400 text-sm mt-1">Data stok keluar akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Enhanced Pagination -->
        <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-white border-t border-slate-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    Menampilkan <span class="font-semibold text-slate-900">{{ $stockLogs->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-900">{{ $stockLogs->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-900">{{ $stockLogs->total() }}</span> data
                </div>
                <div class="flex items-center gap-2">
                    @if($stockLogs->onFirstPage())
                        <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </span>
                    @else
                        <a href="{{ $stockLogs->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </a>
                    @endif
                    
                    <div class="flex items-center gap-1">
                        @foreach($stockLogs->getUrlRange(max(1, $stockLogs->currentPage() - 2), min($stockLogs->lastPage(), $stockLogs->currentPage() + 2)) as $page => $url)
                            @if($page == $stockLogs->currentPage())
                                <span class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold shadow-lg shadow-red-500/30">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>
                    
                    @if($stockLogs->hasMorePages())
                        <a href="{{ $stockLogs->nextPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @else
                        <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
