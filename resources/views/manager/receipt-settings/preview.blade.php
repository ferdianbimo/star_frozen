<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Struk - {{ $settings->store_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-start p-6">
        <!-- Preview Header -->
        <div class="mb-6 text-center">
            <h1 class="text-xl font-bold text-gray-800 mb-2">Preview Struk Penjualan</h1>
            <p class="text-gray-500 text-sm">Ini adalah contoh tampilan struk dengan pengaturan saat ini</p>
            <div class="mt-4 flex items-center justify-center gap-3">
                <a href="{{ route('manager.receipt-settings.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Pengaturan
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 no-print">
                    <i class="fas fa-print"></i>
                    Test Print
                </button>
            </div>
        </div>

        <!-- Receipt Preview -->
        <div class="bg-white shadow-lg p-4" style="width: {{ $settings->receipt_width }}; font-family: 'Courier New', Courier, monospace; font-size:12px;">
            <!-- Logo -->
            @if($settings->show_logo && $settings->logo)
            <div style="text-align:center; margin-bottom: 8px;">
                <img src="{{ Storage::url($settings->logo) }}" alt="Logo" style="max-height: 60px; max-width: 150px; margin: 0 auto;">
            </div>
            @endif

            <!-- Store Header -->
            <div style="text-align:center;">
                <div style="font-size:18px; font-weight:700;">{{ $settings->store_name }}</div>
                @if($settings->show_address && $settings->store_address)
                    <div>{{ $settings->store_address }}</div>
                @endif
                @if($settings->show_phone && $settings->store_phone)
                    <div>NO.TELP: {{ $settings->store_phone }}</div>
                @endif
                @if($settings->store_email)
                    <div>{{ $settings->store_email }}</div>
                @endif
                @if($settings->header_text)
                    <div style="margin-top:4px;">{{ $settings->header_text }}</div>
                @endif
                <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>
            </div>

            <!-- Transaction Info -->
            <div style="margin-top:6px;">
                <div>INVOICE: {{ $transaction['invoice_number'] }}</div>
                <div>TANGGAL: {{ now()->format('d/m/Y H:i') }}</div>
                @if($settings->show_cashier_name)
                    <div>KASIR: {{ $cashier->name }}</div>
                @endif
            </div>

            <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>

            <!-- Items -->
            <div style="margin-top:6px;">
                @php $grand = 0; @endphp
                @foreach($transaction['items'] as $item)
                    @php $line = $item['price'] * $item['quantity']; $grand += $line; @endphp
                    <div style="display:flex; justify-content:space-between;">
                        <div style="width:60%;">{{ Str::limit($item['name'], 28) }}</div>
                        <div style="width:10%; text-align:right;">{{ $item['quantity'] }}</div>
                        <div style="width:30%; text-align:right;">{{ number_format($line,0,',','.') }}</div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>

            <!-- Totals -->
            <div style="margin-top:6px;">
                <div style="display:flex; justify-content:space-between;">
                    <div>HARGA JUAL</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['subtotal'],0,',','.') }}</div>
                </div>
                @if(($transaction['discount_amount'] ?? 0) > 0)
                <div style="display:flex; justify-content:space-between;">
                    <div>DISKON</div>
                    <div style="text-align:right;">- Rp {{ number_format($transaction['discount_amount'],0,',','.') }}</div>
                </div>
                @endif
                @if(($transaction['tax_amount'] ?? 0) > 0)
                <div style="display:flex; justify-content:space-between;">
                    <div>PAJAK</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['tax_amount'],0,',','.') }}</div>
                </div>
                @endif

                <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:6px;">
                    <div>TOTAL</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['total'],0,',','.') }}</div>
                </div>
            </div>

            <div style="margin-top:8px; border-top:1px dashed #000; padding-top:6px;"></div>

            <!-- Payment -->
            <div style="margin-top:6px;">
                <div style="display:flex; justify-content:space-between;">
                    <div>BAYAR ({{ strtoupper($transaction['payment_method']) }})</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['payment_amount'],0,',','.') }}</div>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <div>KEMBALI</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['payment_amount'] - $transaction['total'],0,',','.') }}</div>
                </div>
            </div>

            <!-- Footer -->
            <div style="margin-top:10px; text-align:center;">
                @if($settings->show_thank_you && $settings->thank_you_text)
                    <div>{{ $settings->thank_you_text }}</div>
                @endif
                @if($settings->footer_text)
                    <div style="margin-top:8px; font-size:10px;">{{ $settings->footer_text }}</div>
                @endif
            </div>
        </div>

        <!-- Info Badge -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl max-w-md no-print">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Catatan Preview</p>
                    <p class="text-xs text-blue-600 mt-1">Ini adalah preview dengan data contoh. Tampilan sebenarnya akan menggunakan data transaksi real.</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @page { size: auto; margin: 0; }
        @media print {
            html, body { margin: 0; padding: 0; }
            body { background: white; }
            .no-print { display: none !important; }
        }
    </style>
</body>
</html>
