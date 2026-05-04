<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Ini wajib ada untuk menangkap data dari URL
use App\Models\BookingItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    // Tambahkan Request $request di dalam kurung
    public function index(Request $request)
    {
        // 🔥 LOGIKA BARU: Cek apakah ada parameter 'date' dari klik tombol navigasi
        if ($request->has('date')) {
            // Jika diklik tombol navigasi, jadikan tanggal tersebut sebagai Start Date
            $startDate = Carbon::parse($request->date)->startOfDay();
            // End Date adalah 7 hari setelah Start Date
            $endDate = $startDate->copy()->addDays(7)->endOfDay();
        } else {
            // 🔥 DEFAULT: Jika baru pertama kali buka menu, ambil 7 hari terakhir dari hari ini
            $endDate = Carbon::now()->endOfDay();
            $startDate = Carbon::now()->subDays(7)->startOfDay();
        }

        // 🔥 ambil data penggunaan treatment berdasarkan rentang tanggal yang dipilih
        $analytics = BookingItem::select(
                'treatments.name',
                DB::raw('COUNT(booking_items.id) as usage_count')
            )
            ->join('treatments', 'booking_items.treatment_id', '=', 'treatments.id')
            ->whereBetween('booking_items.created_at', [$startDate, $endDate])
            ->groupBy('treatments.name')
            ->get();

        return view('admin.inventory.index', compact('analytics', 'startDate', 'endDate'));
    }

    public function update(Request $request, $id)
    {
        return back()->with('success', 'Update berhasil');
    }
}