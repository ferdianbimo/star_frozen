@extends('layouts.manager')

@section('title','Daftar Batch')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Inventory Management</h2>
            <p class="text-gray-600 mt-1">Pantau semua batch produk berdasarkan status</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2 animate-pulse"></span>
                Batch Management
            </span>
            <button onclick="location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('manager.inventory.batches', ['status' => 'available']) }}" 
            class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white hover:shadow-xl transition transform hover:scale-105 {{ request('status') == 'available' ? 'ring-4 ring-green-300' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-green-100 text-sm">Tersedia</div>
                    <div class="text-3xl font-bold mt-1">{{ $batches->where('quantity', '>', 0)->where('is_active', true)->count() }}</div>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </a>
        <a href="{{ route('manager.inventory.batches', ['status' => 'expiring']) }}" 
            class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white hover:shadow-xl transition transform hover:scale-105 {{ request('status') == 'expiring' ? 'ring-4 ring-yellow-300' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-yellow-100 text-sm">Segera Exp</div>
                    <div class="text-3xl font-bold mt-1">
                        {{ $batches->filter(function($b) {
                            return $b->expiration_date && $b->expiration_date->isBetween(now(), now()->addDays(7)) && $b->quantity > 0;
                        })->count() }}
                    </div>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </a>
        <a href="{{ route('manager.inventory.batches', ['status' => 'expired']) }}" 
            class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white hover:shadow-xl transition transform hover:scale-105 {{ request('status') == 'expired' ? 'ring-4 ring-red-300' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-red-100 text-sm">Kadaluarsa</div>
                    <div class="text-3xl font-bold mt-1">
                        {{ $batches->filter(function($b) {
                            return $b->expiration_date && $b->expiration_date->isPast();
                        })->count() }}
                    </div>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
            </div>
        </a>
        <a href="{{ route('manager.inventory.batches', ['status' => 'empty']) }}" 
            class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white hover:shadow-xl transition transform hover:scale-105 {{ request('status') == 'empty' ? 'ring-4 ring-gray-300' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-gray-200 text-sm">Habis</div>
                    <div class="text-3xl font-bold mt-1">{{ $batches->where('quantity', '<=', 0)->count() }}</div>
                </div>
                <div class="bg-gray-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="flex border-b">
            <a href="{{ route('manager.inventory.index') }}" class="flex items-center px-6 py-4 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                Stok Produk
            </a>
            <a href="{{ route('manager.inventory.stock-out') }}" class="flex items-center px-6 py-4 text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Stok Keluar
            </a>
            <a href="{{ route('manager.inventory.batches') }}" class="flex items-center px-6 py-4 text-blue-600 border-b-2 border-blue-600 font-semibold bg-blue-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Daftar Batch
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('manager.inventory.batches') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode batch atau nama produk..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="expiring" {{ request('status') == 'expiring' ? 'selected' : '' }}>Segera Kadaluarsa</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                        <option value="empty" {{ request('status') == 'empty' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('manager.inventory.batches') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Daftar Batch
            </h3>
            <span class="text-sm text-gray-500">{{ $batches->total() }} batch</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Batch</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produk</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Beli</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Diterima</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kadaluarsa</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Oleh</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batches as $batch)
                    @php
                        $statusClass = 'bg-green-100 text-green-800';
                        $statusText = 'Tersedia';
                        $rowClass = '';
                        if ($batch->quantity <= 0) {
                            $statusClass = 'bg-gray-100 text-gray-800';
                            $statusText = 'Habis';
                            $rowClass = 'bg-gray-50';
                        } elseif ($batch->isExpired()) {
                            $statusClass = 'bg-red-100 text-red-800';
                            $statusText = 'Kadaluarsa';
                            $rowClass = 'bg-red-50';
                        } elseif ($batch->isExpiringSoon()) {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $daysLeft = $batch->daysUntilExpiration();
                            $statusText = $daysLeft > 0 ? "$daysLeft hari" : "Hari ini!";
                            $rowClass = 'bg-yellow-50';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition {{ $rowClass }}">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <span class="font-mono font-bold text-gray-900">{{ $batch->batch_code }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ $batch->product->name ?? 'Unknown' }}</div>
                            @if($batch->product && $batch->product->barcode)
                                <div class="text-xs text-gray-400">{{ $batch->product->barcode }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $batch->quantity <= 0 ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700' }}">
                                {{ $batch->quantity }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            @if($batch->purchase_price)
                                <span class="font-medium text-gray-900">Rp {{ number_format($batch->purchase_price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-sm text-gray-900">{{ $batch->date_received ? \Carbon\Carbon::parse($batch->date_received)->format('d M') : '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $batch->date_received ? \Carbon\Carbon::parse($batch->date_received)->format('Y') : '' }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($batch->expiration_date)
                                <div class="text-sm {{ $batch->isExpired() ? 'text-red-600 font-bold' : ($batch->isExpiringSoon() ? 'text-yellow-600 font-bold' : 'text-gray-900') }}">
                                    {{ $batch->expiration_date->format('d M Y') }}
                                </div>
                                @if($batch->isExpiringSoon() && !$batch->isExpired())
                                    <div class="text-xs text-yellow-600 animate-pulse">{{ $batch->daysUntilExpiration() }} hari lagi</div>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium text-gray-600">{{ substr($batch->receivedBy->name ?? 'S', 0, 1) }}</span>
                                </div>
                                <span class="text-sm text-gray-700">{{ $batch->receivedBy->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-gray-500 text-lg">Belum ada batch</p>
                                <p class="text-gray-400 text-sm mt-1">Batch akan muncul setelah produk diterima</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $batches->links() }}
        </div>
    </div>
</div>
@endsection
