<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan NK Beauty Salon</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #db2777; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #db2777; margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px; }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 14px; }
        
        .summary-box { background-color: #fdf2f8; border: 1px solid #fbcfe8; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .summary-box h2 { margin: 0; color: #db2777; font-size: 28px; }
        .summary-box p { margin: 0; font-size: 12px; font-weight: bold; color: #9d174d; text-transform: uppercase; }

        .chart-container { text-align: center; margin-bottom: 30px; }
        .chart-container img { max-width: 100%; height: auto; border: 1px solid #eee; border-radius: 8px; padding: 10px; }
        .chart-title { font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 10px; color: #4b5563; }

        table { w-full: 100%; width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th { background-color: #db2777; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background-color: #f9fafb; }
        
        .badge-lunas { color: #059669; font-weight: bold; }
        .badge-dp { color: #2563eb; font-weight: bold; }
        .badge-pending { color: #d97706; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>NK Beauty Salon</h1>
        <p>Laporan Operasional & Keuangan</p>
        <p>Periode: <strong>{{ $judulPeriode }}</strong></p>
    </div>

    <div class="summary-box">
        <p>Total Pendapatan Terkumpul</p>
        <h2>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
    </div>

    <div class="chart-container">
        <div class="chart-title">Grafik Pendapatan Berdasarkan Layanan (Rp)</div>
        <!-- Menampilkan gambar grafik dari URL QuickChart -->
        <img src="{{ $chartUrl }}" alt="Grafik Pendapatan">
    </div>

    <h3 style="color: #db2777; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Rincian Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Status Bayar</th>
                <th>Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $trx)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trx->booking_date)->format('d/m/Y') }}</td>
                    <td>{{ $trx->invoice_code }}</td>
                    <td>{{ $trx->user ? $trx->user->name : 'Walk-in' }}</td>
                    <td>
                        @foreach($trx->items as $item)
                            {{ $item->treatment->name ?? 'Layanan' }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                    <td>
                        @if($trx->payment_status == 'paid') <span class="badge-lunas">Lunas</span>
                        @elseif(in_array($trx->payment_status, ['paid_dp', 'dp'])) <span class="badge-dp">DP</span>
                        @else <span class="badge-pending">Belum</span>
                        @endif
                    </td>
                    <td style="text-align: right;">{{ number_format($trx->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999; padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi NK Beauty Salon pada {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y H:i') }} WIB.
    </div>

</body>
</html>