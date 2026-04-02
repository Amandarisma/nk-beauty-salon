<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\User;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOperationController extends Controller
{
    public function dashboard()
    {
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('booking_status', 'new')->count();
        $totalCustomers = User::where('role', 'user')->count();

        $bookings = Booking::with(['user', 'items.treatment'])->get();
        
        $events = [];

        foreach($bookings as $booking) {
            $firstItem = $booking->items->first();

            if($firstItem) {
                $color = ($booking->payment_status == 'paid_dp' || $booking->payment_status == 'paid_full') 
                    ? '#10b981'
                    : '#f59e0b';

                $customerName = $booking->user 
                    ? $booking->user->name 
                    : ($booking->guest_name . ' (Walk-in)');

                $events[] = [
                    'title' => $customerName,
                    'start' => $firstItem->scheduled_date . 'T' . $firstItem->scheduled_time,
                    'color' => $color
                ];
            }
        }

        return view('admin.dashboard', compact('totalBookings', 'pendingBookings', 'totalCustomers', 'events'));
    }

    public function createWalkIn()
    {
        $treatments = Treatment::all();
        $users = User::where('role', 'user')->get(); 
        return view('admin.pos.create', compact('treatments', 'users'));
    }

    public function storeWalkIn(Request $request)
{
$request->validate([
    'guest_name' => 'required',
    'phone' => 'required',
    'date' => 'required|date',
    'time' => 'required',
    'treatment_id' => 'required|exists:treatments,id',
]);

    return DB::transaction(function () use ($request) {

        $treatment = Treatment::findOrFail($request->treatment_id);

        $startTime = date('H:i:s', strtotime($request->time));
        $endTime = date('H:i:s', strtotime($request->time . ' + ' . $treatment->duration . ' minutes'));

        // 🔥 NORMALISASI NAMA (biar rapi)
        $name = ucfirst(strtolower(trim($request->guest_name)));

        // 🔥 CARI CUSTOMER BERDASARKAN NAMA
       // 🔥 NORMALISASI
$name = ucfirst(strtolower(trim($request->guest_name)));
$phone = preg_replace('/[^0-9]/', '', $request->phone);

// 🔥 FORMAT KE 62
if (substr($phone, 0, 1) == '0') {
    $phone = '62' . substr($phone, 1);
}

// 🔥 CARI CUSTOMER BERDASARKAN HP
$customer = User::where('phone', $phone)->first();

// 🔥 KALAU BELUM ADA → BUAT
if (!$customer) {
$customer = User::create([
    'name' => $name,
    'phone' => $phone,
    'email' => $phone . '@walkin.local', // 🔥 FIX
    'role' => 'user',
    'password' => bcrypt(uniqid()),
]);
}

        // 🔥 KALAU BELUM ADA → BUAT BARU
        if (!$customer) {
            $customer = User::create([
                'name' => $name,
                'email' => null,
                'role' => 'user',
                'password' => bcrypt(uniqid()), // 🔥 random aman
            ]);
        }

        // 🔥 SIMPAN BOOKING (JANGAN DOUBLE DATA)
        $booking = Booking::create([
            'invoice_code' => 'WIN-' . now()->format('dmy') . '-' . rand(100,999),
            'user_id' => $customer->id,
            'guest_name' => null, // 🔥 PENTING: kosongkan
            'booking_date' => $request->date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $treatment->price,
            'dp_amount' => 0,
            'payment_status' => 'paid_full',
            'booking_status' => 'completed',
        ]);

        BookingItem::create([
            'booking_id' => $booking->id,
            'treatment_id' => $treatment->id,
            'scheduled_date' => $request->date,
            'scheduled_time' => $startTime,
            'price_at_booking' => $treatment->price
        ]);

        return redirect()->route('admin.invoice', $booking->id);
    });
}

    public function invoice($id)
    {
        $booking = Booking::with('items.treatment')->findOrFail($id);
        return view('admin.invoice.show', compact('booking'));
    }
}