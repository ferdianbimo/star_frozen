<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $transaction['invoice_number'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    @php
        $settings = \App\Models\ReceiptSetting::current();
    @endphp
    <div class="min-h-screen flex items-start justify-center p-6">
        <div class="bg-white shadow-sm p-4" style="width:{{ $settings->receipt_width }}; font-family: 'Courier New', Courier, monospace; font-size:12px;">
            <!-- Logo -->
            @if($settings->show_logo && $settings->logo)
            <div style="text-align:center; margin-bottom: 8px;">
                <img src="{{ Storage::url($settings->logo) }}" alt="Logo" style="max-height: 60px; max-width: 150px; margin: 0 auto;">
            </div>
            @endif

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

            <div style="margin-top:6px;">
                <div>INVOICE: {{ $transaction['invoice_number'] }}</div>
                <div>
                    TANGGAL: 
                    @if(!empty($transaction['checkout_time']))
                        @php
                            // Expecting format 'YYYY-MM-DD HH:MM:SS' from device; format to 'd/m/Y H:i' without timezone conversion
                            $ct = $transaction['checkout_time'];
                            $formattedDate = $transaction['created_at'];
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
                        {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d/m/Y H:i') }}
                    @endif
                </div>
                @if($settings->show_cashier_name && !empty($cashier))
                    <div>KASIR: {{ $cashier->name }}</div>
                @endif
                @if(!empty($transaction['table_number']))
                    <div>Table: {{ $transaction['table_number'] }}</div>
                @endif
            </div>

            <div style="margin-top:6px; border-top:1px dashed #000; padding-top:6px;"></div>

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

            <div style="margin-top:6px;">
                <div style="display:flex; justify-content:space-between;">
                    <div>HARGA JUAL</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['subtotal'] ?? $grand,0,',','.') }}</div>
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
                @if(($transaction['service_amount'] ?? 0) > 0)
                <div style="display:flex; justify-content:space-between;">
                    <div>KEMBALI</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['service_amount'],0,',','.') }}</div>
                </div>
                @endif

                <div style="display:flex; justify-content:space-between; font-weight:700; margin-top:6px;">
                    <div>TOTAL</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['total'],0,',','.') }}</div>
                </div>
            </div>

            <div style="margin-top:8px; border-top:1px dashed #000; padding-top:6px;"></div>

            <div style="margin-top:6px;">
                <div style="display:flex; justify-content:space-between;">
                    <div>BAYAR ({{ strtoupper($transaction['payment_method'] ?? 'CASH') }})</div>
                    <div style="text-align:right;">Rp {{ number_format($transaction['payment_amount'] ?? $transaction['total'],0,',','.') }}</div>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <div>KEMBALI</div>
                    <div style="text-align:right;">Rp {{ number_format( (($transaction['payment_amount'] ?? $transaction['total']) - $transaction['total']),0,',','.') }}</div>
                </div>
            </div>

            <div style="margin-top:10px; text-align:center;">
                @if($settings->show_thank_you && $settings->thank_you_text)
                    <div>{{ $settings->thank_you_text }}</div>
                @endif
                @if($settings->footer_text)
                    <div style="margin-top:8px; font-size:10px;">{{ $settings->footer_text }}</div>
                @endif
            </div>

            <div style="margin-top:12px; display:flex; gap:12px; justify-content:center;" class="no-print">
                <button onclick="window.print()" class="no-print px-4 py-2 bg-black text-white rounded-md shadow hover:bg-gray-900 flex items-center gap-2" title="Print receipt">
                    <i class="fas fa-print" aria-hidden="true"></i>
                    <span style="font-weight:600;">Print</span>
                </button>

                <a href="{{ route('cashier.pos.new') }}" class="no-print px-4 py-2 bg-white border border-gray-300 text-gray-800 rounded-md shadow hover:bg-gray-100 flex items-center gap-2" title="Start a new transaction">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    <span style="font-weight:600;">New Transaction</span>
                </a>
            </div>
        </div>
    </div>

    <style>
        .receipt { font-family: Arial, Helvetica, sans-serif; color: #111; }
        /* Try to minimize browser-added headers/footers by resetting page margins.
           Note: many browsers still add their own header/footer (date/title) that
           cannot be removed via page CSS — see instructions below to disable
           "Headers and footers" in the print dialog. */
        @page { size: auto; margin: 0; }
        @media print {
            html, body { margin: 0; padding: 0; }
            body { background: white; }
            /* Ensure action controls are excluded from print */
            .no-print { display: none !important; }
        }
    </style>
</body>
</html>
