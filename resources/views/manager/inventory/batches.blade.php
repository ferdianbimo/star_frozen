@extends('layouts.manager')

@section('title','Daftar Batch')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 p-6">
    <!-- Header -->
    <div class="mb-8 bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-8 shadow-2xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-5 border border-white/20">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight">Daftar Batch</h2>
                    <p class="text-slate-400 mt-1">Pantau semua batch produk berdasarkan status</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-4 py-2 bg-indigo-500/20 text-indigo-400 rounded-xl text-sm font-medium border border-indigo-500/30">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full mr-2 animate-pulse"></span>
                    Batch Management
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('manager.inventory.batches', ['status' => 'available']) }}" 
            class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group {{ request('status') == 'available' ? 'ring-2 ring-emerald-500 ring-offset-2' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Tersedia</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">{{ $batches->where('quantity', '>', 0)->where('is_active', true)->count() }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </a>
        <a href="{{ route('manager.inventory.batches', ['status' => 'expiring']) }}" 
            class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group {{ request('status') == 'expiring' ? 'ring-2 ring-amber-500 ring-offset-2' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Segera Exp</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">
                        {{ $batches->filter(function($b) {
                            return $b->expiration_date && $b->expiration_date->isBetween(now(), now()->addDays(7)) && $b->quantity > 0;
                        })->count() }}
                    </div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </a>
        <a href="{{ route('manager.inventory.batches', ['status' => 'expired']) }}" 
            class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group {{ request('status') == 'expired' ? 'ring-2 ring-red-500 ring-offset-2' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Kadaluarsa</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">
                        {{ $batches->filter(function($b) {
                            return $b->expiration_date && $b->expiration_date->isPast();
                        })->count() }}
                    </div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>
        </a>
        <a href="{{ route('manager.inventory.batches', ['status' => 'empty']) }}" 
            class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all group {{ request('status') == 'empty' ? 'ring-2 ring-slate-500 ring-offset-2' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-slate-500 text-sm font-medium">Habis</div>
                    <div class="text-3xl font-bold mt-2 text-slate-800">{{ $batches->where('quantity', '<=', 0)->count() }}</div>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-slate-500 to-slate-600 rounded-xl flex items-center justify-center shadow-lg shadow-slate-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
            </div>
        </a>
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
                <a href="{{ route('manager.inventory.stock-out') }}" class="flex items-center px-6 py-4 text-slate-600 hover:text-blue-600 hover:bg-slate-50 whitespace-nowrap transition-all group">
                    <div class="w-8 h-8 bg-slate-100 group-hover:bg-red-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                         <svg class="w-5 h-5 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                    Stok Keluar
                </a>
                <a href="{{ route('manager.inventory.batches') }}" class="flex items-center px-6 py-4 text-blue-600 border-b-3 border-blue-600 font-semibold bg-gradient-to-t from-blue-50 to-transparent whitespace-nowrap transition-all">
                    <div class="w-8 h-8 bg-slate-100 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-3 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-filter text-white text-xs"></i>
            </div>
            <h3 class="font-semibold text-slate-800">Filter Batch</h3>
        </div>
        <form method="GET" action="{{ route('manager.inventory.batches') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode batch atau nama produk..." 
                            class="w-full pl-12 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all hover:border-slate-300">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Status</label>
                    <div class="relative">
                        <select name="status" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="expiring" {{ request('status') == 'expiring' ? 'selected' : '' }}>Segera Kadaluarsa</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                            <option value="empty" {{ request('status') == 'empty' ? 'selected' : '' }}>Habis</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="flex-1 px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all flex items-center justify-center font-semibold shadow-lg shadow-indigo-500/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('manager.inventory.batches') }}" class="px-6 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all font-medium border-2 border-slate-200 hover:border-slate-300">
                        Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
        <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-lg shadow-indigo-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Daftar Batch</h3>
                    <p class="text-sm text-slate-500">Total {{ $batches->total() }} batch ditemukan</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-full">
                    Hal {{ $batches->currentPage() }}/{{ $batches->lastPage() }}
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Batch</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Produk</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-slate-600 uppercase tracking-wider">Harga Beli</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Diterima</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Kadaluarsa</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-600 uppercase tracking-wider">Oleh</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($batches as $batch)
                    @php
                        $statusClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                        $statusText = 'Tersedia';
                        $rowClass = '';
                        if ($batch->quantity <= 0) {
                            $statusClass = 'bg-slate-100 text-slate-600 border border-slate-200';
                            $statusText = 'Habis';
                            $rowClass = 'bg-slate-50/50';
                        } elseif ($batch->isExpired()) {
                            $statusClass = 'bg-red-100 text-red-700 border border-red-200';
                            $statusText = 'Kadaluarsa';
                            $rowClass = 'bg-red-50/50';
                        } elseif ($batch->isExpiringSoon()) {
                            $statusClass = 'bg-amber-100 text-amber-700 border border-amber-200';
                            $daysLeft = $batch->daysUntilExpiration();
                            $statusText = $daysLeft > 0 ? "$daysLeft hari" : "Hari ini!";
                            $rowClass = 'bg-amber-50/50';
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-all {{ $rowClass }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center mr-4 border border-indigo-200">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <span class="font-mono font-bold text-slate-900 text-sm">{{ $batch->batch_code }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-semibold text-slate-900">{{ $batch->product->name ?? 'Unknown' }}</div>
                            @if($batch->product && $batch->product->barcode)
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $batch->product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $batch->quantity <= 0 ? 'bg-slate-100 text-slate-600 border border-slate-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                {{ $batch->quantity }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            @if($batch->purchase_price)
                                <span class="font-bold text-slate-900">Rp {{ number_format($batch->purchase_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-sm font-semibold text-slate-900">{{ $batch->date_received ? \Carbon\Carbon::parse($batch->date_received)->format('d M') : '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $batch->date_received ? \Carbon\Carbon::parse($batch->date_received)->format('Y') : '' }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($batch->expiration_date)
                                <div class="text-sm font-semibold {{ $batch->isExpired() ? 'text-red-600' : ($batch->isExpiringSoon() ? 'text-amber-600' : 'text-slate-900') }}">
                                    {{ $batch->expiration_date->format('d M Y') }}
                                </div>
                                @if($batch->isExpiringSoon() && !$batch->isExpired())
                                    <div class="text-xs text-amber-600 font-medium animate-pulse">{{ $batch->daysUntilExpiration() }} hari lagi</div>
                                @endif
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center mr-3 border border-slate-200">
                                    <span class="text-xs font-bold text-slate-600">{{ substr($batch->receivedBy->name ?? 'S', 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ $batch->receivedBy->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-4 py-1.5 text-xs font-bold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <p class="text-slate-600 text-lg font-medium">Belum ada batch</p>
                                <p class="text-slate-400 text-sm mt-1">Batch akan muncul setelah produk diterima</p>
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
                    Menampilkan <span class="font-semibold text-slate-900">{{ $batches->firstItem() ?? 0 }}</span> - <span class="font-semibold text-slate-900">{{ $batches->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-slate-900">{{ $batches->total() }}</span> batch
                </div>
                <div class="flex items-center gap-2">
                    @if($batches->onFirstPage())
                        <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </span>
                    @else
                        <a href="{{ $batches->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </a>
                    @endif
                    
                    <div class="flex items-center gap-1">
                        @foreach($batches->getUrlRange(max(1, $batches->currentPage() - 2), min($batches->lastPage(), $batches->currentPage() + 2)) as $page => $url)
                            @if($page == $batches->currentPage())
                                <span class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold shadow-lg shadow-indigo-500/30">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>
                    
                    @if($batches->hasMorePages())
                        <a href="{{ $batches->nextPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
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
