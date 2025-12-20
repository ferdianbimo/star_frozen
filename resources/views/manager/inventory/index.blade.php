@extends('layouts.manager')

@section('title','Inventory')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="p-6">
        <!-- Header with Dark Theme -->
        <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl shadow-2xl p-6 mb-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/10 to-purple-600/10"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/10 rounded-full translate-y-24 -translate-x-24"></div>
            
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl md:text-3xl font-bold text-white">Stok Produk</h2>
                            <p class="text-slate-400 text-sm">Pantau stok produk dan batch secara realtime</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-4 py-2 bg-emerald-500/20 text-emerald-400 rounded-full text-sm font-medium border border-emerald-500/30">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                        Live Data
                    </span>
                    <button onclick="location.reload()" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all duration-300 flex items-center gap-2 border border-white/20 backdrop-blur-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards with Enhanced Design -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-blue-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="text-slate-500 text-sm font-medium">Total Produk</div>
                    <div class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['total_products'] ?? 0 }}</div>
                </div>
            </div>

            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div class="text-slate-500 text-sm font-medium">Total Stok</div>
                    <div class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_stock'] ?? 0) }}</div>
                </div>
            </div>

            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-500/20 to-amber-600/10 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-amber-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="text-slate-500 text-sm font-medium">Stok Rendah</div>
                    <div class="text-3xl font-bold text-amber-600 mt-1">{{ $stats['low_stock_count'] ?? 0 }}</div>
                </div>
            </div>

            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-indigo-500/20 to-indigo-600/10 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="text-slate-500 text-sm font-medium">Total Batch</div>
                    <div class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['total_batches'] ?? 0 }}</div>
                </div>
            </div>

            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-yellow-500/30 animate-pulse">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-slate-500 text-sm font-medium">Segera Exp</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['expiring_soon'] ?? 0 }}</div>
                </div>
            </div>

            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-red-500/20 to-red-600/10 rounded-full -translate-y-10 translate-x-10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center mb-3 shadow-lg shadow-red-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                    <div class="text-slate-500 text-sm font-medium">Kadaluarsa</div>
                    <div class="text-3xl font-bold text-red-600 mt-1">{{ $stats['expired'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white rounded-2xl shadow-sm mb-6 border border-slate-100 overflow-x-auto">
            <div class="flex min-w-max">
                <a href="{{ route('manager.inventory.index') }}" class="flex items-center px-6 py-4 text-blue-600 border-b-3 border-blue-600 font-semibold bg-gradient-to-t from-blue-50 to-transparent whitespace-nowrap transition-all">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    Stok Produk
                </a>
                <a href="{{ route('manager.inventory.stock-out') }}" class="flex items-center px-6 py-4 text-slate-600 hover:text-blue-600 hover:bg-slate-50 whitespace-nowrap transition-all group">
                    <div class="w-8 h-8 bg-slate-100 group-hover:bg-red-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 text-slate-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-filter text-white text-xs"></i>
                </div>
                <h3 class="font-semibold text-slate-800">Filter Produk</h3>
            </div>
            <form method="GET" action="{{ route('manager.inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2 lg:col-span-2">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Cari Produk</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk, barcode, atau kategori..." 
                            class="w-full pl-12 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all hover:border-slate-300">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Urutkan</label>
                    <div class="relative">
                        <select name="sort" onchange="this.form.submit()" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                            <option value="stock_low" {{ request('sort') == 'stock_low' ? 'selected' : '' }}>Stok (Terendah)</option>
                            <option value="stock_high" {{ request('sort') == 'stock_high' ? 'selected' : '' }}>Stok (Tertinggi)</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/30 flex items-center justify-center font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                    @if(request('search') || request('sort'))
                    <a href="{{ route('manager.inventory.index') }}" class="px-5 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all font-medium text-center border-2 border-slate-200 hover:border-slate-300">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Products Table -->
            <div class="xl:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
                    <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg shadow-blue-500/20">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">Daftar Produk</h3>
                                <p class="text-sm text-slate-500">Total {{ $products->total() }} produk tersedia</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                                Hal {{ $products->currentPage() }}/{{ $products->lastPage() }}
                            </span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Produk</th>
                                    <th class="px-4 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Kategori</th>
                                    <th class="px-4 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Stok</th>
                                    <th class="px-4 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Batch</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($products as $product)
                                @php
                                    $activeBatches = $product->batches ? $product->batches->where('is_active', true)->where('quantity', '>', 0) : collect();
                                    $expiringBatches = $activeBatches->filter(function($b) {
                                        return $b->expiration_date && $b->expiration_date->isBetween(now(), now()->addDays(7));
                                    });
                                    $isLowStock = $product->effective_stock <= ($product->low_stock_threshold ?? 5);
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-all {{ $isLowStock ? 'bg-red-50/50' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($product->image)
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover mr-4 shadow-sm border border-slate-200">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center mr-4 border border-slate-200">
                                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                                                @if($product->barcode)
                                                    <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $product->barcode }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="px-3 py-1.5 text-xs font-semibold bg-slate-100 text-slate-700 rounded-lg">
                                            {{ $product->category ?? 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @php $effectiveStock = $product->effective_stock; @endphp
                                        <div class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $isLowStock ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                            {{ $effectiveStock }}
                                            @if($isLowStock)
                                                <svg class="w-4 h-4 ml-1.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="font-semibold text-slate-700">{{ $activeBatches->count() }}</span>
                                            @if($expiringBatches->count() > 0)
                                                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg border border-amber-200 animate-pulse">
                                                    {{ $expiringBatches->count() }} exp!
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-slate-900 text-lg">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <p class="text-slate-600 text-lg font-medium">Belum ada produk</p>
                                            <p class="text-slate-400 text-sm mt-1">Produk yang ditambahkan akan muncul di sini</p>
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
                                Menampilkan <span class="font-semibold text-slate-900">{{ $products->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-900">{{ $products->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-900">{{ $products->total() }}</span> produk
                            </div>
                            <div class="flex items-center gap-2">
                                @if($products->onFirstPage())
                                    <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </a>
                                @endif
                                
                                <div class="flex items-center gap-1">
                                    @foreach($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                                        @if($page == $products->currentPage())
                                            <span class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-semibold shadow-lg shadow-blue-500/30">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                </div>
                                
                                @if($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
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

            <!-- Recent Batches Sidebar -->
            <div class="xl:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100 sticky top-6">
                    <div class="px-5 py-5 bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 text-white">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold">Batch Terbaru</h3>
                                <p class="text-sm text-slate-300">Stok masuk terkini</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        @if(isset($recentBatches) && $recentBatches->count() > 0)
                            <div class="space-y-3 max-h-[480px] overflow-y-auto custom-scrollbar pr-1">
                                @foreach($recentBatches as $batch)
                                    @php
                                        $statusClass = 'border-emerald-200 bg-gradient-to-br from-emerald-50 to-white';
                                        $badgeClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                                        $iconBg = 'bg-emerald-100';
                                        $iconColor = 'text-emerald-600';
                                        if ($batch->isExpired()) {
                                            $statusClass = 'border-red-200 bg-gradient-to-br from-red-50 to-white';
                                            $badgeClass = 'bg-red-100 text-red-700 border border-red-200';
                                            $iconBg = 'bg-red-100';
                                            $iconColor = 'text-red-600';
                                        } elseif ($batch->isExpiringSoon()) {
                                            $statusClass = 'border-amber-200 bg-gradient-to-br from-amber-50 to-white';
                                            $badgeClass = 'bg-amber-100 text-amber-700 border border-amber-200';
                                            $iconBg = 'bg-amber-100';
                                            $iconColor = 'text-amber-600';
                                        }
                                    @endphp
                                    <div class="border rounded-2xl p-4 {{ $statusClass }} transition-all hover:shadow-lg hover:-translate-y-0.5 group">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="font-semibold text-sm text-slate-900 truncate flex-1 pr-2">{{ $batch->product->name ?? 'Unknown' }}</div>
                                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badgeClass }} flex-shrink-0">
                                                +{{ $batch->quantity }}
                                            </span>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-center text-xs text-slate-600">
                                                <div class="w-6 h-6 {{ $iconBg }} rounded-lg flex items-center justify-center mr-2">
                                                    <svg class="w-3.5 h-3.5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                </div>
                                                <span class="font-mono">{{ $batch->batch_code }}</span>
                                            </div>
                                            <div class="flex items-center text-xs text-slate-600">
                                                <div class="w-6 h-6 {{ $iconBg }} rounded-lg flex items-center justify-center mr-2">
                                                    <svg class="w-3.5 h-3.5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                @if($batch->expiration_date)
                                                    Exp: {{ $batch->expiration_date->format('d M Y') }}
                                                @else
                                                    <span class="text-slate-400">Tidak ada exp</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center text-xs text-slate-400 mt-3 pt-3 border-t border-slate-200/80">
                                            <div class="w-5 h-5 bg-slate-100 rounded-full flex items-center justify-center mr-2">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            {{ $batch->receivedBy->name ?? 'System' }} • {{ $batch->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('manager.inventory.batches') }}" class="mt-5 flex items-center justify-center py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/20 font-semibold text-sm group">
                                Lihat Semua Batch
                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <p class="text-slate-600 font-medium">Belum ada batch</p>
                                <p class="text-slate-400 text-sm mt-1">Batch terbaru akan muncul di sini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3b82f6, #6366f1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #2563eb, #4f46e5);
        }
    </style>
@endsection
