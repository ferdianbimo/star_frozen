<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Produk</h2>
            <a href="{{ route('cashier.inventory.index') }}" class="px-4 py-2 border rounded">Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('cashier.inventory.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-3">
                        <input type="text" name="name" placeholder="Nama Produk" required class="border p-2 rounded">
                        <input type="text" name="category" placeholder="Kategori" class="border p-2 rounded">
                        <input type="number" name="purchase_price" placeholder="Harga Beli" class="border p-2 rounded">
                        <input type="number" name="price" placeholder="Harga Jual" required class="border p-2 rounded">
                        <div class="flex gap-2">
                            <input type="number" name="stock" placeholder="Stok" required class="border p-2 rounded w-1/2">
                            <input type="text" name="unit" placeholder="Satuan (pcs, pack, sack)" class="border p-2 rounded w-1/2">
                        </div>
                        <div class="flex gap-2">
                            <label class="flex-1">
                                <div class="text-sm text-gray-600 mb-1">Tanggal Masuk</div>
                                <input type="date" name="date_in" class="border p-2 rounded w-full">
                            </label>
                            <label class="flex-1">
                                <div class="text-sm text-gray-600 mb-1">Kedaluwarsa</div>
                                <input type="date" name="expiration_date" class="border p-2 rounded w-full">
                            </label>
                        </div>

                        <input type="text" name="barcode" placeholder="Barcode" class="border p-2 rounded">
                        <textarea name="description" placeholder="Deskripsi" class="border p-2 rounded"></textarea>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('cashier.inventory.index') }}" class="px-4 py-2 border rounded">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
