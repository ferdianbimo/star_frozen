@extends('layouts.manager')

@section('title','History Pemasukan')

@section('content')
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">History Pemasukan</h1>
                        <p class="text-sm text-slate-500">Log penjualan dan stok keluar</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-slate-600/20 hover:shadow-slate-600/30">
                    <i class="fas fa-arrow-left"></i>
                    Dashboard
                </a>
                <a href="{{ route('manager.finance.expenses') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40">
                    <i class="fas fa-money-bill-wave"></i>
                    Kelola Pengeluaran
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Pemasukan -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-xl shadow-emerald-500/20 p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-coins text-2xl"></i>
                    </div>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium backdrop-blur-sm">Total</span>
                </div>
                <p class="text-emerald-100 text-sm font-medium mb-1">Total Pemasukan</p>
                <h2 class="text-3xl font-bold">Rp {{ number_format($totalFilteredValue, 0, ',', '.') }}</h2>
            </div>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fas fa-receipt text-white text-lg"></i>
                    </div>
                </div>
                <p class="text-slate-500 text-sm font-medium mb-1">Jumlah Transaksi</p>
                <h2 class="text-3xl font-bold text-slate-800">{{ $incomeLogs->total() }}</h2>
            </div>
        </div>

        <!-- Filter Periode -->
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <i class="fas fa-calendar-alt text-white text-lg"></i>
                    </div>
                </div>
                <p class="text-slate-500 text-sm font-medium mb-1">Periode</p>
                <h2 class="text-lg font-bold text-slate-800">
                    @if(request('start_date') && request('end_date'))
                        {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                    @elseif(request('start_date'))
                        Dari {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                    @elseif(request('end_date'))
                        Sampai {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                    @else
                        Semua Waktu
                    @endif
                </h2>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-6 mb-6 border border-slate-100">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center">
                <i class="fas fa-filter text-white"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Filter & Pencarian</h3>
        </div>
        
        <form method="GET" action="{{ route('manager.finance.income') }}">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                        Cari Produk
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fas fa-search text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk..." 
                               class="w-full pl-11 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all hover:border-slate-300">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                        Filter Kasir
                    </label>
                    <div class="relative">
                        <select name="user_id" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="">Semua Kasir</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                        Dari Tanggal
                    </label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all hover:border-slate-300">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                        Sampai Tanggal
                    </label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full px-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all hover:border-slate-300">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        Cari
                    </button>
                </div>
            </div>
            
            <!-- Export Buttons -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-100">
                <button type="submit" name="export" value="pdf" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-red-500/30 hover:shadow-red-500/40">
                    <i class="fas fa-file-pdf"></i>
                    Export PDF
                </button>
                <button type="submit" name="export" value="excel" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40">
                    <i class="fas fa-file-excel"></i>
                    Export Excel
                </button>
                @if(request()->hasAny(['search', 'user_id', 'start_date', 'end_date']))
                <a href="{{ route('manager.finance.income') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all">
                    <i class="fas fa-times"></i>
                    Reset Filter
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Income Logs Table -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Daftar Pemasukan</h3>
                </div>
                <span class="px-3 py-1 bg-slate-100 rounded-full text-xs font-medium text-slate-600">
                    {{ $incomeLogs->total() }} Data
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-slate-400"></i>
                                Tanggal
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-box text-slate-400"></i>
                                Produk
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-cubes text-slate-400"></i>
                                Jumlah Terjual
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tag text-slate-400"></i>
                                Harga Satuan
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-coins text-slate-400"></i>
                                Total
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-slate-400"></i>
                                User
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-sticky-note text-slate-400"></i>
                                Catatan
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($incomeLogs as $log)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-800">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-slate-400">{{ $log->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-cube text-indigo-500"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $log->product->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-400">{{ $log->product->code ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg font-bold text-sm">
                                <i class="fas fa-minus text-xs"></i>
                                {{ abs($log->change) }} 
                                <span class="text-red-500 font-medium">{{ $log->unit_label ?? 'pcs' }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-700">Rp {{ number_format($log->unit_price, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg font-bold text-sm">
                                <i class="fas fa-plus text-xs"></i>
                                Rp {{ number_format($log->total_value, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-sm text-slate-600">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-500">{{ $log->note ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-inbox text-4xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-500 text-lg font-medium">Belum ada data penjualan</p>
                                <p class="text-slate-400 text-sm mt-1">Transaksi penjualan akan muncul di sini</p>
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
@endsection

@push('scripts')
<script>
    // Auto-refresh untuk update data otomatis setiap 30 detik
    setInterval(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentPage = urlParams.get('page');
        
        if (!currentPage || currentPage === '1') {
            console.log('Memeriksa update data pemasukan...');
            location.reload();
        }
    }, 30000);
    
    console.log('Auto-refresh aktif: Data akan diperbarui setiap 30 detik');
</script>
@endpush
