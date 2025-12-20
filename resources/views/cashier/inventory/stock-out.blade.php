@extends('layouts.cashier')

@section('title','Stok Keluar')

@section('content')
                <!-- Modern Header -->
                <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-6 mb-8 shadow-xl">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30">
                                <i class="fas fa-arrow-up text-2xl text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Stok Keluar</h2>
                                <p class="text-slate-400 text-sm">Riwayat barang keluar dari transaksi</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-slate-700/50 rounded-xl border border-slate-600">
                                <span class="text-slate-400 text-sm"><i class="fas fa-box-open mr-2"></i>{{ $logs->total() ?? 0 }} Log Keluar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <!-- Tabs -->
                    <div class="border-b border-slate-200 bg-slate-50">
                        <nav class="flex" aria-label="Tabs">
                            <a href="{{ route('cashier.inventory.index') }}" class="py-4 px-6 border-b-2 border-transparent text-sm font-medium text-slate-500 hover:text-slate-700 hover:bg-white/50 transition-colors">
                                <i class="fas fa-arrow-down mr-2"></i>Stok Masuk
                            </a>
                            <a href="{{ route('cashier.inventory.stock-out') }}" class="py-4 px-6 border-b-2 border-red-500 text-sm font-semibold text-red-600 bg-white">
                                <i class="fas fa-arrow-up mr-2"></i>Stok Keluar
                            </a>
                        </nav>
                    </div>
                    
                    <div class="p-6">
                        <!-- Filter & Search -->
                        <form method="GET" action="{{ route('cashier.inventory.stock-out') }}" class="flex flex-wrap items-center gap-4 mb-6">
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-slate-400"></i>
                                    </div>
                                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk, kategori..." class="w-full border-2 border-slate-200 rounded-xl pl-11 pr-4 py-3 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-700 hover:border-slate-300">
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="sortSelect" class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Urutkan:</label>
                                <div class="relative">
                                    <select name="sort" class="custom-select appearance-none border-2 border-slate-200 rounded-xl pl-4 pr-10 py-2.5 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-700 cursor-pointer hover:border-slate-300" onchange="this.form.submit()">
                                        <option value="tanggal_terbaru" {{ ($sort ?? '') == 'tanggal_terbaru' ? 'selected' : '' }}>Tanggal Terbaru</option>
                                        <option value="tanggal_terlama" {{ ($sort ?? '') == 'tanggal_terlama' ? 'selected' : '' }}>Tanggal Terlama</option>
                                        <option value="jumlah_banyak" {{ ($sort ?? '') == 'jumlah_banyak' ? 'selected' : '' }}>Jumlah Terbanyak</option>
                                        <option value="jumlah_sedikit" {{ ($sort ?? '') == 'jumlah_sedikit' ? 'selected' : '' }}>Jumlah Tersedikit</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all">
                                <i class="fas fa-search mr-2"></i> Cari
                            </button>
                        </form>
                        
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                            <table class="min-w-full">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Jam</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Produk</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Qty Keluar</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Stok Saat Ini</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($logs as $index => $log)
                                    @php
                                        $currentStock = $log->product ? $log->product->effective_stock : 0;
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $logs->firstItem() + $index }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 font-medium">{{ $log->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $log->created_at->format('H:i:s') }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($log->product && $log->product->image)
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($log->product->image) }}" alt="{{ $log->product->name }}" class="h-10 w-10 rounded-lg object-cover border border-slate-200">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                                                        <i class="fas fa-box text-slate-400"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-slate-700">{{ $log->product ? $log->product->name : '—' }}</div>
                                                    <div class="text-xs text-slate-400">{{ $log->product ? $log->product->category : '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-red-100 to-rose-100 text-red-700 border border-red-200">
                                                <i class="fas fa-arrow-down text-xs"></i>
                                                {{ abs($log->change) }} {{ $log->unit_type ?? 'pcs' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 {{ $currentStock <= 10 ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }} rounded-lg text-sm font-semibold">{{ $currentStock }} pcs</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 max-w-[200px] truncate" title="{{ $log->note }}">
                                            {{ \Illuminate\Support\Str::limit($log->note, 30) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center mb-4">
                                                    <i class="fas fa-receipt text-4xl text-slate-400"></i>
                                                </div>
                                                <h3 class="text-lg font-semibold text-slate-700 mb-2">Belum Ada Stok Keluar</h3>
                                                <p class="text-sm text-slate-500">Stok keluar dari transaksi POS akan muncul di sini</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Custom Pagination -->
                    @if($logs->hasPages())
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                            <div class="text-sm text-slate-600">
                                Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }} log
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Previous Button --}}
                                @if ($logs->onFirstPage())
                                    <span class="px-3 py-2 bg-slate-200 text-slate-400 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </span>
                                @else
                                    <a href="{{ $logs->previousPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </a>
                                @endif

                                {{-- Page Numbers --}}
                                @foreach(range(1, $logs->lastPage()) as $page)
                                    @if($page == $logs->currentPage())
                                        <span class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-lg font-medium shadow-lg shadow-red-500/30">{{ $page }}</span>
                                    @elseif($page == 1 || $page == $logs->lastPage() || abs($page - $logs->currentPage()) <= 2)
                                        <a href="{{ $logs->url($page) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">{{ $page }}</a>
                                    @elseif(abs($page - $logs->currentPage()) == 3)
                                        <span class="px-2 py-2 text-slate-400">...</span>
                                    @endif
                                @endforeach

                                {{-- Next Button --}}
                                @if ($logs->hasMorePages())
                                    <a href="{{ $logs->nextPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </a>
                                @else
                                    <span class="px-3 py-2 bg-slate-200 text-slate-400 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
@endsection
