<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index()
    {
        // Ambil transaksi hari ini dan masa lalu
        $transactions = Booking::with(['user', 'items.treatment'])
            ->whereDate('booking_date', '<=', Carbon::today())
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('admin.transactions.index', compact('transactions'));
    }

    // 🔥 FUNGSI BARU: EXPORT PDF
    public function exportPdf()
    {
        $transactions = Booking::with(['user', 'items.treatment'])
            ->whereDate('booking_date', '<=', Carbon::today())
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        // Total Pendapatan dari yang Lunas/DP
        $totalPendapatan = 0;
        foreach($transactions as $trx) {
            if($trx->payment_status == 'paid' || $trx->payment_status == 'paid_full') {
                $totalPendapatan += $trx->total_price;
            } elseif ($trx->payment_status == 'paid_dp') {
                $totalPendapatan += $trx->dp_amount;
            }
        }

        // Generate PDF
        $pdf = Pdf::loadView('admin.transactions.pdf', compact('transactions', 'totalPendapatan'));
        $pdf->setPaper('A4', 'landscape');
        
        $fileName = 'Laporan_Transaksi_NKSALON_' . date('Y-m-d') . '.pdf';
        
        // Return untuk di download
        return $pdf->download($fileName);
    }
}