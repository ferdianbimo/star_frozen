@extends('layouts.cashier')

@section('title','Batch Produk - ' . $product->name)

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('cashier.inventory.index') }}" class="text-blue-600 hover:underline text-sm mb-2 inline-block">
                        ← Kembali ke Inventory
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">Batch Produk: {{ $product->name }}</h2>
                    <p class="text-gray-600">Total stok: {{ $product->effective_stock }} {{ $product->unit ?? 'pcs' }}</p>
                </div>
                <a href="{{ route('cashier.inventory.batch.stock-in') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Batch Baru
                </a>
            </div>
        </div>

        <!-- Batches Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-sm text-gray-500 bg-gray-50">
                        <th class="py-3 px-4 text-left">Kode Batch</th>
                        <th class="py-3 px-4 text-left">Tanggal Diterima</th>
                        <th class="py-3 px-4 text-left">Tanggal Kadaluarsa</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-left">Stok Tersisa</th>
                        <th class="py-3 px-4 text-left">Harga Beli</th>
                        <th class="py-3 px-4 text-left">Diterima Oleh</th>
                        <th class="py-3 px-4 text-left">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($batches as $batch)
                        @php
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusText = 'Aktif';
                            
                            if ($batch->isExpired()) {
                                $statusClass = 'bg-red-100 text-red-800';
                                $statusText = 'Kadaluarsa';
                            } elseif ($batch->isExpiringSoon()) {
                                $statusClass = 'bg-orange-100 text-orange-800';
                                $statusText = 'Segera Kadaluarsa';
                            } elseif ($batch->quantity <= 0) {
                                $statusClass = 'bg-gray-100 text-gray-800';
                                $statusText = 'Habis';
                            }
                        @endphp
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-4 px-4 font-medium">{{ $batch->batch_code }}</td>
                            <td class="py-4 px-4">{{ $batch->date_received->format('d M Y') }}</td>
                            <td class="py-4 px-4">
                                @if($batch->expiration_date)
                                    {{ $batch->expiration_date->format('d M Y') }}
                                    @if($batch->daysUntilExpiration() !== null)
                                        <div class="text-xs text-gray-500">
                                            @if($batch->daysUntilExpiration() < 0)
                                                ({{ abs($batch->daysUntilExpiration()) }} hari lalu)
                                            @elseif($batch->daysUntilExpiration() == 0)
                                                (Hari ini)
                                            @else
                                                ({{ $batch->daysUntilExpiration() }} hari lagi)
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-semibold">{{ $batch->quantity }}</td>
                            <td class="py-4 px-4">
                                @if($batch->purchase_price)
                                    Rp {{ number_format($batch->purchase_price, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">{{ $batch->receivedBy ? $batch->receivedBy->name : '-' }}</td>
                            <td class="py-4 px-4">{{ $batch->notes ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button onclick="openEditModal({{ $batch->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <button onclick="confirmDelete({{ $batch->id }}, '{{ $batch->batch_code }}', {{ $batch->quantity }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p>Belum ada batch untuk produk ini</p>
                                <a href="{{ route('cashier.inventory.batch.stock-in') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                                    Tambah batch pertama →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($batches->hasPages())
            <div class="mt-4">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    <!-- Edit Batch Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800">Edit Batch</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form id="editBatchForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Batch</label>
                        <input type="text" id="edit_batch_code" name="batch_code" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Diterima</label>
                            <input type="date" id="edit_date_received" name="date_received" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kadaluarsa</label>
                            <input type="date" id="edit_expiration_date" name="expiration_date" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kuantitas</label>
                            <input type="number" id="edit_quantity" name="quantity" min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli</label>
                            <input type="number" id="edit_purchase_price" name="purchase_price" min="0" step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea id="edit_notes" name="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                
                <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 text-center mb-2">Hapus Batch?</h3>
                <p class="text-gray-600 text-center mb-2">
                    Apakah Anda yakin ingin menghapus batch <strong id="deleteBatchCode"></strong>?
                </p>
                <p id="stockWarning" class="hidden text-center mb-4">
                    <span class="inline-flex items-center px-3 py-2 bg-orange-100 text-orange-800 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Batch ini masih memiliki <strong id="stockQuantity"></strong> unit stok yang akan dihapus
                    </span>
                </p>
                <p class="text-gray-500 text-center text-sm mb-6">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                
                <form id="deleteBatchForm" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteModal()" 
                                class="flex-1 px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Store batch data for editing
        const batchData = {};
        
        @foreach($batches as $batch)
            batchData[{{ $batch->id }}] = {
                id: {{ $batch->id }},
                batch_code: "{{ $batch->batch_code }}",
                date_received: "{{ $batch->date_received->format('Y-m-d') }}",
                expiration_date: "{{ $batch->expiration_date ? $batch->expiration_date->format('Y-m-d') : '' }}",
                quantity: {{ $batch->quantity }},
                purchase_price: {{ $batch->purchase_price ?? 0 }},
                notes: "{{ addslashes($batch->notes ?? '') }}"
            };
        @endforeach
        
        function openEditModal(batchId) {
            const batch = batchData[batchId];
            if (!batch) return;
            
            document.getElementById('edit_batch_code').value = batch.batch_code || '';
            document.getElementById('edit_date_received').value = batch.date_received || '';
            document.getElementById('edit_expiration_date').value = batch.expiration_date || '';
            document.getElementById('edit_quantity').value = batch.quantity || 0;
            document.getElementById('edit_purchase_price').value = batch.purchase_price || 0;
            document.getElementById('edit_notes').value = batch.notes || '';
            
            const form = document.getElementById('editBatchForm');
            form.action = `/cashier/inventory/batch/${batchId}`;
            
            document.getElementById('editModal').classList.remove('hidden');
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        
        function confirmDelete(batchId, batchCode, quantity) {
            document.getElementById('deleteBatchCode').textContent = batchCode;
            
            // Show/hide stock warning based on quantity
            const stockWarning = document.getElementById('stockWarning');
            const stockQuantity = document.getElementById('stockQuantity');
            
            if (quantity > 0) {
                stockQuantity.textContent = quantity;
                stockWarning.classList.remove('hidden');
            } else {
                stockWarning.classList.add('hidden');
            }
            
            const form = document.getElementById('deleteBatchForm');
            form.action = `/cashier/inventory/batch/${batchId}`;
            
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
        
        // Close modals when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
        
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
        
        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>
@endsection
