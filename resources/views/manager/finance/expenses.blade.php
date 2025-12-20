@extends('layouts.manager')

@section('title','Kelola Pengeluaran')

@section('content')
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center shadow-lg shadow-red-500/30">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Kelola Pengeluaran</h1>
                        <p class="text-sm text-slate-500">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('manager.finance.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-slate-600/20 hover:shadow-slate-600/30">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <button onclick="openModal('add')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40">
                    <i class="fas fa-plus"></i>
                    Tambah Pengeluaran
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Pengeluaran -->
        <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl shadow-xl shadow-red-500/20 p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-medium backdrop-blur-sm">Total</span>
                </div>
                <p class="text-red-100 text-sm font-medium mb-1">Total Pengeluaran</p>
                <h2 class="text-3xl font-bold">Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</h2>
            </div>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fas fa-file-invoice text-white text-lg"></i>
                    </div>
                </div>
                <p class="text-slate-500 text-sm font-medium mb-1">Jumlah Pengeluaran</p>
                <h2 class="text-3xl font-bold text-slate-800">{{ $expenses->total() }}</h2>
            </div>
        </div>

        <!-- Kategori Terbanyak -->
        <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <i class="fas fa-tags text-white text-lg"></i>
                    </div>
                </div>
                <p class="text-slate-500 text-sm font-medium mb-1">Kategori</p>
                <h2 class="text-lg font-bold text-slate-800">{{ count($categories) }} Kategori</h2>
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
        
        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                        Cari
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fas fa-search text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." 
                               class="w-full pl-11 pr-4 py-3 bg-white border-2 border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all hover:border-slate-300">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">
                        Kategori
                    </label>
                    <div class="relative">
                        <select name="category" class="custom-select w-full appearance-none bg-white border-2 border-slate-200 rounded-xl px-4 py-3 pr-10 text-sm font-medium text-slate-700 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all cursor-pointer hover:border-slate-300">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
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
                @if(request()->hasAny(['search', 'category', 'start_date', 'end_date']))
                <a href="{{ route('manager.finance.expenses') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all">
                    <i class="fas fa-times"></i>
                    Reset Filter
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 flex items-center justify-center">
                        <i class="fas fa-list text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Daftar Pengeluaran</h3>
                </div>
                <span class="px-3 py-1 bg-slate-100 rounded-full text-xs font-medium text-slate-600">
                    {{ $expenses->total() }} Data
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar text-slate-400"></i>
                                Tanggal
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tag text-slate-400"></i>
                                Kategori
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-align-left text-slate-400"></i>
                                Deskripsi
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center justify-end gap-2">
                                <i class="fas fa-money-bill text-slate-400"></i>
                                Jumlah
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-slate-400"></i>
                                Dicatat Oleh
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-cog text-slate-400"></i>
                                Aksi
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-800">{{ $expense->expense_date->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-slate-100 text-slate-700">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">{{ Str::limit($expense->description, 50) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg font-bold text-sm">
                                <i class="fas fa-minus text-xs"></i>
                                Rp {{ number_format($expense->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($expense->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm text-slate-600">{{ $expense->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='editExpense(@json($expense))' class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick='deleteExpense({{ $expense->id }})' class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-receipt text-4xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-500 text-lg font-medium">Tidak ada data pengeluaran</p>
                                <p class="text-slate-400 text-sm mt-1">Klik tombol "Tambah Pengeluaran" untuk menambah data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($expenses->hasPages())
    <div class="mt-6">
        {{ $expenses->links() }}
    </div>
    @endif

    <!-- Modal Add/Edit -->
    <div id="expenseModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <h2 id="modalTitle" class="text-xl font-bold text-slate-800">Tambah Pengeluaran</h2>
                    </div>
                    <button onclick="closeModal()" class="w-9 h-9 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-500 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form id="expenseForm" method="POST" class="p-6">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">
                            <i class="fas fa-tag mr-1 text-slate-400"></i>
                            Kategori
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <select name="category" id="category" required 
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all appearance-none cursor-pointer">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-400 text-sm"></i>
                                </div>
                            </div>
                            <button type="button" onclick="openCategoryModal()" class="px-3 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors" title="Kelola Kategori">
                                <i class="fas fa-cog"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">
                            <i class="fas fa-align-left mr-1 text-slate-400"></i>
                            Deskripsi
                        </label>
                        <textarea name="description" id="description" required rows="3" 
                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all resize-none" 
                                  placeholder="Detail pengeluaran..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">
                            <i class="fas fa-money-bill mr-1 text-slate-400"></i>
                            Jumlah (Rp)
                        </label>
                        <input type="number" name="amount" id="amount" required min="0" step="0.01" 
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all" 
                               placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">
                            <i class="fas fa-calendar mr-1 text-slate-400"></i>
                            Tanggal
                        </label>
                        <input type="date" name="expense_date" id="expense_date" required 
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all">
                    </div>
                </div>
                
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Category Management Modal -->
    <div id="categoryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-tags"></i>
                    Kelola Kategori Pengeluaran
                </h3>
                <button type="button" onclick="closeCategoryModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Add Category Form -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-600 mb-2">Tambah Kategori Baru</label>
                    <div class="flex gap-2">
                        <input type="text" id="newCategoryName" 
                               class="flex-1 px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 focus:bg-white transition-all" 
                               placeholder="Nama kategori baru...">
                        <button type="button" onclick="addCategory()" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Category List -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                        <span class="text-sm font-medium text-slate-600">Daftar Kategori</span>
                    </div>
                    <div id="categoryList" class="max-h-64 overflow-y-auto divide-y divide-slate-100">
                        <!-- Categories will be loaded here -->
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50 px-6 py-4 flex justify-end">
                <button type="button" onclick="closeCategoryModal()" class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-medium transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit Kategori
                </h3>
                <button type="button" onclick="closeEditCategoryModal()" class="text-white/80 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <input type="hidden" id="editCategoryId">
                <label class="block text-sm font-medium text-slate-600 mb-2">Nama Kategori</label>
                <input type="text" id="editCategoryName" 
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 focus:bg-white transition-all" 
                       placeholder="Nama kategori...">
            </div>
            
            <div class="bg-slate-50 px-6 py-4 flex gap-3 justify-end">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-medium transition-all">
                    Batal
                </button>
                <button type="button" onclick="updateCategory()" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl font-medium transition-all">
                    Simpan
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openModal(mode) {
        document.getElementById('expenseModal').classList.remove('hidden');
        if (mode === 'add') {
            document.getElementById('modalTitle').textContent = 'Tambah Pengeluaran';
            document.getElementById('expenseForm').action = '{{ route("manager.finance.expenses.store") }}';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('expenseForm').reset();
            document.getElementById('expense_date').value = new Date().toISOString().split('T')[0];
        }
    }

    function editExpense(expense) {
        document.getElementById('expenseModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Edit Pengeluaran';
        document.getElementById('expenseForm').action = '/manager/finance/expenses/' + expense.id;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('category').value = expense.category;
        document.getElementById('description').value = expense.description;
        document.getElementById('amount').value = expense.amount;
        document.getElementById('expense_date').value = expense.expense_date;
    }

    function closeModal() {
        document.getElementById('expenseModal').classList.add('hidden');
    }

    function deleteExpense(id) {
        confirmAction('Yakin ingin menghapus pengeluaran ini?', function() {
            const form = document.getElementById('deleteForm');
            form.action = '/manager/finance/expenses/' + id;
            form.submit();
        }, {
            title: 'Hapus Pengeluaran',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal'
        });
    }
    
    // Close modal on backdrop click
    document.getElementById('expenseModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Category Management Functions
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.getElementById('categoryModal').classList.add('flex');
        loadCategories();
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.getElementById('categoryModal').classList.remove('flex');
        document.getElementById('newCategoryName').value = '';
    }

    function openEditCategoryModal(id, name) {
        document.getElementById('editCategoryModal').classList.remove('hidden');
        document.getElementById('editCategoryModal').classList.add('flex');
        document.getElementById('editCategoryId').value = id;
        document.getElementById('editCategoryName').value = name;
    }

    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').classList.add('hidden');
        document.getElementById('editCategoryModal').classList.remove('flex');
    }

    async function loadCategories() {
        try {
            const response = await fetch('{{ route("manager.finance.expense-categories.index") }}');
            const categories = await response.json();
            
            const container = document.getElementById('categoryList');
            if (categories.length === 0) {
                container.innerHTML = '<div class="px-4 py-6 text-center text-slate-400 text-sm">Belum ada kategori</div>';
                return;
            }
            
            container.innerHTML = categories.map(cat => `
                <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition-colors">
                    <span class="text-slate-700">${cat.name}</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="openEditCategoryModal(${cat.id}, '${cat.name.replace(/'/g, "\\'")}')" 
                                class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button type="button" onclick="deleteCategory(${cat.id}, '${cat.name.replace(/'/g, "\\'")}')" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    async function addCategory() {
        const name = document.getElementById('newCategoryName').value.trim();
        if (!name) {
            showActionModal('error', 'Error', 'Nama kategori tidak boleh kosong!');
            return;
        }

        try {
            const response = await fetch('{{ route("manager.finance.expense-categories.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name })
            });

            const result = await response.json();
            if (result.success) {
                document.getElementById('newCategoryName').value = '';
                loadCategories();
                refreshCategoryDropdown();
                showActionModal('success', 'Berhasil', result.message);
            } else {
                showActionModal('error', 'Gagal', result.message || 'Gagal menambah kategori');
            }
        } catch (error) {
            console.error('Error adding category:', error);
            showActionModal('error', 'Error', 'Terjadi kesalahan');
        }
    }

    async function updateCategory() {
        const id = document.getElementById('editCategoryId').value;
        const name = document.getElementById('editCategoryName').value.trim();
        if (!name) {
            showActionModal('error', 'Error', 'Nama kategori tidak boleh kosong!');
            return;
        }

        try {
            const response = await fetch(`/manager/finance/expense-categories/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name })
            });

            const result = await response.json();
            if (result.success) {
                closeEditCategoryModal();
                loadCategories();
                refreshCategoryDropdown();
                showActionModal('success', 'Berhasil', result.message);
            } else {
                showActionModal('error', 'Gagal', result.message || 'Gagal mengupdate kategori');
            }
        } catch (error) {
            console.error('Error updating category:', error);
            showActionModal('error', 'Error', 'Terjadi kesalahan');
        }
    }

    function deleteCategory(id, name) {
        confirmAction(`Yakin ingin menghapus kategori "${name}"?`, async function() {
            try {
                const response = await fetch(`/manager/finance/expense-categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const result = await response.json();
                if (result.success) {
                    loadCategories();
                    refreshCategoryDropdown();
                    showActionModal('success', 'Berhasil', result.message);
                } else {
                    showActionModal('error', 'Gagal', result.message || 'Gagal menghapus kategori');
                }
            } catch (error) {
                console.error('Error deleting category:', error);
                showActionModal('error', 'Error', 'Terjadi kesalahan');
            }
        }, {
            title: 'Hapus Kategori',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal'
        });
    }

    async function refreshCategoryDropdown() {
        try {
            const response = await fetch('{{ route("manager.finance.expense-categories.index") }}');
            const categories = await response.json();
            
            const select = document.getElementById('category');
            const currentValue = select.value;
            
            select.innerHTML = '<option value="">Pilih Kategori</option>' + 
                categories.map(cat => `<option value="${cat.name}">${cat.name}</option>`).join('');
            
            // Restore selected value if still exists
            if (categories.some(c => c.name === currentValue)) {
                select.value = currentValue;
            }
        } catch (error) {
            console.error('Error refreshing categories:', error);
        }
    }

    // Close category modal on backdrop click
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCategoryModal();
        }
    });

    document.getElementById('editCategoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditCategoryModal();
        }
    });
</script>
@endpush
