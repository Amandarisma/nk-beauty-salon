<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'user')
            ->withCount('bookings')
            ->get();

        foreach ($customers as $customer) {
            $favorite = DB::table('booking_items')
                ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->join('treatments', 'booking_items.treatment_id', '=', 'treatments.id')
                ->where('bookings.user_id', $customer->id)
                ->select('treatments.name', DB::raw('COUNT(*) as total'))
                ->groupBy('treatments.name')
                ->orderByDesc('total')
                ->first();

            // 🔥 LOGIKA BARU: Tambahkan teks (berapa kali)
            if (isset($favorite->name)) {
                $customer->favorite_treatment = ucfirst(strtolower($favorite->name)) . ' (' . $favorite->total . 'x)';
            } else {
                $customer->favorite_treatment = 'Belum ada';
            }
        }

        return view('admin.customers.index', compact('customers'));
    }

    // 🔥 FUNGSI BARU UNTUK HALAMAN DETAIL PELANGGAN
    public function show($id)
    {
        $customer = User::findOrFail($id);
        
        // Ambil riwayat transaksi khusus pelanggan ini
        $transactions = Booking::with(['items.treatment'])
            ->where('user_id', $id)
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('admin.customers.show', compact('customer', 'transactions'));
    }
}