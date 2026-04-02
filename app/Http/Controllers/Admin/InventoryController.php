<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // 🔥 ambil range 7 hari terakhir
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(7);

        // 🔥 ambil data penggunaan treatment
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