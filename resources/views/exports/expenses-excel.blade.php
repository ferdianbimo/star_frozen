<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 8px 12px; }
        th { background-color: #dc2626; color: #ffffff; font-weight: bold; text-align: center; }
        .header-row { background-color: #dc2626; color: #ffffff; }
        .data-row-even { background-color: #fef2f2; }
        .data-row-odd { background-color: #ffffff; }
        .total-row { background-color: #b91c1c; color: #ffffff; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .title { font-size: 18px; font-weight: bold; color: #dc2626; text-align: center; margin-bottom: 5px; }
        .subtitle { font-size: 12px; color: #6b7280; text-align: center; margin-bottom: 15px; }
        .info { font-size: 11px; color: #6b7280; text-align: center; font-style: italic; margin-top: 15px; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="6" class="title">LAPORAN PENGELUARAN - STAR FROZEN</td>
        </tr>
        <tr>
            <td colspan="6" class="subtitle">Periode: {{ $periodText }} | Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr><td colspan="6"></td></tr>
    </table>
    
    <table>
        <thead>
            <tr class="header-row">
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="18%">Kategori</th>
                <th width="35%">Deskripsi</th>
                <th width="15%">Jumlah (Rp)</th>
                <th width="15%">Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($expensesList as $expense)
            <tr class="{{ $no % 2 == 0 ? 'data-row-even' : 'data-row-odd' }}">
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                <td>{{ $expense->category ?? '-' }}</td>
                <td>{{ $expense->description ?? '-' }}</td>
                <td class="text-right" style="mso-number-format:'#,##0';">{{ $expense->amount }}</td>
                <td>{{ $expense->user->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL PENGELUARAN:</td>
                <td class="text-right" style="mso-number-format:'Rp #,##0';">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <table>
        <tr>
            <td colspan="6" class="info">
                Total {{ $expensesList->count() }} transaksi pengeluaran | 
                Rata-rata: Rp {{ $expensesList->count() > 0 ? number_format($totalExpenses / $expensesList->count(), 0, ',', '.') : 0 }} per transaksi
            </td>
        </tr>
    </table>
</body>
</html>
