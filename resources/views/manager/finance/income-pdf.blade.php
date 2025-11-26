<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemasukan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #1e40af;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #64748b;
        }
        .summary {
            background: #f1f5f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #10b981;
        }
        .summary h2 {
            margin: 0 0 10px 0;
            color: #059669;
            font-size: 16px;
        }
        .summary .total {
            font-size: 24px;
            font-weight: bold;
            color: #047857;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #1e40af;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background: #dcfce7 !important;
            border-top: 2px solid #10b981;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #64748b;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>STAR FROZEN POS</h1>
        <p>Laporan Pemasukan</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</p>
    </div>

    <div class="summary">
        <h2>Total Pemasukan</h2>
        <div class="total">Rp {{ number_format($totalFilteredValue, 0, ',', '.') }}</div>
        <p style="margin: 5px 0 0 0; color: #64748b;">Dari {{ $incomeLogs->count() }} transaksi penjualan</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Produk</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 15%;" class="text-right">Harga Satuan</th>
                <th style="width: 15%;" class="text-right">Total</th>
                <th style="width: 10%;">Kasir</th>
                <th style="width: 10%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomeLogs as $index => $log)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td><strong>{{ $log->product->name ?? 'N/A' }}</strong></td>
                <td class="text-center">{{ abs($log->change) }} pack</td>
                <td class="text-right">Rp {{ number_format($log->unit_price, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($log->total_value, 0, ',', '.') }}</strong></td>
                <td>{{ $log->user->name ?? 'System' }}</td>
                <td style="font-size: 10px;">{{ $log->note ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data</td>
            </tr>
            @endforelse
            
            @if($incomeLogs->count() > 0)
            <tr class="total-row">
                <td colspan="5" class="text-right" style="padding: 12px;"><strong>TOTAL PEMASUKAN:</strong></td>
                <td class="text-right" style="padding: 12px;"><strong>Rp {{ number_format($totalFilteredValue, 0, ',', '.') }}</strong></td>
                <td colspan="2"></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Star Frozen POS</p>
        <p>© {{ date('Y') }} Star Frozen. All rights reserved.</p>
    </div>
</body>
</html>
