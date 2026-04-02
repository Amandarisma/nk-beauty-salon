<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'user')
            ->withCount('bookings')
            ->get();

        // 🔥 TAMBAHAN LOGIKA FAVORIT
        foreach ($customers as $customer) {
            $favorite = DB::table('booking_items')
                ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->join('treatments', 'booking_items.treatment_id', '=', 'treatments.id')
                ->where('bookings.user_id', $customer->id)
                ->select('treatments.name', DB::raw('COUNT(*) as total'))
                ->groupBy('treatments.name')
                ->orderByDesc('total')
                ->first();

            $customer->favorite_treatment = isset($favorite->name)
    ? ucfirst(strtolower($favorite->name))
    : null;
        }

        return view('admin.customers.index', compact('customers'));
    }
}