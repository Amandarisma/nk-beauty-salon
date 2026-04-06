<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\User;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        // 🔥 LOGIKA BARU: Ambil jam saat ini di server
        $nowTime = Carbon::now()->format('H:i:s');

        // 1. Total Booking Hari Ini
        $totalBookingsToday = Booking::whereDate('booking_date', $today)->count();

        // 🔥 2. Hitung ANTREAN SELANJUTNYA (Belum selesai, belum batal, dan Jam BElUM LEWAT)
        $antreanSelanjutnya = Booking::whereDate('booking_date', $today)
            ->whereNotIn('booking_status', ['completed', 'cancelled'])
            ->whereTime('start_time', '>=', $nowTime)
            ->count();

        // 3. Ambil Data Kalender
        $bookings = Booking::with(['user', 'items.treatment'])
            ->where('booking_status', '!=', 'cancelled')
            ->get();
            
        $events = [];

        foreach($bookings as $booking) {
            $firstItem = $booking->items->first();

            if($firstItem) {
                $eventDateTime = Carbon::parse($firstItem->scheduled_date . ' ' . $firstItem->scheduled_time);
                
                $totalAsli = $booking->total_price;
                
                if ($booking->payment_status == 'paid') {
                    $sudahDp = $totalAsli; 
                } else {
                    $sudahDp = $booking->dp_amount ?? 0;
                }
                
                $sisaPelunasan = $totalAsli - $sudahDp;

                $isPastDay = Carbon::parse($firstItem->scheduled_date)->isBefore(Carbon::today());

                if ($booking->booking_status == 'completed' || $isPastDay) {
                    $bgColor = '#f3f4f6'; $borderColor = '#e5e7eb'; $textColor = '#6b7280'; 
                } elseif ($booking->payment_status == 'paid') {
                    $bgColor = '#d1fae5'; $borderColor = '#a7f3d0'; $textColor = '#065f46'; 
                } elseif ($booking->payment_status == 'paid_dp') {
                    $bgColor = '#dbeafe'; $borderColor = '#bfdbfe'; $textColor = '#1e40af'; 
                } else {
                    $bgColor = '#fef3c7'; $borderColor = '#fde68a'; $textColor = '#92400e'; 
                }

                $isWalkIn = strpos($booking->invoice_code, 'WIN-') !== false;
                $customerName = $booking->user ? $booking->user->name : 'Tamu';
                if ($isWalkIn) $customerName .= ' (Walk-in)';

                $treatmentList = $booking->items->map(function($item) {
                    return '• ' . ($item->treatment->name ?? 'Layanan');
                })->implode('<br>');

                $formattedTime = $eventDateTime->translatedFormat('d M Y') . ' - ' . $eventDateTime->format('H:i') . ' WIB';

                $isWaiting = !in_array($booking->booking_status, ['completed', 'cancelled']);

                $events[] = [
                    'id' => $booking->id,
                    'title' => $customerName,
                    'start' => $firstItem->scheduled_date . 'T' . $firstItem->scheduled_time,
                    'backgroundColor' => $bgColor,
                    'borderColor' => $borderColor,
                    'textColor' => $textColor,
                    'is_waiting' => $isWaiting, 
                    'waktu_lengkap' => $formattedTime,
                    'layanan' => $treatmentList,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->booking_status,
                    'total_asli' => number_format($totalAsli, 0, ',', '.'),
                    'sudah_dp' => number_format($sudahDp, 0, ',', '.'),
                    'sisa' => number_format($sisaPelunasan, 0, ',', '.')
                ];
            }
        }

        // 🔥 Bawa data $antreanSelanjutnya ke tampilan
        return view('admin.dashboard', compact('totalBookingsToday', 'antreanSelanjutnya', 'events'));
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->booking_status = $request->status; 
        $booking->save();

        $pesan = $request->status == 'completed' 
            ? 'Booking berhasil diselesaikan!' 
            : 'Booking telah dibatalkan.';

        return back()->with('success', $pesan);
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
            'date' => 'required|date',
            'time' => 'required',
            'treatment_ids' => 'required|array', 
            'treatment_ids.*' => 'exists:treatments,id',
        ]);

        return DB::transaction(function () use ($request) {
            $treatments = Treatment::whereIn('id', $request->treatment_ids)->get();
            $totalPrice = $treatments->sum('price');
            $totalDuration = $treatments->sum('duration');

            $startTime = Carbon::parse($request->date . ' ' . $request->time);
            $endTime = $startTime->copy()->addMinutes($totalDuration);

            if ($request->filled('user_id')) {
                $customer = User::findOrFail($request->user_id);
            } else {
                $request->validate([
                    'guest_name' => 'required|string',
                    'phone' => 'required|string',
                ]);

                $name = ucfirst(strtolower(trim($request->guest_name)));
                $phone = preg_replace('/[^0-9]/', '', $request->phone);
                if (substr($phone, 0, 1) == '0') { $phone = '62' . substr($phone, 1); }

                $customer = User::where('phone', $phone)->first() ?? User::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $phone . '@walkin.local',
                    'role' => 'user',
                    'password' => bcrypt(uniqid()),
                ]);
            }

            $booking = Booking::create([
                'invoice_code' => 'WIN-' . now()->format('YmdHis'),
                'user_id' => $customer->id,
                'booking_date' => $request->date,
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'total_price' => $totalPrice, 
                'dp_amount' => 0,
                'payment_status' => 'paid',
                'booking_status' => 'confirmed',
            ]);

            $currentTime = $startTime->copy();
            foreach ($treatments as $treatment) {
                BookingItem::create([
                    'booking_id' => $booking->id,
                    'treatment_id' => $treatment->id,
                    'scheduled_date' => $request->date,
                    'scheduled_time' => $currentTime->format('H:i:s'),
                    'price_at_booking' => $treatment->price
                ]);
                $currentTime->addMinutes($treatment->duration);
            }

            return redirect()->route('admin.invoice', $booking->id);
        });
    }

    public function invoice($id)
    {
        $booking = Booking::with('items.treatment')->findOrFail($id);
        return view('admin.invoice.show', compact('booking'));
    }
}