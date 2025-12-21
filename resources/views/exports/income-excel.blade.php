<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 8px 12px; }
        th { background-color: #059669; color: #ffffff; font-weight: bold; text-align: center; }
        .header-row { background-color: #059669; color: #ffffff; }
        .data-row-even { background-color: #ecfdf5; }
        .data-row-odd { background-color: #ffffff; }
        .total-row { background-color: #047857; color: #ffffff; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .title { font-size: 18px; font-weight: bold; color: #059669; text-align: center; margin-bottom: 5px; }
        .subtitle { font-size: 12px; color: #6b7280; text-align: center; margin-bottom: 15px; }
        .info { font-size: 11px; color: #6b7280; text-align: center; font-style: italic; margin-top: 15px; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="9" class="title">LAPORAN PEMASUKAN - STAR FROZEN</td>
        </tr>
        <tr>
            <td colspan="9" class="subtitle">Periode: {{ $periodText }} | Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr><td colspan="9"></td></tr>
    </table>
    
    <table>
        <thead>
            <tr class="header-row">
                <th width="4%">No</th>
                <th width="10%">Tanggal</th>
                <th width="6%">Waktu</th>
                <th width="20%">Produk</th>
                <th width="8%">Kode</th>
                <th width="6%">Qty</th>
                <th width="12%">Harga Satuan</th>
                <th width="14%">Total (Rp)</th>
                <th width="10%">Kasir</th>
                <th width="10%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $totalQty = 0; @endphp
            @foreach($incomeLogs as $log)
            @php $totalQty += abs($log->change); @endphp
            <tr class="{{ $no % 2 == 0 ? 'data-row-even' : 'data-row-odd' }}">
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ $log->created_at->format('d/m/Y') }}</td>
                <td class="text-center">{{ $log->created_at->format('H:i') }}</td>
                <td>{{ $log->product->name ?? 'N/A' }}</td>
                <td class="text-center">{{ $log->product->code ?? '-' }}</td>
                <td class="text-center">{{ abs($log->change) }} {{ $log->unit_label ?? 'pcs' }}</td>
                <td class="text-right" style="mso-number-format:'#,##0';">{{ number_format($log->unit_price, 0, ',', '.') }}</td>
                <td class="text-right" style="mso-number-format:'#,##0';">{{ number_format($log->total_value, 0, ',', '.') }}</td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td>{{ $log->note ?? '-' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" class="text-right">TOTAL PEMASUKAN:</td>
                <td class="text-right" style="mso-number-format:'Rp #,##0';">Rp {{ number_format($totalFilteredValue, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
    
    <table>
        <tr>
            <td colspan="10" class="info">
                Total {{ $incomeLogs->count() }} transaksi | 
                Total {{ number_format($totalQty, 0, ',', '.') }} item terjual | 
                Rata-rata: Rp {{ $incomeLogs->count() > 0 ? number_format($totalFilteredValue / $incomeLogs->count(), 0, ',', '.') : 0 }} per transaksi
            </td>
        </tr>
    </table>
</body>
</html>
