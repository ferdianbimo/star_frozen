<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemasukan</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #059669;
            padding: 10px 20px 10px 20px;
            margin: -8px -20px 4px -20px;
            position: relative;
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
            color: #059669;
            margin-bottom: 2px;
        }
        .header-subtitle {
            font-size: 13px;
            color: #059669;
            margin-bottom: 0;
        }
        .header-meta {
            text-align: right;
            color: #059669;
            font-size: 13px;
        }
        .header-meta-date {
            font-size: 12px;
            color: #059669;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #34d399, #10b981);
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
            background: #10b981;
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
            background: #f1f5f9;
            color: #475569;
            padding: 10px 12px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background: #fafbfc;
        }
        tr:hover {
            background: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        
        /* Badges */
        .qty-badge {
            display: inline-block;
            background: #fee2e2;
            color: #dc2626;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
        }
        .income-badge {
            display: inline-block;
            background: #d1fae5;
            color: #059669;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
        }
        .product-name {
            font-weight: 600;
            color: #1e293b;
        }
        .product-code {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
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
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important;
            border-top: 2px solid #10b981;
        }
        .total-row td {
            padding: 12px;
            font-weight: 700;
            color: #047857;
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
            color: #10b981;
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
                        <div class="header-subtitle">Laporan Pemasukan</div>
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
                <h2>Daftar Pemasukan</h2>
                <span class="table-badge">{{ $incomeLogs->count() }} Data</span>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 22%;">Produk</th>
                        <th style="width: 10%;" class="text-center">Qty</th>
                        <th style="width: 14%;" class="text-right">Harga Satuan</th>
                        <th style="width: 16%;" class="text-right">Total</th>
                        <th style="width: 12%;">Kasir</th>
                        <th style="width: 10%;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomeLogs as $index => $log)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 500;">{{ $log->created_at->format('d/m/Y') }}</div>
                            <div style="font-size: 10px; color: #94a3b8;">{{ $log->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="product-name">{{ $log->product->name ?? 'N/A' }}</div>
                            <div class="product-code">{{ $log->product->code ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="qty-badge">{{ abs($log->change) }} {{ $log->unit_label ?? 'pcs' }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($log->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">
                            <span class="income-badge">Rp {{ number_format($log->total_value, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">{{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}</div>
                                <span>{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td style="font-size: 10px; color: #64748b;">{{ Str::limit($log->note ?? '-', 20) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <p>Tidak ada data pemasukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    
                    @if($incomeLogs->count() > 0)
                    <tr class="total-row">
                        <td colspan="5" class="text-right">TOTAL PEMASUKAN:</td>
                        <td class="text-right">Rp {{ number_format($totalFilteredValue, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                    @endif
                th {
                    background: #059669;
                    color: #fff;
                    padding: 8px 12px;
                    font-size: 10px;
                    font-weight: bold;
                    text-align: center;
                    border-bottom: 2px solid #047857;
                }
                td {
                    padding: 8px 12px;
                    border-bottom: 1px solid #f1f5f9;
                    vertical-align: middle;
                    font-size: 10px;
                }
                tbody tr:nth-child(even) {
                    background: #ecfdf5;
                }
                tbody tr:nth-child(odd) {
                    background: #fff;
                }
