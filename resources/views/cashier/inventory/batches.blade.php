@extends('layouts.cashier')

@section('title','Batch Produk - ' . $product->name)

@section('content')
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('cashier.inventory.index') }}" class="text-blue-600 hover:underline text-sm mb-2 inline-block">
                        ← Kembali ke Inventory
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">Batch Produk: {{ $product->name }}</h2>
                    <p class="text-gray-600">Total stok: {{ $product->stock }} {{ $product->unit ?? 'pcs' }}</p>
                </div>
                <a href="{{ route('cashier.inventory.batch.stock-in') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Batch Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
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
@endsection
