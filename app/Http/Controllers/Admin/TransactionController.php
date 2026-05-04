<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // PASTIKAN INI ADA
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

    // 🔥 FUNGSI BARU: UPDATE STATUS BAYAR DARI POP UP
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:paid_full,cancelled'
        ]);

        // Karena di tabelmu menggunakan model Booking, kita cari berdasarkan Booking ID
        $transaction = Booking::findOrFail($id); 
        
        $transaction->update([
            'payment_status' => $request->payment_status
        ]);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui menjadi ' . ($request->payment_status == 'paid_full' ? 'Lunas' : 'Dibatalkan') . '.');
    }
}