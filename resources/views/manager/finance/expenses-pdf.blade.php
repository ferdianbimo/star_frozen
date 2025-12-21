<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #334155;
            background: #fff;
            line-height: 1.5;
        }
        .container {
            padding: 8px 20px 20px 20px;
            max-width: 100%;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #dc2626;
            padding: 10px 20px 10px 20px;
            margin: -8px -20px 4px -20px;
            position: relative;
        }
        .main-title {
            font-size: 26px;
            font-weight: bold;
            color: #dc2626;
            text-align: center;
            margin-bottom: 2px;
            margin-top: 8px;
            letter-spacing: 1px;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ef4444, #f87171, #ef4444);
        }
        .header-content {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header-title {
            font-size: 22px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 2px;
        }
        .header-subtitle {
            font-size: 13px;
            color: #dc2626;
            margin-bottom: 0;
        }
        .header-meta {
            text-align: right;
            color: #dc2626;
            font-size: 13px;
        }
        .header-meta-date {
            font-size: 12px;
            color: #dc2626;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-box {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }
        .brand-info h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .brand-info p {
            font-size: 12px;
            opacity: 0.9;
        }
        .header-meta {
            text-align: right;
            font-size: 11px;
        }
        .header-meta p {
            margin-bottom: 3px;
            opacity: 0.9;
        }
        
        /* Summary Box */
        .summary-section {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .summary-card {
            flex: 1;
            padding: 15px;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }
        .summary-main {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        .summary-main::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .summary-secondary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .summary-label {
            font-size: 11px;
            opacity: 0.8;
            margin-bottom: 6px;
        }
        .summary-value {
            font-size: 20px;
            font-weight: 700;
        }
        .summary-secondary .summary-label {
            color: #64748b;
        }
        .summary-secondary .summary-value {
            color: #1e293b;
            font-size: 18px;
        }
        
        /* Table */
        .table-section {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .table-header {
            background: #f8fafc;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table-header h2 {
            font-size: 13px;
            color: #1e293b;
            font-weight: 600;
        }
        .table-badge {
            background: #ef4444;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #dc2626;
            color: #fff;
            padding: 8px 12px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            border-bottom: 2px solid #b91c1c;
        }
        td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 10px;
        }
        tbody tr:nth-child(even) {
            background: #fef2f2;
        }
        tbody tr:nth-child(odd) {
            background: #fff;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        
        /* Badges */
        .expense-badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
        }
        .category-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 11px;
        }
        .user-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-avatar {
            width: 26px;
            height: 26px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 6px;
            color: white;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Total Row */
        .total-row {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%) !important;
            border-top: 2px solid #ef4444;
        }
        .total-row td {
            padding: 12px;
            font-weight: 700;
            color: #b91c1c;
            font-size: 12px;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-left {
            color: #94a3b8;
            font-size: 10px;
        }
        .footer-right {
            text-align: right;
        }
        .footer-brand {
            font-weight: 600;
            color: #ef4444;
            font-size: 12px;
        }
        .footer-copy {
            color: #94a3b8;
            font-size: 9px;
            margin-top: 3px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #94a3b8;
        }
        .empty-state p {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <div class="header-title">STAR FROZEN</div>
                    <div class="header-subtitle">Laporan Pengeluaran</div>
                </div>
                <div class="header-meta">
                    <div><b>Tanggal Cetak:</b></div>
                    <div class="header-meta-date">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
                    <div class="header-meta-date">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <div class="table-header">
                <h2>Daftar Pengeluaran</h2>
                <span class="table-badge">{{ $expensesList->count() }} Data</span>
            </div>
            
            <table>
                <thead>
                    <tr>
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
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                        <td>{{ $expense->category ?? '-' }}</td>
                        <td>{{ $expense->description ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        <td>{{ $expense->user->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                    @if($expensesList->count() > 0)
                    <tr class="total-row">
                        <td colspan="4" class="text-right">TOTAL PENGELUARAN:</td>
                        <td class="text-right">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="info" style="margin-top:8px;">
                Total {{ $expensesList->count() }} transaksi pengeluaran |
                Rata-rata: Rp {{ $expensesList->count() > 0 ? number_format($totalExpenses / $expensesList->count(), 0, ',', '.') : 0 }} per transaksi
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                <p>Dokumen ini digenerate secara otomatis oleh sistem</p>
                <p>Halaman 1 dari 1</p>
            </div>
            <div class="footer-right">
                <div class="footer-brand">Star Frozen POS</div>
                <div class="footer-copy">© {{ date('Y') }} All rights reserved</div>
            </div>
        </div>
    </div>
</body>
</html>
