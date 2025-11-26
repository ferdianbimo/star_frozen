<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #991b1b;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #64748b;
        }
        .summary {
            background: #fef2f2;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #ef4444;
        }
        .summary h2 {
            margin: 0 0 10px 0;
            color: #dc2626;
            font-size: 16px;
        }
        .summary .total {
            font-size: 24px;
            font-weight: bold;
            color: #b91c1c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #991b1b;
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
            background: #fee2e2 !important;
            border-top: 2px solid #ef4444;
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
        <p>Laporan Pengeluaran</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}</p>
    </div>

    <div class="summary">
        <h2>Total Pengeluaran</h2>
        <div class="total">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
        <p style="margin: 5px 0 0 0; color: #64748b;">Dari {{ $expensesList->count() }} transaksi pengeluaran</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 38%;">Deskripsi</th>
                <th style="width: 18%;" class="text-right">Jumlah</th>
                <th style="width: 12%;">Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expensesList as $index => $expense)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                <td><strong>{{ $expense->category }}</strong></td>
                <td>{{ $expense->description }}</td>
                <td class="text-right"><strong>Rp {{ number_format($expense->amount, 0, ',', '.') }}</strong></td>
                <td>{{ $expense->user->name ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data</td>
            </tr>
            @endforelse
            
            @if($expensesList->count() > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right" style="padding: 12px;"><strong>TOTAL PENGELUARAN:</strong></td>
                <td class="text-right" style="padding: 12px;"><strong>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</strong></td>
                <td></td>
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
