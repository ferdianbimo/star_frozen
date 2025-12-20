@extends('layouts.manager')

@section('title', 'Riwayat Stok')

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
                        <a href="{{ route('manager.inventory.index') }}" class="w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all border border-white/20">
                            <i class="fas fa-arrow-left text-white"></i>
                        </a>
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-history text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl md:text-3xl font-bold text-white">Riwayat Pergerakan Stok</h2>
                            <p class="text-slate-400 text-sm">History semua transaksi stok masuk dan keluar</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-4 py-2 bg-cyan-500/20 text-cyan-400 rounded-full text-sm font-medium border border-cyan-500/30">
                        <span class="w-2 h-2 bg-cyan-400 rounded-full mr-2 animate-pulse"></span>
                        {{ $stockLogs->total() }} Log
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6 border border-slate-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-filter text-white text-xs"></i>
                </div>
                <h3 class="font-semibold text-slate-800">Filter Riwayat</h3>
            </div>
            <form method="GET" action="{{ route('manager.inventory.history') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Produk</label>
                    <div class="relative">
                        <select name="product_id" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="">Semua Produk</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Tipe</label>
                    <div class="relative">
                        <select name="type" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="">Semua Tipe</option>
                            <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                            <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                        class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all hover:border-slate-300">
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                        class="w-full bg-white border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all hover:border-slate-300">
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl hover:from-cyan-600 hover:to-blue-700 transition-all flex items-center justify-center font-semibold shadow-lg shadow-cyan-500/30">
                        <i class="fas fa-filter mr-2"></i>
                        Filter
                    </button>
                    @if(request('product_id') || request('type') || request('start_date') || request('end_date'))
                    <a href="{{ route('manager.inventory.history') }}" class="px-5 py-3 bg-slate-100 text-slate-700 rounded-xl hover:bg-slate-200 transition-all font-medium border-2 border-slate-200 hover:border-slate-300">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-slate-100">
            <div class="px-6 py-5 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mr-3 shadow-lg shadow-cyan-500/20">
                        <i class="fas fa-history text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">Log Pergerakan Stok</h3>
                        <p class="text-sm text-slate-500">Menampilkan {{ $stockLogs->count() }} dari {{ $stockLogs->total() }} log</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Sebelum</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Sesudah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($stockLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-slate-400 text-xs"></i>
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        {{ $log->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $log->product->name }}</div>
                                <div class="text-xs text-slate-400">{{ $log->product->sku ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->type == 'in')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-emerald-50 to-green-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-arrow-down text-[10px]"></i>
                                        Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-red-50 to-rose-50 text-red-700 border border-red-200">
                                        <i class="fas fa-arrow-up text-[10px]"></i>
                                        Keluar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-lg {{ $log->type == 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $log->type == 'in' ? '+' : '-' }}{{ $log->quantity }}
                                </span>
                                <span class="text-xs text-slate-400 ml-1">{{ $log->product->unit }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $log->old_stock ?? '-' }} {{ $log->product->unit }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-700">{{ $log->new_stock ?? '-' }}</span>
                                <span class="text-xs text-slate-400 ml-1">{{ $log->product->unit }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr($log->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-slate-600">{{ $log->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">
                                {{ $log->notes ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                                        <i class="fas fa-history text-slate-400 text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-slate-600 mb-1">Belum ada riwayat</h4>
                                    <p class="text-sm text-slate-400">Belum ada pergerakan stok yang tercatat</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($stockLogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $stockLogs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
