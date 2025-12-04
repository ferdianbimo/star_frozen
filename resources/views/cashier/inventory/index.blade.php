@extends('layouts.cashier')

@section('title','Inventory')

@section('content')
          
                <!-- Products Table -->
                                <div class="bg-white rounded-lg shadow p-6">
                                    <!-- Tabs -->
                                    <div class="mb-4 border-b">
                                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                            <a href="#" class="py-4 px-1 border-b-2 border-blue-600 text-sm font-medium text-blue-600">Stok Masuk</a>
                                            <a href="{{ route('cashier.inventory.stock-out') }}" class="py-4 px-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700">Stok Keluar</a>
                                        </nav>
                                    </div>

                                    <!-- Search + category + sort + add button -->
                                    <form id="filterForm" method="GET" action="{{ route('cashier.inventory.index') }}" class="flex items-center gap-4 mb-6">
                                        <div class="flex-1 flex items-center gap-3">
                                            <div class="flex-1">
                                                <input id="qInput" type="text" name="q" value="{{ request('q', '') }}" placeholder="Cari produk..." class="w-full border rounded-lg p-3 shadow-sm" autocomplete="off">
                                            </div>
                                            
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <label for="sortSelect" class="text-sm text-gray-600 mr-2">Urutkan:</label>
                                            <select id="sortSelect" name="sort" class="border rounded-lg px-3 py-2 text-sm">
                                                <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Nama (A-Z)</option>
                                                <option value="expiration_asc" {{ request('sort') == 'expiration_asc' ? 'selected' : '' }}>Kadaluarsa (terdekat)</option>
                                                <option value="expiration_desc" {{ request('sort') == 'expiration_desc' ? 'selected' : '' }}>Kadaluarsa (terjauh)</option>
                                                <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stok (rendah → tinggi)</option>
                                                <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stok (tinggi → rendah)</option>
                                            </select>
                                        </div>

                                        <div>
                                            <button type="button" id="showAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Tambah Produk
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Table -->
                                    <div class="bg-white">
                                        <table class="w-full border-collapse">
                                            <thead>
                                                <tr class="text-sm text-gray-500 bg-gray-50">
                                                    <th class="py-3 px-4 text-left">No</th>
                                                    <th class="py-3 px-4 text-left">Nama Produk</th>
                                                    <th class="py-3 px-4 text-left">Kategori</th>
                                                    <th class="py-3 px-4 text-left">Harga Beli</th>
                                                    <th class="py-3 px-4 text-left">Harga Jual</th>
                                                    <th class="py-3 px-4 text-left">Stok</th>
                                                    <th class="py-3 px-4 text-left">Satuan</th>
                                                    <th class="py-3 px-4 text-left">Tanggal Masuk</th>
                                                    <th class="py-3 px-4 text-left">Kadaluarsa</th>
                                                    <th class="py-3 px-4 text-left">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-sm">
                                                @forelse($products as $index => $product)
                                                    <tr class="border-b hover:bg-gray-50">
                                                        <td class="py-4 px-4">{{ $products->firstItem() + $index }}</td>
                                                        <td class="py-4 px-4 col-name">
                                                            <div class="flex items-center">
                                                                @if($product->image)
                                                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover mr-3 rounded">
                                                                @else
                                                                    <div class="w-10 h-10 bg-gray-100 mr-3 rounded flex items-center justify-center text-xs text-gray-400">No</div>
                                                                @endif
                                                                <span>{{ $product->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="py-4 px-4 text-gray-600 col-category">{{ $product->category ?? '-' }}</td>
                                                        <td class="py-4 px-4">Rp {{ number_format($product->purchase_price ?? 0,0,',','.') }}</td>
                                                        <td class="py-4 px-4">Rp {{ number_format($product->price,0,',','.') }}</td>
                                                        <td class="py-4 px-4">{{ $product->stock }}</td>
                                                        <td class="py-4 px-4">{{ $product->unit ?? 'pcs' }}</td>
                                                        <td class="py-4 px-4">{{ $product->date_in ? \Carbon\Carbon::parse($product->date_in)->format('d/m/Y') : '-' }}</td>
                                                        <td class="py-4 px-4">{{ $product->expiration_date ? \Carbon\Carbon::parse($product->expiration_date)->format('d/m/Y') : '-' }}</td>
                                                        <td class="py-4 px-4">
                                                            <button type="button" 
                                                                class="btn-edit text-blue-600 mr-3"
                                                                data-id="{{ $product->id }}"
                                                                data-name="{{ $product->name }}"
                                                                data-description="{{ $product->description ?? '' }}"
                                                                data-category="{{ $product->category ?? '' }}"
                                                                data-price="{{ $product->price ?? '' }}"
                                                                data-purchase_price="{{ $product->purchase_price ?? '' }}"
                                                                data-stock="{{ $product->stock ?? 0 }}"
                                                                data-barcode="{{ $product->barcode ?? '' }}"
                                                                data-unit="{{ $product->unit ?? '' }}"
                                                                data-date_in="{{ $product->date_in ?? '' }}"
                                                                data-expiration_date="{{ $product->expiration_date ?? '' }}"
                                                                data-image="{{ $product->image ? \Illuminate\Support\Facades\Storage::url($product->image) : '' }}"
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
                                                        <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">
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
                                        <div class="mt-6 flex items-center justify-between border-t pt-4">
                                            <div class="text-sm text-gray-600">
                                                Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk
                                            </div>
                                            <div class="flex items-center gap-2">
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
        <div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
                <div class="flex items-center justify-between px-4 py-3 border-b">
                    <h3 class="text-lg font-medium">Tambah Produk Baru</h3>
                    <button id="addModalClose" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <form id="addForm" method="POST" action="{{ route('cashier.inventory.store') }}" class="px-6 py-4" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="from_add" value="1">
                    @if($errors->any() && old('from_add'))
                        <div class="mb-4 rounded-md bg-red-50 border border-red-100 p-3 text-sm text-red-700">
                            <strong>Terdapat error pada input:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input name="name" id="a_name" value="{{ old('name') }}" class="mt-1 block w-full border rounded-md px-3 py-2" required placeholder="Masukkan nama produk" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kategori</label>
                            <input name="category" id="a_category" value="{{ old('category') }}" class="mt-1 block w-full border rounded-md px-3 py-2" placeholder="Pilih kategori" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
                            <input name="purchase_price" id="a_purchase_price" type="number" step="0.01" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('purchase_price', 0) }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                            <input name="price" id="a_price" type="number" step="0.01" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('price', 0) }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok Awal</label>
                            <input name="stock" id="a_stock" type="number" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('stock', 0) }}" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Satuan</label>
                            <input name="unit" id="a_unit" value="{{ old('unit') }}" class="mt-1 block w-full border rounded-md px-3 py-2" placeholder="Pilih satuan" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                            <input name="date_in" id="a_date_in" type="date" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('date_in') }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Kadaluarsa</label>
                            <input name="expiration_date" id="a_expiration_date" type="date" class="mt-1 block w-full border rounded-md px-3 py-2" value="{{ old('expiration_date') }}" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="a_description" rows="3" class="mt-1 block w-full border rounded-md px-3 py-2" placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barcode</label>
                            <input name="barcode" id="a_barcode" value="{{ old('barcode') }}" class="mt-1 block w-full border rounded-md px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                            <div class="mt-1 flex items-center gap-3">
                                <input name="image" id="a_image" type="file" accept="image/*" class="hidden" />
                                <label for="a_image" id="a_image_button" class="inline-flex items-center gap-2 px-3 py-2 bg-white border rounded-md cursor-pointer hover:bg-gray-50 text-sm">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7M16 3l-4 4-4-4"/></svg>
                                    <span>Pilih Gambar</span>
                                </label>
                                <span id="a_image_name" class="text-sm text-gray-600"></span>
                            </div>
                            <div class="mt-2">
                                <img id="a_image_preview" src="" alt="Preview" class="w-20 h-20 object-cover rounded hidden">
                            </div>
                        </div>
                       
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" id="addModalCancel" class="px-4 py-2 bg-gray-100 rounded-md">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl">
                <div class="flex items-center justify-between px-4 py-3 border-b">
                    <h3 class="text-lg font-medium">Edit Produk</h3>
                    <button id="editModalClose" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <form id="editForm" method="POST" action="#" class="px-6 py-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input name="name" id="p_name" class="mt-1 block w-full border rounded-md px-3 py-2" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kategori</label>
                            <input name="category" id="p_category" class="mt-1 block w-full border rounded-md px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                            <input name="price" id="p_price" type="number" step="0.01" class="mt-1 block w-full border rounded-md px-3 py-2" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
                            <input name="purchase_price" id="p_purchase_price" type="number" step="0.01" class="mt-1 block w-full border rounded-md px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stok</label>
                            <input name="stock" id="p_stock" type="number" class="mt-1 block w-full border rounded-md px-3 py-2" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Satuan</label>
                            <input name="unit" id="p_unit" class="mt-1 block w-full border rounded-md px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                            <input name="date_in" id="p_date_in" type="date" class="mt-1 block w-full border rounded-md px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kadaluarsa</label>
                            <input name="expiration_date" id="p_expiration_date" type="date" class="mt-1 block w-full border rounded-md px-3 py-2" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" id="p_description" rows="3" class="mt-1 block w-full border rounded-md px-3 py-2"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barcode</label>
                            <input name="barcode" id="p_barcode" class="mt-1 block w-full border rounded-md px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                            <div class="mt-1 flex items-center gap-3">
                                <input name="image" id="p_image" type="file" accept="image/*" class="hidden" />
                                <label for="p_image" id="p_image_button" class="inline-flex items-center gap-2 px-3 py-2 bg-white border rounded-md cursor-pointer hover:bg-gray-50 text-sm">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7M16 3l-4 4-4-4"/></svg>
                                    <span>Pilih Gambar</span>
                                </label>
                                <span id="p_image_name" class="text-sm text-gray-600"></span>
                            </div>
                            <div class="mt-2">
                                <img id="p_image_preview" src="" alt="Preview" class="w-20 h-20 object-cover rounded hidden">
                            </div>
                        </div>
                    
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" id="editModalCancel" class="px-4 py-2 bg-gray-100 rounded-md">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast container -->
        <div id="toastContainer" class="fixed top-6 right-6 z-50 space-y-3"></div>

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
                        qs('#p_name').value = d.name || '';
                        qs('#p_description').value = d.description || '';
                        qs('#p_category').value = d.category || '';
                        qs('#p_price').value = d.price || '';
                        qs('#p_purchase_price').value = d.purchase_price || '';
                        qs('#p_stock').value = d.stock || 0;
                        qs('#p_barcode').value = d.barcode || '';
                        qs('#p_unit').value = d.unit || '';
                        qs('#p_date_in').value = d.date_in || '';
                        qs('#p_expiration_date').value = d.expiration_date || '';
                        // populate image preview (if any)
                        const pImg = qs('#p_image_preview');
                        const pFileInput = qs('#p_image');
                        if(pFileInput) pFileInput.value = null;
                        if(d.image){
                            pImg.src = d.image;
                            pImg.classList.remove('hidden');
                        } else {
                            pImg.src = '';
                            pImg.classList.add('hidden');
                        }
                        openModal();
                    });
                });

                closeBtn.addEventListener('click', closeModal);
                cancelBtn.addEventListener('click', closeModal);
                // close when clicking outside dialog
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
                    // show save confirmation
                    saveModal.classList.remove('hidden'); saveModal.classList.add('flex');
                });

                saveCancel.addEventListener('click', function(){ saveModal.classList.add('hidden'); saveModal.classList.remove('flex'); });
                saveConfirm.addEventListener('click', function(){ saveModal.classList.add('hidden'); saveModal.classList.remove('flex'); form.submit(); });

                // Toast notifications (reads session flashes)
                (function(){
                    const toastContainer = document.getElementById('toastContainer');
                    const success = @json(session('success'));
                    const error = @json(session('error'));

                    function makeToast(message, type = 'success'){
                        if(!message) return;
                        const colors = {
                            success: ['bg-green-600','text-white'],
                            error: ['bg-red-600','text-white']
                        };
                        const toast = document.createElement('div');
                        toast.className = `max-w-sm w-full flex items-start gap-3 p-3 rounded-lg shadow-lg ${colors[type][0]} ${colors[type][1]}`;
                        toast.innerHTML = `
                            <div class="flex-1 text-sm">${message}</div>
                            <button class="ml-2 text-white close-toast">&times;</button>
                        `;
                        toastContainer.appendChild(toast);
                        const closeBtn = toast.querySelector('.close-toast');
                        const remove = () => { toast.classList.add('opacity-0'); setTimeout(()=> toast.remove(),300); };
                        closeBtn.addEventListener('click', remove);
                        setTimeout(remove, 4200);
                    }

                    if(success) makeToast(success, 'success');
                    if(error) makeToast(error, 'error');
                })();

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
                                    // If server-side validation failed for add form, open modal automatically
                                    @if($errors->any() && old('from_add'))
                                            openAdd();
                                    @endif
                                })();

                                // Image preview handlers for add/edit
                                (function(){
                                    function previewFile(inputEl, previewEl, nameEl, buttonEl){
                                        const file = inputEl.files && inputEl.files[0];
                                        const preview = document.querySelector(previewEl);
                                        const nameSpan = nameEl ? document.querySelector(nameEl) : null;
                                        const button = buttonEl ? document.querySelector(buttonEl) : null;
                                        if(!preview) return;
                                        if(file){
                                            const reader = new FileReader();
                                            reader.onload = function(e){ preview.src = e.target.result; preview.classList.remove('hidden'); };
                                            reader.readAsDataURL(file);
                                            if(nameSpan) nameSpan.textContent = file.name;
                                            if(button) button.querySelector('span').textContent = 'Ganti Gambar';
                                        } else {
                                            preview.src = '';
                                            preview.classList.add('hidden');
                                            if(nameSpan) nameSpan.textContent = '';
                                            if(button) button.querySelector('span').textContent = 'Pilih Gambar';
                                        }
                                    }

                                    const aInput = document.getElementById('a_image');
                                    const pInput = document.getElementById('p_image');
                                    if(aInput){ aInput.addEventListener('change', function(){ previewFile(aInput, '#a_image_preview', '#a_image_name', '#a_image_button'); }); }
                                    if(pInput){ pInput.addEventListener('change', function(){ previewFile(pInput, '#p_image_preview', '#p_image_name', '#p_image_button'); }); }
                                })();

                                // Search + filter: submit only when Enter is pressed for text search.
                                (function(){
                                    const filterForm = document.getElementById('filterForm');
                                    if(!filterForm) return;
                                    const qInput = document.getElementById('qInput');
                                    const categorySelect = document.getElementById('categorySelect');
                                    const sortSelect = document.getElementById('sortSelect');

                                    // Only submit search when user presses Enter (avoid live debounce searches)
                                    if(qInput){
                                        qInput.addEventListener('keydown', function(e){
                                            if(e.key === 'Enter'){
                                                e.preventDefault();
                                                filterForm.submit();
                                            }
                                        });
                                    }

                                    // Keep automatic submit for select changes
                                    if(categorySelect){
                                        categorySelect.addEventListener('change', function(){ filterForm.submit(); });
                                    }
                                    if(sortSelect){
                                        sortSelect.addEventListener('change', function(){ filterForm.submit(); });
                                    }
                                })();


            })();
        </script>

@endsection


