@extends('layouts.cashier')

@section('title','Inventory')

@section('content')
          
                <!-- Modern Header -->
                <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-6 mb-8 shadow-xl">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                <i class="fas fa-warehouse text-2xl text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Inventory</h2>
                                <p class="text-slate-400 text-sm">Kelola stok dan produk</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-slate-700/50 rounded-xl border border-slate-600">
                                <span class="text-slate-400 text-sm"><i class="fas fa-box mr-2"></i>{{ $products->total() ?? 0 }} Produk</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <!-- Tabs -->
                    <div class="border-b border-slate-200 bg-slate-50">
                        <nav class="flex" aria-label="Tabs">
                            <a href="#" class="py-4 px-6 border-b-2 border-blue-600 text-sm font-semibold text-blue-600 bg-white">
                                <i class="fas fa-arrow-down mr-2"></i>Stok Masuk
                            </a>
                            <a href="{{ route('cashier.inventory.stock-out') }}" class="py-4 px-6 border-b-2 border-transparent text-sm font-medium text-slate-500 hover:text-slate-700 hover:bg-white/50 transition-colors">
                                <i class="fas fa-arrow-up mr-2"></i>Stok Keluar
                            </a>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- Search + category + sort + add button -->
                        <form id="filterForm" method="GET" action="{{ route('cashier.inventory.index') }}" class="flex flex-col lg:flex-row flex-wrap items-stretch lg:items-center gap-3 lg:gap-4 mb-6">
                            <div class="flex-1 w-full lg:w-auto">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-slate-400"></i>
                                    </div>
                                    <input id="qInput" type="text" name="q" value="{{ request('q', '') }}" placeholder="Cari produk..." class="w-full border-2 border-slate-200 rounded-xl pl-11 pr-4 py-3 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-700 hover:border-slate-300" autocomplete="off">
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                                <label for="sortSelect" class="text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:block">Urutkan:</label>
                                <div class="relative">
                                    <select id="sortSelect" name="sort" class="custom-select w-full sm:w-auto appearance-none border-2 border-slate-200 rounded-xl px-4 py-2.5 pr-10 bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium text-slate-700 cursor-pointer hover:border-slate-300">
                                        <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Nama (A-Z)</option>
                                        <option value="expiration_asc" {{ request('sort') == 'expiration_asc' ? 'selected' : '' }}>Kadaluarsa (terdekat)</option>
                                        <option value="expiration_desc" {{ request('sort') == 'expiration_desc' ? 'selected' : '' }}>Kadaluarsa (terjauh)</option>
                                        <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stok (rendah → tinggi)</option>
                                        <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stok (tinggi → rendah)</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full lg:w-auto">
                                <a href="{{ route('cashier.inventory.batch.stock-in') }}" class="inline-flex items-center justify-center px-3 lg:px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-emerald-500/30 transition-all text-sm">
                                    <i class="fas fa-boxes mr-1 lg:mr-2"></i>
                                    <span class="hidden sm:inline">Stok Masuk</span><span class="sm:hidden">Batch</span>
                                </a>
                                <button type="button" id="showAddModal" class="inline-flex items-center justify-center px-3 lg:px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all text-sm">
                                    <i class="fas fa-plus mr-1 lg:mr-2"></i>
                                    <span class="hidden sm:inline">Tambah Produk</span><span class="sm:hidden">Tambah</span>
                                </button>
                            </div>
                        </form>

                        <!-- Table -->
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden overflow-x-auto">
                            <table class="w-full min-w-[800px]">
                                <thead>
                                    <tr class="text-xs text-slate-600 bg-slate-50 uppercase tracking-wider">
                                        <th class="py-4 px-4 text-left font-semibold">No</th>
                                        <th class="py-4 px-4 text-left font-semibold">Nama Produk</th>
                                        <th class="py-4 px-4 text-left font-semibold">Kategori</th>
                                        <th class="py-4 px-4 text-left font-semibold">Harga Jual/Pcs</th>
                                        <th class="py-4 px-4 text-left font-semibold">Stok (Pcs)</th>
                                        <th class="py-4 px-4 text-left font-semibold">Satuan Dijual</th>
                                        <th class="py-4 px-4 text-left font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-slate-100">
                                    @forelse($products as $index => $product)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="py-4 px-4 text-slate-500">{{ $products->firstItem() + $index }}</td>
                                            <td class="py-4 px-4 col-name">
                                                <div class="flex items-center gap-3">
                                                    @if($product->image)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                                                    @else
                                                        <div class="w-10 h-10 bg-gradient-to-br from-slate-100 to-slate-200 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-box text-slate-400"></i>
                                                        </div>
                                                    @endif
                                                    <span class="font-medium text-slate-700">{{ $product->name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-slate-600 col-category">
                                                <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs">{{ $product->category ?? '-' }}</span>
                                            </td>
                                            <td class="py-4 px-4 font-semibold text-blue-600">Rp {{ number_format($product->price_pcs ?? $product->price ?? 0,0,',','.') }}</td>
                                            <td class="py-4 px-4">
                                                @php $effectiveStock = $product->effective_stock; @endphp
                                                <span class="px-2 py-1 {{ $effectiveStock <= 10 ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }} rounded-lg text-xs font-semibold">{{ $effectiveStock }}</span>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @if($product->sell_pcs)<span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">Pcs</span>@endif
                                                    @if($product->sell_pack)<span class="px-1.5 py-0.5 bg-teal-100 text-teal-700 rounded text-xs">Pack</span>@endif
                                                    @if($product->sell_renteng)<span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-xs">Renteng</span>@endif
                                                    @if($product->sell_box)<span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-xs">Box</span>@endif
                                                    @if($product->sell_karton)<span class="px-1.5 py-0.5 bg-rose-100 text-rose-700 rounded text-xs">Karton</span>@endif
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="flex items-center gap-1">
                                                    <a href="{{ route('cashier.inventory.batches', $product) }}" 
                                                       class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" 
                                                       title="Lihat Batch">
                                                        <i class="fas fa-boxes"></i>
                                                    </a>
                                                    
                                                    <button type="button" 
                                                        class="btn-edit p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                        data-id="{{ $product->id }}"
                                                        data-name="{{ $product->name }}"
                                                        data-description="{{ $product->description ?? '' }}"
                                                        data-category="{{ $product->category ?? '' }}"
                                                        data-barcode="{{ $product->barcode ?? '' }}"
                                                        data-image="{{ $product->image ? \Illuminate\Support\Facades\Storage::url($product->image) : '' }}"
                                                        data-sell_pcs="{{ $product->sell_pcs ? '1' : '' }}"
                                                        data-sell_pack="{{ $product->sell_pack ? '1' : '' }}"
                                                        data-sell_renteng="{{ $product->sell_renteng ? '1' : '' }}"
                                                        data-sell_box="{{ $product->sell_box ? '1' : '' }}"
                                                        data-sell_karton="{{ $product->sell_karton ? '1' : '' }}"
                                                        data-pcs_per_pack="{{ $product->pcs_per_pack ?? '' }}"
                                                        data-pcs_per_renteng="{{ $product->pcs_per_renteng ?? '' }}"
                                                        data-box_contains_qty="{{ $product->box_contains_qty ?? '' }}"
                                                        data-box_contains_unit="{{ $product->box_contains_unit ?? 'renteng' }}"
                                                        data-karton_contains_qty="{{ $product->karton_contains_qty ?? '' }}"
                                                        data-karton_contains_unit="{{ $product->karton_contains_unit ?? 'box' }}"
                                                        data-price_pcs="{{ $product->price_pcs ?? $product->price ?? '' }}"
                                                        data-price_pack="{{ $product->price_pack ?? '' }}"
                                                        data-price_renteng="{{ $product->price_renteng ?? '' }}"
                                                        data-price_box="{{ $product->price_box ?? '' }}"
                                                        data-price_karton="{{ $product->price_karton ?? '' }}"
                                                        data-purchase_price_pcs="{{ $product->purchase_price_pcs ?? $product->purchase_price ?? '' }}"
                                                        data-purchase_price_pack="{{ $product->purchase_price_pack ?? '' }}"
                                                        data-purchase_price_renteng="{{ $product->purchase_price_renteng ?? '' }}"
                                                        data-purchase_price_box="{{ $product->purchase_price_box ?? '' }}"
                                                        data-purchase_price_karton="{{ $product->purchase_price_karton ?? '' }}"
                                                        data-stock="{{ $product->stock ?? 0 }}"
                                                            ><i class="fas fa-edit"></i></button>

                                                            <form action="{{ route('cashier.inventory.destroy', $product) }}" method="POST" class="inline-block delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 btn-delete"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">
                                                            @if(request('q') || request('category'))
                                                                Produk tidak ditemukan untuk kata kunci atau kategori tersebut.
                                                            @else
                                                                Tidak ada produk tersedia.
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Custom Pagination -->
                                    @if($products->hasPages())
                                        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 border-t pt-4">
                                            <div class="text-sm text-gray-600 text-center sm:text-left">
                                                Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk
                                            </div>
                                            <div class="flex items-center gap-1 sm:gap-2 flex-wrap justify-center">
                                                {{-- Previous Button --}}
                                                @if ($products->onFirstPage())
                                                    <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                                        <i class="fas fa-chevron-left text-xs"></i>
                                                    </span>
                                                @else
                                                    <a href="{{ $products->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                                        <i class="fas fa-chevron-left text-xs"></i>
                                                    </a>
                                                @endif

                                                {{-- Page Numbers --}}
                                                @foreach(range(1, $products->lastPage()) as $page)
                                                    @if($page == $products->currentPage())
                                                        <span class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium">{{ $page }}</span>
                                                    @elseif($page == 1 || $page == $products->lastPage() || abs($page - $products->currentPage()) <= 2)
                                                        <a href="{{ $products->url($page) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">{{ $page }}</a>
                                                    @elseif(abs($page - $products->currentPage()) == 3)
                                                        <span class="px-2 py-2 text-gray-400">...</span>
                                                    @endif
                                                @endforeach

                                                {{-- Next Button --}}
                                                @if ($products->hasMorePages())
                                                    <a href="{{ $products->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                                        <i class="fas fa-chevron-right text-xs"></i>
                                                    </a>
                                                @else
                                                    <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
                                                        <i class="fas fa-chevron-right text-xs"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

        <!-- Add Product Modal -->
        <div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto py-8">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 my-auto">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white">Tambah Produk Baru</h3>
                        </div>
                        <button id="addModalClose" class="text-white/80 hover:text-white text-2xl">&times;</button>
                    </div>
                </div>

                <form id="addForm" method="POST" action="{{ route('cashier.inventory.store') }}" class="p-6" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="from_add" value="1">
                    @if($errors->any() && old('from_add'))
                        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                            <div class="flex items-center gap-2 font-semibold mb-2">
                                <i class="fas fa-exclamation-circle"></i>
                                Terdapat error pada input:
                            </div>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Basic Info -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            Informasi Produk
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                                <input name="name" id="a_name" value="{{ old('name') }}" class="w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all hover:border-slate-300" required placeholder="Masukkan nama produk" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <select name="category" id="a_category" class="custom-select w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 appearance-none bg-white pr-10 transition-all cursor-pointer hover:border-slate-300">
                                            <option value="">Pilih Kategori</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                        </div>
                                    </div>
                                    <button type="button" onclick="openAddCategoryModal('add')" class="px-3 py-2.5 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors" title="Tambah Kategori">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" onclick="openManageCategoryModal()" class="px-3 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors" title="Kelola Kategori">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                                <input name="barcode" id="a_barcode" value="{{ old('barcode') }}" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" placeholder="Scan atau masukkan barcode" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                                <div class="flex items-center gap-3">
                                    <input name="image" id="a_image" type="file" accept="image/*" class="hidden" />
                                    <label for="a_image" id="a_image_button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-200 text-sm transition-colors">
                                        <i class="fas fa-image text-slate-500"></i>
                                        <span>Pilih Gambar</span>
                                    </label>
                                    <span id="a_image_name" class="text-sm text-gray-600"></span>
                                </div>
                                <div class="mt-2 flex items-start gap-2">
                                    <img id="a_image_preview" src="" alt="Preview" class="w-16 h-16 object-cover rounded-lg hidden">
                                    <button type="button" id="a_image_delete" onclick="clearAddImage()" class="hidden p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" title="Hapus Gambar">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" id="a_description" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Unit Configuration Section -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-cubes text-purple-500"></i>
                            Konfigurasi Satuan & Harga
                        </h4>
                        <p class="text-xs text-slate-500 mb-4">Tentukan satuan yang tersedia dan harga untuk setiap satuan. Centang satuan yang akan dijual.</p>
                        
                        <div class="space-y-3">
                            <!-- PCS -->
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_pcs" value="1" checked class="rounded text-blue-600 w-5 h-5">
                                        <span class="text-sm">Pcs (Satuan Dasar)</span>
                                    </label>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">1 Pcs</span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Pcs</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_pcs" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" value="{{ old('purchase_price_pcs') }}" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Pcs</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_pcs" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" value="{{ old('price_pcs') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PACK -->
                            <div class="p-4 bg-gradient-to-r from-teal-50 to-emerald-50 rounded-xl border border-teal-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_pack" value="1" class="rounded text-teal-600 w-5 h-5" onchange="toggleUnitFields('pack', this)">
                                        <span class="text-sm">Pack</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Pack =</span>
                                        <input name="pcs_per_pack" id="a_pcs_per_pack" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" disabled />
                                        <span class="text-slate-500">Pcs</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Pack</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_pack" id="a_purchase_price_pack" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Pack</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_pack" id="a_price_pack" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RENTENG -->
                            <div class="p-4 bg-gradient-to-r from-purple-50 to-fuchsia-50 rounded-xl border border-purple-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_renteng" value="1" class="rounded text-purple-600 w-5 h-5" onchange="toggleUnitFields('renteng', this)">
                                        <span class="text-sm">Renteng</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Renteng =</span>
                                        <input name="pcs_per_renteng" id="a_pcs_per_renteng" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" disabled />
                                        <span class="text-slate-500">Pcs</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Renteng</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_renteng" id="a_purchase_price_renteng" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Renteng</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_renteng" id="a_price_renteng" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BOX -->
                            <div class="p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_box" value="1" class="rounded text-amber-600 w-5 h-5" onchange="toggleUnitFields('box', this)">
                                        <span class="text-sm">Box</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Box =</span>
                                        <input name="box_contains_qty" id="a_box_contains_qty" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" disabled />
                                        <select name="box_contains_unit" id="a_box_contains_unit" class="border border-slate-200 rounded px-2 py-1 text-xs" disabled>
                                            <option value="pcs">Pcs</option>
                                            <option value="pack">Pack</option>
                                            <option value="renteng" selected>Renteng</option>
                                        </select>
                                        <span id="a_box_pcs_equiv" class="text-amber-600 font-medium ml-1"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Box</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_box" id="a_purchase_price_box" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Box</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_box" id="a_price_box" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KARTON -->
                            <div class="p-4 bg-gradient-to-r from-rose-50 to-pink-50 rounded-xl border border-rose-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_karton" value="1" class="rounded text-rose-600 w-5 h-5" onchange="toggleUnitFields('karton', this)">
                                        <span class="text-sm">Karton</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Karton =</span>
                                        <input name="karton_contains_qty" id="a_karton_contains_qty" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" disabled />
                                        <select name="karton_contains_unit" id="a_karton_contains_unit" class="border border-slate-200 rounded px-2 py-1 text-xs" disabled>
                                            <option value="pcs">Pcs</option>
                                            <option value="pack">Pack</option>
                                            <option value="renteng">Renteng</option>
                                            <option value="box" selected>Box</option>
                                        </select>
                                        <span id="a_karton_pcs_equiv" class="text-rose-600 font-medium ml-1"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Karton</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_karton" id="a_purchase_price_karton" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Karton</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_karton" id="a_price_karton" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for compatibility -->
                    <input type="hidden" name="price" value="0">
                    <input type="hidden" name="stock" value="0">

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" id="addModalCancel" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 font-medium transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 overflow-y-auto py-8">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 my-auto">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-t-2xl px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-edit text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white">Edit Produk</h3>
                        </div>
                        <button id="editModalClose" class="text-white/80 hover:text-white text-2xl">&times;</button>
                    </div>
                </div>

                <form id="editForm" method="POST" action="#" class="p-6" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Info -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-emerald-500"></i>
                            Informasi Produk
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                                <input name="name" id="p_name" class="w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all hover:border-slate-300" required placeholder="Masukkan nama produk" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <select name="category" id="p_category" class="custom-select w-full border-2 border-slate-200 rounded-xl px-4 py-2.5 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 appearance-none bg-white pr-10 transition-all cursor-pointer hover:border-slate-300">
                                            <option value="">Pilih Kategori</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                                        </div>
                                    </div>
                                    <button type="button" onclick="openAddCategoryModal('edit')" class="px-3 py-2.5 bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-colors" title="Tambah Kategori">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" onclick="openManageCategoryModal()" class="px-3 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors" title="Kelola Kategori">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                                <input name="barcode" id="p_barcode" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Scan atau masukkan barcode" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>
                                <div class="flex items-center gap-3">
                                    <input name="image" id="p_image" type="file" accept="image/*" class="hidden" />
                                    <input type="hidden" name="remove_image" id="p_remove_image" value="0" />
                                    <label for="p_image" id="p_image_button" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-200 text-sm transition-colors">
                                        <i class="fas fa-image text-slate-500"></i>
                                        <span>Pilih Gambar</span>
                                    </label>
                                    <span id="p_image_name" class="text-sm text-gray-600"></span>
                                </div>
                                <div class="mt-2 flex items-start gap-2">
                                    <img id="p_image_preview" src="" alt="Preview" class="w-16 h-16 object-cover rounded-lg hidden">
                                    <button type="button" id="p_image_delete" onclick="clearEditImage()" class="hidden p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" title="Hapus Gambar">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" id="p_description" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Deskripsi produk (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Unit Configuration Section -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-cubes text-purple-500"></i>
                            Konfigurasi Satuan & Harga
                        </h4>
                        <p class="text-xs text-slate-500 mb-4">Tentukan satuan yang tersedia dan harga untuk setiap satuan. Centang satuan yang akan dijual.</p>
                        
                        <div class="space-y-3">
                            <!-- PCS -->
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_pcs" id="p_sell_pcs" value="1" class="rounded text-blue-600 w-5 h-5">
                                        <span class="text-sm">Pcs (Satuan Dasar)</span>
                                    </label>
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">1 Pcs</span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Pcs</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_pcs" id="p_purchase_price_pcs" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Pcs</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_pcs" id="p_price_pcs" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PACK -->
                            <div class="p-4 bg-gradient-to-r from-teal-50 to-emerald-50 rounded-xl border border-teal-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_pack" id="p_sell_pack" value="1" class="rounded text-teal-600 w-5 h-5" onchange="toggleEditUnitFields('pack', this)">
                                        <span class="text-sm">Pack</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Pack =</span>
                                        <input name="pcs_per_pack" id="p_pcs_per_pack" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" />
                                        <span class="text-slate-500">Pcs</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Pack</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_pack" id="p_purchase_price_pack" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Pack</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_pack" id="p_price_pack" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RENTENG -->
                            <div class="p-4 bg-gradient-to-r from-purple-50 to-fuchsia-50 rounded-xl border border-purple-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_renteng" id="p_sell_renteng" value="1" class="rounded text-purple-600 w-5 h-5" onchange="toggleEditUnitFields('renteng', this)">
                                        <span class="text-sm">Renteng</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Renteng =</span>
                                        <input name="pcs_per_renteng" id="p_pcs_per_renteng" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" />
                                        <span class="text-slate-500">Pcs</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Renteng</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_renteng" id="p_purchase_price_renteng" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Renteng</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_renteng" id="p_price_renteng" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BOX -->
                            <div class="p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_box" id="p_sell_box" value="1" class="rounded text-amber-600 w-5 h-5" onchange="toggleEditUnitFields('box', this)">
                                        <span class="text-sm">Box</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Box =</span>
                                        <input name="box_contains_qty" id="p_box_contains_qty" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" />
                                        <select name="box_contains_unit" id="p_box_contains_unit" class="border border-slate-200 rounded px-2 py-1 text-xs">
                                            <option value="pcs">Pcs</option>
                                            <option value="pack">Pack</option>
                                            <option value="renteng">Renteng</option>
                                        </select>
                                        <span id="p_box_pcs_equiv" class="text-amber-600 font-medium ml-1"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Box</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_box" id="p_purchase_price_box" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Box</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_box" id="p_price_box" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- KARTON -->
                            <div class="p-4 bg-gradient-to-r from-rose-50 to-pink-50 rounded-xl border border-rose-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center gap-2 font-medium text-slate-700">
                                        <input type="checkbox" name="sell_karton" id="p_sell_karton" value="1" class="rounded text-rose-600 w-5 h-5" onchange="toggleEditUnitFields('karton', this)">
                                        <span class="text-sm">Karton</span>
                                    </label>
                                    <div class="flex items-center gap-1 text-xs">
                                        <span class="text-slate-500">1 Karton =</span>
                                        <input name="karton_contains_qty" id="p_karton_contains_qty" type="number" min="1" class="w-14 border border-slate-200 rounded px-2 py-1 text-center text-xs" placeholder="0" />
                                        <select name="karton_contains_unit" id="p_karton_contains_unit" class="border border-slate-200 rounded px-2 py-1 text-xs">
                                            <option value="pcs">Pcs</option>
                                            <option value="pack">Pack</option>
                                            <option value="renteng">Renteng</option>
                                            <option value="box">Box</option>
                                        </select>
                                        <span id="p_karton_pcs_equiv" class="text-rose-600 font-medium ml-1"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Beli/Karton</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="purchase_price_karton" id="p_purchase_price_karton" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-slate-600 mb-1">Harga Jual/Karton</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs text-slate-400">Rp</span>
                                            <input name="price_karton" id="p_price_karton" type="number" step="0.01" class="w-full border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm" placeholder="0" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for compatibility -->
                    <input type="hidden" name="price" id="p_price" value="0">
                    <input type="hidden" name="stock" id="p_stock" value="0">

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" id="editModalCancel" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 font-medium transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-emerald-500/30 transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div id="addCategoryModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tag text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white">Tambah Kategori Baru</h3>
                        </div>
                        <button onclick="closeAddCategoryModal()" class="text-white/80 hover:text-white text-2xl">&times;</button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" id="new_category_name" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" placeholder="Masukkan nama kategori" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                        <textarea id="new_category_desc" rows="2" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" placeholder="Deskripsi kategori"></textarea>
                    </div>
                    <div id="addCategoryError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"></div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeAddCategoryModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 font-medium transition-colors">Batal</button>
                        <button type="button" onclick="saveNewCategory()" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:shadow-lg hover:shadow-blue-500/30 transition-all flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Tambah Kategori
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Categories Modal -->
        <div id="manageCategoryModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4">
                <div class="bg-gradient-to-r from-slate-600 to-slate-700 rounded-t-2xl px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-tags text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white">Kelola Kategori</h3>
                        </div>
                        <button onclick="closeManageCategoryModal()" class="text-white/80 hover:text-white text-2xl">&times;</button>
                    </div>
                </div>
                <div class="p-6">
                    <div id="categoryList" class="max-h-80 overflow-y-auto space-y-2 mb-4">
                        <!-- Categories will be loaded here -->
                        <div class="text-center py-4 text-slate-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Memuat kategori...
                        </div>
                    </div>
                    <div id="manageCategoryError" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"></div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" onclick="closeManageCategoryModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 font-medium transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function(){
                function qs(sel, ctx){ return (ctx || document).querySelector(sel); }
                function qsa(sel, ctx){ return Array.from((ctx || document).querySelectorAll(sel)); }

                const modal = document.getElementById('editModal');
                const form = document.getElementById('editForm');
                const closeBtn = document.getElementById('editModalClose');
                const cancelBtn = document.getElementById('editModalCancel');

                function openModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
                function closeModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }

                qsa('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', function(){
                        const d = this.dataset;
                        const id = d.id;
                        form.action = '/cashier/inventory/' + id;
                        
                        // Basic info
                        qs('#p_name').value = d.name || '';
                        qs('#p_description').value = d.description || '';
                        qs('#p_category').value = d.category || '';
                        qs('#p_barcode').value = d.barcode || '';
                        qs('#p_stock').value = d.stock || 0;
                        qs('#p_price').value = d.price_pcs || 0;
                        
                        // Unit sell checkboxes
                        qs('#p_sell_pcs').checked = d.sell_pcs === '1';
                        qs('#p_sell_pack').checked = d.sell_pack === '1';
                        qs('#p_sell_renteng').checked = d.sell_renteng === '1';
                        qs('#p_sell_box').checked = d.sell_box === '1';
                        qs('#p_sell_karton').checked = d.sell_karton === '1';
                        
                        // Unit conversions
                        qs('#p_pcs_per_pack').value = d.pcs_per_pack || '';
                        qs('#p_pcs_per_renteng').value = d.pcs_per_renteng || '';
                        qs('#p_box_contains_qty').value = d.box_contains_qty || '';
                        qs('#p_box_contains_unit').value = d.box_contains_unit || 'renteng';
                        qs('#p_karton_contains_qty').value = d.karton_contains_qty || '';
                        qs('#p_karton_contains_unit').value = d.karton_contains_unit || 'box';
                        
                        // Prices
                        qs('#p_price_pcs').value = d.price_pcs || '';
                        qs('#p_price_pack').value = d.price_pack || '';
                        qs('#p_price_renteng').value = d.price_renteng || '';
                        qs('#p_price_box').value = d.price_box || '';
                        qs('#p_price_karton').value = d.price_karton || '';
                        
                        // Purchase prices
                        qs('#p_purchase_price_pcs').value = d.purchase_price_pcs || '';
                        qs('#p_purchase_price_pack').value = d.purchase_price_pack || '';
                        qs('#p_purchase_price_renteng').value = d.purchase_price_renteng || '';
                        qs('#p_purchase_price_box').value = d.purchase_price_box || '';
                        qs('#p_purchase_price_karton').value = d.purchase_price_karton || '';
                        
                        // Image preview
                        const pImg = qs('#p_image_preview');
                        const pFileInput = qs('#p_image');
                        const pDeleteBtn = qs('#p_image_delete');
                        const pRemoveInput = qs('#p_remove_image');
                        if(pFileInput) pFileInput.value = null;
                        if(pRemoveInput) pRemoveInput.value = '0';
                        if(d.image){
                            pImg.src = d.image;
                            pImg.classList.remove('hidden');
                            if(pDeleteBtn) pDeleteBtn.classList.remove('hidden');
                        } else {
                            pImg.src = '';
                            pImg.classList.add('hidden');
                            if(pDeleteBtn) pDeleteBtn.classList.add('hidden');
                        }
                        
                        // Update pcs equivalents
                        updateEditPcsEquivalents();
                        
                        openModal();
                    });
                });

                closeBtn.addEventListener('click', closeModal);
                cancelBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

                // Delete confirmation modal handling
                const deleteModalHtml = `
                    <div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                            <h3 class="text-lg font-medium mb-4">Yakin hapus produk ini?</h3>
                            <p class="text-sm text-gray-600 mb-6">Tindakan ini tidak bisa dibatalkan.</p>
                            <div class="flex justify-end gap-2">
                                <button id="deleteCancel" class="px-4 py-2 bg-gray-100 rounded-md">Batal</button>
                                <button id="deleteConfirm" class="px-4 py-2 bg-red-600 text-white rounded-md">Ya, Hapus</button>
                            </div>
                        </div>
                    </div>`;

                document.body.insertAdjacentHTML('beforeend', deleteModalHtml);
                const deleteModal = document.getElementById('deleteConfirmModal');
                const deleteCancel = document.getElementById('deleteCancel');
                const deleteConfirm = document.getElementById('deleteConfirm');
                let pendingDeleteForm = null;

                qsa('.delete-form').forEach(f => {
                    f.addEventListener('submit', function(e){
                        e.preventDefault();
                        pendingDeleteForm = this;
                        deleteModal.classList.remove('hidden'); deleteModal.classList.add('flex');
                    });
                });

                deleteCancel.addEventListener('click', function(){ deleteModal.classList.add('hidden'); deleteModal.classList.remove('flex'); pendingDeleteForm = null; });
                deleteConfirm.addEventListener('click', function(){ if(pendingDeleteForm){ pendingDeleteForm.submit(); } });

                // Edit form save confirmation
                const saveModalHtml = `
                    <div id="saveConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-40">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                            <h3 class="text-lg font-medium mb-4">Simpan perubahan?</h3>
                            <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menyimpan perubahan pada produk ini?</p>
                            <div class="flex justify-end gap-2">
                                <button id="saveCancel" class="px-4 py-2 bg-gray-100 rounded-md">Tidak</button>
                                <button id="saveConfirm" class="px-4 py-2 bg-blue-600 text-white rounded-md">Ya, Simpan</button>
                            </div>
                        </div>
                    </div>`;

                document.body.insertAdjacentHTML('beforeend', saveModalHtml);
                const saveModal = document.getElementById('saveConfirmModal');
                const saveCancel = document.getElementById('saveCancel');
                const saveConfirm = document.getElementById('saveConfirm');

                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    saveModal.classList.remove('hidden'); saveModal.classList.add('flex');
                });

                saveCancel.addEventListener('click', function(){ saveModal.classList.add('hidden'); saveModal.classList.remove('flex'); });
                saveConfirm.addEventListener('click', function(){ saveModal.classList.add('hidden'); saveModal.classList.remove('flex'); form.submit(); });

                // Add modal handlers
                (function(){
                    const showAdd = document.getElementById('showAddModal');
                    const addModal = document.getElementById('addModal');
                    const addClose = document.getElementById('addModalClose');
                    const addCancel = document.getElementById('addModalCancel');
                    if(!showAdd || !addModal) return;
                    function openAdd(){ addModal.classList.remove('hidden'); addModal.classList.add('flex'); }
                    function closeAdd(){ addModal.classList.add('hidden'); addModal.classList.remove('flex'); }
                    showAdd.addEventListener('click', openAdd);
                    addClose.addEventListener('click', closeAdd);
                    addCancel.addEventListener('click', closeAdd);
                    addModal.addEventListener('click', function(e){ if(e.target === addModal) closeAdd(); });
                    @if($errors->any() && old('from_add'))
                            openAdd();
                    @endif
                })();

                // Image preview handlers
                (function(){
                    function previewFile(inputEl, previewEl, nameEl, buttonEl, deleteEl){
                        const file = inputEl.files && inputEl.files[0];
                        const preview = document.querySelector(previewEl);
                        const nameSpan = nameEl ? document.querySelector(nameEl) : null;
                        const button = buttonEl ? document.querySelector(buttonEl) : null;
                        const deleteBtn = deleteEl ? document.querySelector(deleteEl) : null;
                        if(!preview) return;
                        if(file){
                            const reader = new FileReader();
                            reader.onload = function(e){ preview.src = e.target.result; preview.classList.remove('hidden'); };
                            reader.readAsDataURL(file);
                            if(nameSpan) nameSpan.textContent = file.name;
                            if(button) button.querySelector('span').textContent = 'Ganti Gambar';
                            if(deleteBtn) deleteBtn.classList.remove('hidden');
                        } else {
                            preview.src = '';
                            preview.classList.add('hidden');
                            if(nameSpan) nameSpan.textContent = '';
                            if(button) button.querySelector('span').textContent = 'Pilih Gambar';
                            if(deleteBtn) deleteBtn.classList.add('hidden');
                        }
                    }

                    const aInput = document.getElementById('a_image');
                    const pInput = document.getElementById('p_image');
                    if(aInput){ aInput.addEventListener('change', function(){ previewFile(aInput, '#a_image_preview', '#a_image_name', '#a_image_button', '#a_image_delete'); }); }
                    if(pInput){ pInput.addEventListener('change', function(){ previewFile(pInput, '#p_image_preview', '#p_image_name', '#p_image_button', '#p_image_delete'); }); }
                })();

                // Clear image functions
                window.clearAddImage = function() {
                    const input = document.getElementById('a_image');
                    const preview = document.getElementById('a_image_preview');
                    const nameSpan = document.getElementById('a_image_name');
                    const button = document.getElementById('a_image_button');
                    const deleteBtn = document.getElementById('a_image_delete');
                    
                    if(input) input.value = '';
                    if(preview) { preview.src = ''; preview.classList.add('hidden'); }
                    if(nameSpan) nameSpan.textContent = '';
                    if(button) button.querySelector('span').textContent = 'Pilih Gambar';
                    if(deleteBtn) deleteBtn.classList.add('hidden');
                };

                window.clearEditImage = function() {
                    const input = document.getElementById('p_image');
                    const preview = document.getElementById('p_image_preview');
                    const nameSpan = document.getElementById('p_image_name');
                    const button = document.getElementById('p_image_button');
                    const deleteBtn = document.getElementById('p_image_delete');
                    const removeInput = document.getElementById('p_remove_image');
                    
                    if(input) input.value = '';
                    if(preview) { preview.src = ''; preview.classList.add('hidden'); }
                    if(nameSpan) nameSpan.textContent = '';
                    if(button) button.querySelector('span').textContent = 'Pilih Gambar';
                    if(deleteBtn) deleteBtn.classList.add('hidden');
                    if(removeInput) removeInput.value = '1';
                };

                // Search + filter
                (function(){
                    const filterForm = document.getElementById('filterForm');
                    if(!filterForm) return;
                    const qInput = document.getElementById('qInput');
                    const categorySelect = document.getElementById('categorySelect');
                    const sortSelect = document.getElementById('sortSelect');

                    if(qInput){
                        qInput.addEventListener('keydown', function(e){
                            if(e.key === 'Enter'){
                                e.preventDefault();
                                filterForm.submit();
                            }
                        });
                    }

                    if(categorySelect){
                        categorySelect.addEventListener('change', function(){ filterForm.submit(); });
                    }
                    if(sortSelect){
                        sortSelect.addEventListener('change', function(){ filterForm.submit(); });
                    }
                })();

                // Unit toggle functions for Add modal
                window.toggleUnitFields = function(unit, checkbox) {
                    const fields = {
                        'pack': ['a_pcs_per_pack', 'a_purchase_price_pack', 'a_price_pack'],
                        'renteng': ['a_pcs_per_renteng', 'a_purchase_price_renteng', 'a_price_renteng'],
                        'box': ['a_box_contains_qty', 'a_box_contains_unit', 'a_purchase_price_box', 'a_price_box'],
                        'karton': ['a_karton_contains_qty', 'a_karton_contains_unit', 'a_purchase_price_karton', 'a_price_karton']
                    };
                    
                    if (fields[unit]) {
                        fields[unit].forEach(fieldId => {
                            const input = document.getElementById(fieldId);
                            if (input) {
                                input.disabled = !checkbox.checked;
                                if (!checkbox.checked) {
                                    input.value = '';
                                }
                            }
                        });
                    }
                    updateAddPcsEquivalents();
                };
                
                // Unit toggle functions for Edit modal
                window.toggleEditUnitFields = function(unit, checkbox) {
                    updateEditPcsEquivalents();
                };
                
                // Calculate and display pcs equivalents for Add modal
                function updateAddPcsEquivalents() {
                    const pcsPerPack = parseInt(document.getElementById('a_pcs_per_pack')?.value) || 0;
                    const pcsPerRenteng = parseInt(document.getElementById('a_pcs_per_renteng')?.value) || 0;
                    const boxQty = parseInt(document.getElementById('a_box_contains_qty')?.value) || 0;
                    const boxUnit = document.getElementById('a_box_contains_unit')?.value || 'pcs';
                    const kartonQty = parseInt(document.getElementById('a_karton_contains_qty')?.value) || 0;
                    const kartonUnit = document.getElementById('a_karton_contains_unit')?.value || 'box';
                    
                    // Calculate box pcs equivalent
                    let boxPcs = 0;
                    if (boxUnit === 'pcs') boxPcs = boxQty;
                    else if (boxUnit === 'pack') boxPcs = boxQty * pcsPerPack;
                    else if (boxUnit === 'renteng') boxPcs = boxQty * pcsPerRenteng;
                    
                    const boxEquivEl = document.getElementById('a_box_pcs_equiv');
                    if (boxEquivEl) {
                        boxEquivEl.textContent = boxPcs > 0 ? `= ${boxPcs} Pcs` : '';
                    }
                    
                    // Calculate karton pcs equivalent
                    let kartonPcs = 0;
                    if (kartonUnit === 'pcs') kartonPcs = kartonQty;
                    else if (kartonUnit === 'pack') kartonPcs = kartonQty * pcsPerPack;
                    else if (kartonUnit === 'renteng') kartonPcs = kartonQty * pcsPerRenteng;
                    else if (kartonUnit === 'box') kartonPcs = kartonQty * boxPcs;
                    
                    const kartonEquivEl = document.getElementById('a_karton_pcs_equiv');
                    if (kartonEquivEl) {
                        kartonEquivEl.textContent = kartonPcs > 0 ? `= ${kartonPcs} Pcs` : '';
                    }
                }
                
                // Calculate and display pcs equivalents for Edit modal
                function updateEditPcsEquivalents() {
                    const pcsPerPack = parseInt(document.getElementById('p_pcs_per_pack')?.value) || 0;
                    const pcsPerRenteng = parseInt(document.getElementById('p_pcs_per_renteng')?.value) || 0;
                    const boxQty = parseInt(document.getElementById('p_box_contains_qty')?.value) || 0;
                    const boxUnit = document.getElementById('p_box_contains_unit')?.value || 'pcs';
                    const kartonQty = parseInt(document.getElementById('p_karton_contains_qty')?.value) || 0;
                    const kartonUnit = document.getElementById('p_karton_contains_unit')?.value || 'box';
                    
                    // Calculate box pcs equivalent
                    let boxPcs = 0;
                    if (boxUnit === 'pcs') boxPcs = boxQty;
                    else if (boxUnit === 'pack') boxPcs = boxQty * pcsPerPack;
                    else if (boxUnit === 'renteng') boxPcs = boxQty * pcsPerRenteng;
                    
                    const boxEquivEl = document.getElementById('p_box_pcs_equiv');
                    if (boxEquivEl) {
                        boxEquivEl.textContent = boxPcs > 0 ? `= ${boxPcs} Pcs` : '';
                    }
                    
                    // Calculate karton pcs equivalent
                    let kartonPcs = 0;
                    if (kartonUnit === 'pcs') kartonPcs = kartonQty;
                    else if (kartonUnit === 'pack') kartonPcs = kartonQty * pcsPerPack;
                    else if (kartonUnit === 'renteng') kartonPcs = kartonQty * pcsPerRenteng;
                    else if (kartonUnit === 'box') kartonPcs = kartonQty * boxPcs;
                    
                    const kartonEquivEl = document.getElementById('p_karton_pcs_equiv');
                    if (kartonEquivEl) {
                        kartonEquivEl.textContent = kartonPcs > 0 ? `= ${kartonPcs} Pcs` : '';
                    }
                }
                
                // Add event listeners for dynamic calculation - Add modal
                ['a_pcs_per_pack', 'a_pcs_per_renteng', 'a_box_contains_qty', 'a_box_contains_unit', 'a_karton_contains_qty', 'a_karton_contains_unit'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', updateAddPcsEquivalents);
                        el.addEventListener('change', updateAddPcsEquivalents);
                    }
                });
                
                // Add event listeners for dynamic calculation - Edit modal
                ['p_pcs_per_pack', 'p_pcs_per_renteng', 'p_box_contains_qty', 'p_box_contains_unit', 'p_karton_contains_qty', 'p_karton_contains_unit'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', updateEditPcsEquivalents);
                        el.addEventListener('change', updateEditPcsEquivalents);
                    }
                });

            })();
            
            // ============================================
            // Category Management Functions
            // ============================================
            
            let currentCategoryContext = 'add'; // 'add' or 'edit' - to know which dropdown to update
            let allCategories = [];
            
            // Load categories on page load
            document.addEventListener('DOMContentLoaded', function() {
                loadCategories();
            });
            
            // Fetch categories from API
            async function loadCategories() {
                try {
                    const response = await fetch('/cashier/api/categories');
                    const data = await response.json();
                    allCategories = data.categories || [];
                    populateCategoryDropdowns();
                } catch (error) {
                    console.error('Error loading categories:', error);
                }
            }
            
            // Populate both category dropdowns
            function populateCategoryDropdowns(selectedValue = null) {
                const addSelect = document.getElementById('a_category');
                const editSelect = document.getElementById('p_category');
                
                const optionsHtml = '<option value="">Pilih Kategori</option>' + 
                    allCategories.map(cat => `<option value="${cat.name}">${cat.name}</option>`).join('');
                
                if (addSelect) {
                    const currentAddValue = addSelect.value;
                    addSelect.innerHTML = optionsHtml;
                    if (selectedValue && currentCategoryContext === 'add') {
                        addSelect.value = selectedValue;
                    } else if (currentAddValue) {
                        addSelect.value = currentAddValue;
                    }
                }
                
                if (editSelect) {
                    const currentEditValue = editSelect.value;
                    editSelect.innerHTML = optionsHtml;
                    if (selectedValue && currentCategoryContext === 'edit') {
                        editSelect.value = selectedValue;
                    } else if (currentEditValue) {
                        editSelect.value = currentEditValue;
                    }
                }
            }
            
            // Open Add Category Modal
            function openAddCategoryModal(context = 'add') {
                currentCategoryContext = context;
                document.getElementById('new_category_name').value = '';
                document.getElementById('new_category_desc').value = '';
                document.getElementById('addCategoryError').classList.add('hidden');
                
                const modal = document.getElementById('addCategoryModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.getElementById('new_category_name').focus();
            }
            
            // Close Add Category Modal
            function closeAddCategoryModal() {
                const modal = document.getElementById('addCategoryModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            
            // Save new category
            async function saveNewCategory() {
                const name = document.getElementById('new_category_name').value.trim();
                const description = document.getElementById('new_category_desc').value.trim();
                const errorEl = document.getElementById('addCategoryError');
                
                if (!name) {
                    errorEl.textContent = 'Nama kategori wajib diisi!';
                    errorEl.classList.remove('hidden');
                    return;
                }
                
                try {
                    const response = await fetch('/cashier/api/categories', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ name, description })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok) {
                        closeAddCategoryModal();
                        await loadCategories();
                        // Select the new category in the appropriate dropdown
                        populateCategoryDropdowns(name);
                    } else {
                        errorEl.textContent = data.message || 'Gagal menambahkan kategori';
                        errorEl.classList.remove('hidden');
                    }
                } catch (error) {
                    errorEl.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                    errorEl.classList.remove('hidden');
                }
            }
            
            // Open Manage Categories Modal
            function openManageCategoryModal() {
                const modal = document.getElementById('manageCategoryModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                renderCategoryList();
            }
            
            // Close Manage Categories Modal
            function closeManageCategoryModal() {
                const modal = document.getElementById('manageCategoryModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            
            // Render category list in manage modal
            function renderCategoryList() {
                const container = document.getElementById('categoryList');
                const errorEl = document.getElementById('manageCategoryError');
                errorEl.classList.add('hidden');
                
                if (allCategories.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-slate-500">
                            <i class="fas fa-folder-open text-4xl mb-3"></i>
                            <p>Belum ada kategori</p>
                            <button onclick="openAddCategoryModal('add')" class="mt-3 text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-plus mr-1"></i> Tambah Kategori
                            </button>
                        </div>
                    `;
                    return;
                }
                
                container.innerHTML = allCategories.map(cat => `
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tag text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-700">${cat.name}</p>
                                ${cat.description ? `<p class="text-xs text-slate-500">${cat.description}</p>` : ''}
                                <p class="text-xs text-slate-400">${cat.products_count || 0} produk</p>
                            </div>
                        </div>
                        <button onclick="deleteCategory(${cat.id}, '${cat.name}')" class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors" title="Hapus Kategori">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                `).join('');
            }
            
            // Delete category
            async function deleteCategory(id, name) {
                const errorEl = document.getElementById('manageCategoryError');
                
                confirmAction(`Apakah Anda yakin ingin menghapus kategori "${name}"?`, async function() {
                    try {
                        const response = await fetch(`/cashier/api/categories/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            await loadCategories();
                            renderCategoryList();
                            showActionModal('success', 'Kategori berhasil dihapus');
                        } else {
                            errorEl.textContent = data.message || 'Gagal menghapus kategori';
                            errorEl.classList.remove('hidden');
                        }
                    } catch (error) {
                        errorEl.textContent = 'Terjadi kesalahan saat menghapus kategori';
                        errorEl.classList.remove('hidden');
                    }
                }, {
                    title: 'Hapus Kategori',
                    confirmText: 'Ya, Hapus'
                });
            }
        </script>

@endsection


