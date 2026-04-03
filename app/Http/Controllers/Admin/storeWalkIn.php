<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOperationController extends Controller
{
    public function storeWalkIn(Request $request)
    {
        $request->validate([
            'guest_name' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'treatment_id' => 'required|exists:treatments,id',
        ]);

        return DB::transaction(function () use ($request) {

            $treatment = Treatment::findOrFail($request->treatment_id);

            $startTime = date('H:i:s', strtotime($request->time));
            $endTime = date('H:i:s', strtotime($request->time . ' + ' . $treatment->duration . ' minutes'));

            $booking = Booking::create([
                'invoice_code' => 'WIN-' . now()->format('dmy') . '-' . rand(100,999),
                'user_id' => auth()->id(),
                'guest_name' => $request->guest_name,
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
}