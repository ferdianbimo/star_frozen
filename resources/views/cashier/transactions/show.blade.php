@extends('layouts.cashier')

@section('title', 'Detail Transaksi')

@section('content')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Transaction Info -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6 mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Transaksi
                            </h2>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">ID Transaksi</p>
                                    <p class="text-base font-mono font-medium text-gray-900">#{{ $transaction->id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Invoice Number</p>
                                    <p class="text-base font-medium text-blue-600">{{ $transaction->invoice_number ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Tanggal & Waktu</p>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Kasir</p>
                                    <p class="text-base font-medium text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                                    <i class="fas fa-shopping-cart mr-2 text-green-500"></i> Daftar Item
                                </h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($transaction->items as $index => $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    @if($item->product && $item->product->image)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->image) }}" 
                                                             alt="{{ $item->product->name ?? 'Product' }}" 
                                                             class="h-10 w-10 rounded object-cover mr-3">
                                                    @else
                                                        <div class="h-10 w-10 rounded bg-gray-200 mr-3 flex items-center justify-center">
                                                            <i class="fas fa-box text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Product #'.$item->product_id }}</div>
                                                        <div class="text-xs text-gray-500">{{ $item->product->category ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $item->quantity }} pack
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calculator mr-2 text-purple-500"></i> Ringkasan
                            </h2>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-500">Subtotal</span>
                                    <span class="text-base font-medium text-gray-900">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                                </div>
                                
                                @if($transaction->discount > 0)
                                <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                    <span class="text-sm text-gray-500">Diskon</span>
                                    <span class="text-base font-medium text-red-600">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center pt-3">
                                    <span class="text-lg font-medium text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-green-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Pembayaran</h3>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Metode</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            <i class="fas fa-money-bill-wave mr-1 text-green-500"></i> Cash
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Status</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Lunas
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <button id="showReceiptBtn" type="button"
                                   class="block w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
                                    <i class="fas fa-receipt mr-2"></i> Lihat Struk
                                </button>
                            </div>
                            <!-- Receipt Modal -->
                            <div id="receiptModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black bg-opacity-40">
                                <div class="bg-white shadow-sm p-4 m-6" style="width:320px; font-family: 'Courier New', Courier, monospace; font-size:12px;">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium">Struk Transaksi</h3>
                                        <button id="receiptClose" class="text-gray-600">&times;</button>
                                    </div>

                                    <div style="text-align:center; margin-top:6px;">
                                        <div style="font-size:18px; font-weight:700;">Star Frozen</div>
                                        <div>Jl. Abdul Fatah Barat, RT.02/RW.02, Dusun Bungur, Bungur, Kec. Karangrejo, Kabupaten Tulungagung, Jawa Timur 66253</div>
                                        <div>NO.TELP: 0812-3456-7890</div>
                                        <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>
                                    </div>

                                    <div style="margin-top:6px;">
                                        <div>INVOICE: {{ $transaction->invoice_number }}</div>
                                        <div>
                                            TANGGAL:
                                            @if(!empty($transaction->checkout_time))
                                                @php
                                                    $ct = $transaction->checkout_time;
                                                    $formattedDate = $transaction->created_at->toDateTimeString();
                                                    try {
                                                        if(strpos($ct, ' ') !== false){
                                                            [$d, $t] = explode(' ', $ct);
                                                            [$y, $m, $day] = explode('-', $d);
                                                            $hhmm = substr($t,0,5);
                                                            $formattedDate = $day . '/' . $m . '/' . $y . ' ' . $hhmm;
                                                        } else {
                                                            $formattedDate = $ct;
                                                        }
                                                    } catch (\Exception $e) {
                                                        $formattedDate = $ct;
                                                    }
                                                @endphp
                                                {{ $formattedDate }}
                                            @else
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </div>
                                    </div>

                                    <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>

                                    <div style="margin-top:6px;">
                                        @php $grand = 0; @endphp
                                        @foreach($transaction->items as $item)
                                            @php $line = $item->price * $item->quantity; $grand += $line; @endphp
                                            <div style="display:flex; justify-content:space-between;">
                                                <div style="width:60%;">{{ \Illuminate\Support\Str::limit($item->product->name ?? 'Product #'.$item->product_id, 28) }}</div>
                                                <div style="width:10%; text-align:right;">{{ $item->quantity }}</div>
                                                <div style="width:30%; text-align:right;">{{ number_format($line,0,',','.') }}</div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>

                                    <div style="margin-top:6px;">
                                        <div style="display:flex; justify-content:space-between;">
                                            <div>HARGA JUAL</div>
                                            <div style="text-align:right;">Rp {{ number_format($transaction->subtotal ?? $grand,0,',','.') }}</div>
                                        </div>
                                        @if(($transaction->discount ?? 0) > 0)
                                        <div style="display:flex; justify-content:space-between;">
                                            <div>DISKON</div>
                                            <div style="text-align:right;">- Rp {{ number_format($transaction->discount,0,',','.') }}</div>
                                        </div>
                                        @endif
                                        @if(($transaction->tax ?? 0) > 0)
                                        <div style="display:flex; justify-content:space-between;">
                                            <div>PAJAK</div>
                                            <div style="text-align:right;">Rp {{ number_format($transaction->tax,0,',','.') }}</div>
                                        </div>
                                        @endif

                                        <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:6px;">
                                            <div>TOTAL</div>
                                            <div style="text-align:right;">Rp {{ number_format($transaction->total,0,',','.') }}</div>
                                        </div>
                                    </div>

                                    <div style="margin-top:8px; border-top:1px dashed #000; padding-top:6px;"></div>

                                    <div style="margin-top:6px;">
                                        <div style="display:flex; justify-content:space-between;">
                                            <div>BAYAR ({{ strtoupper($transaction->payment_method ?? 'CASH') }})</div>
                                            <div style="text-align:right;">Rp {{ number_format($transaction->payment_amount ?? $transaction->total,0,',','.') }}</div>
                                        </div>
                                        <div style="display:flex; justify-content:space-between;">
                                            <div>KEMBALI</div>
                                            <div style="text-align:right;">Rp {{ number_format((($transaction->payment_amount ?? $transaction->total) - $transaction->total),0,',','.') }}</div>
                                        </div>
                                    </div>

                                    <div style="margin-top:10px; text-align:center;">
                                        <div>TERIMAKASIH TELAH BERBELANJA</div>
                                        <div style="margin-top:8px; font-size:10px;">Printed by Star Frozen POS</div>
                                    </div>

                                    <div style="margin-top:12px; display:flex; gap:12px; justify-content:center;" class="no-print">
                                        <button id="printReceiptBtn" class="no-print px-4 py-2 bg-black text-white rounded-md shadow hover:bg-gray-900 flex items-center gap-2" title="Print receipt">
                                            <i class="fas fa-print" aria-hidden="true"></i>
                                            <span style="font-weight:600;">Print</span>
                                        </button>
                                        <button id="closeReceiptBtn" class="no-print px-4 py-2 bg-white border border-gray-300 text-gray-800 rounded-md shadow hover:bg-gray-100 flex items-center gap-2" title="Close">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                            <span style="font-weight:600;">Close</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const showBtn = document.getElementById('showReceiptBtn');
        const modal = document.getElementById('receiptModal');
        const close = document.getElementById('receiptClose');
        const closeBtn = document.getElementById('closeReceiptBtn');
        const printBtn = document.getElementById('printReceiptBtn');

        function openModal(){ if(modal){ modal.classList.remove('hidden'); modal.classList.add('flex'); } }
        function hideModal(){ if(modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); } }

        if(showBtn) showBtn.addEventListener('click', openModal);
        if(close) close.addEventListener('click', hideModal);
        if(closeBtn) closeBtn.addEventListener('click', hideModal);
        if(printBtn) printBtn.addEventListener('click', function(){ window.print(); });
    });
</script>
@endpush

@endsection
