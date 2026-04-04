<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        // 🔥 FILTER: Hanya ambil hari ini dan masa lalu, lalu urutkan dari yang terbaru
        $transactions = Booking::with(['user', 'items.treatment'])
            ->whereDate('booking_date', '<=', Carbon::today())
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('admin.transactions.index', compact('transactions'));
    }
}