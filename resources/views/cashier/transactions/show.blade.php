@extends('layouts.cashier')

@section('title', 'Detail Transaksi')

@section('content')
                <!-- Modern Header -->
                <div class="bg-gradient-to-r from-slate-800 via-slate-900 to-slate-800 rounded-2xl p-6 mb-8 shadow-xl">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                                <i class="fas fa-receipt text-2xl text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">Detail Transaksi</h2>
                                <p class="text-slate-400 text-sm">Invoice: {{ $transaction->invoice_number ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('cashier.transactions.index') }}" class="px-4 py-2.5 bg-slate-700/50 hover:bg-slate-600/50 text-white rounded-xl border border-slate-600 transition-all flex items-center gap-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Kembali</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Transaction Info -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
                            <h2 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                                    <i class="fas fa-info-circle text-white"></i>
                                </div>
                                Informasi Transaksi
                            </h2>
                            <div class="grid grid-cols-2 gap-6">
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-sm text-slate-500 font-medium mb-1">ID Transaksi</p>
                                    <p class="text-lg font-mono font-bold text-slate-800 bg-slate-200 px-3 py-1 rounded-lg inline-block">#{{ $transaction->id }}</p>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-sm text-slate-500 font-medium mb-1">Invoice Number</p>
                                    <p class="text-lg font-bold text-blue-600">{{ $transaction->invoice_number ?? '-' }}</p>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-sm text-slate-500 font-medium mb-1">Tanggal & Waktu</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-calendar-alt text-white text-xs"></i>
                                        </div>
                                        <p class="text-lg font-bold text-slate-800">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <p class="text-sm text-slate-500 font-medium mb-1">Kasir</p>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-user text-white text-xs"></i>
                                        </div>
                                        <p class="text-lg font-bold text-slate-800">{{ $transaction->user->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                        <i class="fas fa-shopping-cart text-white"></i>
                                    </div>
                                    Daftar Item
                                    <span class="ml-auto px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 rounded-full text-sm font-medium border border-blue-200">
                                        {{ $transaction->items->count() }} item(s)
                                    </span>
                                </h2>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Produk</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Harga</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Qty</th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        @foreach($transaction->items as $index => $item)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-mono font-medium text-slate-700 bg-slate-100 px-2 py-1 rounded">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    @if($item->product && $item->product->image)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->image) }}" 
                                                             alt="{{ $item->product->name ?? 'Product' }}" 
                                                             class="h-12 w-12 rounded-xl object-cover border-2 border-slate-100">
                                                    @else
                                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center border-2 border-slate-100">
                                                            <i class="fas fa-box text-slate-400 text-lg"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-bold text-slate-800">{{ $item->product->name ?? 'Product #'.$item->product_id }}</div>
                                                        <div class="text-xs text-slate-500">{{ $item->product->category ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-700">
                                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 border border-blue-200">
                                                    <i class="fas fa-box mr-1"></i>{{ $item->quantity }} pack
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-base font-bold text-emerald-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
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
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-6">
                            <h2 class="text-lg font-bold text-slate-800 mb-5 flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                                    <i class="fas fa-calculator text-white"></i>
                                </div>
                                Ringkasan
                            </h2>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                    <span class="text-sm text-slate-500 font-medium">Subtotal</span>
                                    <span class="text-lg font-bold text-slate-800">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                                </div>
                                
                                @if($transaction->discount > 0)
                                <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                    <span class="text-sm text-slate-500 font-medium flex items-center gap-2">
                                        <span class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-tag text-red-500 text-xs"></i>
                                        </span>
                                        Diskon
                                    </span>
                                    <span class="text-lg font-bold text-red-600">-Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center pt-2 pb-4 bg-gradient-to-r from-emerald-50 to-teal-50 -mx-6 px-6 border-t border-b border-emerald-200">
                                    <span class="text-lg font-bold text-slate-800">Total</span>
                                    <span class="text-2xl font-extrabold text-emerald-600">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                    <span class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-credit-card text-green-500 text-xs"></i>
                                    </span>
                                    Pembayaran
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                        <span class="text-sm text-slate-500 font-medium">Metode</span>
                                        <span class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                            <span class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-money-bill-wave text-green-500 text-xs"></i>
                                            </span>
                                            Cash
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl">
                                        <span class="text-sm text-slate-500 font-medium">Status</span>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 border border-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Lunas
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <button id="showReceiptBtn" type="button"
                                   class="block w-full text-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-receipt"></i> Lihat Struk
                                </button>
                            </div>
                            <!-- Receipt Modal -->
                            <div id="receiptModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black bg-opacity-40" style="padding-top: 120px;">
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
