<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi - NK Beauty Salon</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #db2777; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #db2777; margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #fce7f3; color: #db2777; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .status-lunas { color: #059669; font-weight: bold; }
        .status-dp { color: #2563eb; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .summary-box { margin-top: 20px; padding: 15px; background-color: #f9fafb; border: 1px solid #ddd; border-radius: 5px; width: 300px; float: right; }
        .summary-title { font-weight: bold; color: #db2777; margin-bottom: 10px; font-size: 14px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>NK Beauty Salon</h1>
        <p>Laporan Rekapitulasi Transaksi Pelanggan</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Tanggal Layanan</th>
                <th>Nama Pelanggan</th>
                <th>Layanan</th>
                <th>Status Pembayaran</th>
                <th class="text-right">Total Transaksi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $trx->invoice_code }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->booking_date)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($trx->start_time)->format('H:i') }}</td>
                <td>{{ $trx->user ? $trx->user->name : ($trx->guest_name ?? 'Walk-in') }}</td>
                <td>
                    @foreach($trx->items as $item)
                        • {{ $item->treatment->name ?? '-' }}<br>
                    @endforeach
                </td>
                <td class="text-center">
                    @if($trx->payment_status == 'paid' || $trx->payment_status == 'paid_full')
                        <span class="status-lunas">LUNAS</span>
                    @elseif($trx->payment_status == 'paid_dp')
                        <span class="status-dp">DP 30%</span>
                    @else
                        <span class="status-pending">PENDING</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($trx->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-title">Ringkasan Pendapatan Masuk</div>
        <table style="border:none; margin-top:0;">
            <tr>
                <td style="border:none; padding:4px 0;">Total Transaksi (Lunas/DP) :</td>
                <td style="border:none; padding:4px 0; font-weight:bold; text-align:right;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

</body>
</html>