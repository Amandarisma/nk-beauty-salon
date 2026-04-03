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

class AdminOperationController extends Controller
{
    public function dashboard()
    {
        // 1. Ambil Tanggal Hari Ini
        $today = Carbon::today();

        // 2. Hitung Data Statistik untuk Card (Hanya Hari Ini)
        $totalBookingsToday = Booking::whereDate('booking_date', $today)->count();

        $pendingBookingsToday = Booking::whereDate('booking_date', $today)
            ->whereIn('booking_status', ['pending', 'confirmed', 'new'])
            ->count();

        $totalCustomers = User::where('role', 'user')->count();

        // 3. Ambil Data Untuk Kalender (Variabel $events)
        $bookings = Booking::with(['user', 'items.treatment'])->get();
        $events = [];

        foreach($bookings as $booking) {
            $firstItem = $booking->items->first();

            if($firstItem) {
                $eventDateTime = Carbon::parse($firstItem->scheduled_date . ' ' . $firstItem->scheduled_time);
                $isPast = $eventDateTime->isPast();

                // Logika Status Pembayaran
                $isPending = ($booking->payment_status == 'pending');
                $sudahDp = $isPending ? 0 : $booking->dp_amount;
                $sisaPelunasan = $booking->total_price - $sudahDp;

                // Penentuan Warna Pastel
                if ($isPast || $booking->booking_status == 'completed') {
                    $bgColor = '#f3f4f6'; // Abu-abu (Selesai/Lewat)
                    $borderColor = '#e5e7eb';
                    $textColor = '#9ca3af';
                } else {
                    if (!$isPending) {
                        $bgColor = '#d1fae5'; // Hijau (Lunas/DP)
                        $borderColor = '#a7f3d0';
                        $textColor = '#065f46';
                    } else {
                        $bgColor = '#fef3c7'; // Kuning (Menunggu)
                        $borderColor = '#fde68a';
                        $textColor = '#92400e';
                    }
                }

                // Nama Pelanggan & Label Walk-in
                $isWalkIn = strpos($booking->invoice_code, 'WIN-') !== false;
                $customerName = $booking->user ? $booking->user->name : 'Tamu';
                if ($isWalkIn) {
                    $customerName .= ' (Walk-in)';
                }

                // Format List Layanan
                $treatmentList = $booking->items->map(function($item) {
                    return '• ' . $item->treatment->name;
                })->implode('<br>');

                $formattedTime = $eventDateTime->translatedFormat('d M Y') . ' - ' . $eventDateTime->format('H:i') . ' WIB';

                // Masukkan ke Array Events
                $events[] = [
                    'title' => $customerName,
                    'start' => $firstItem->scheduled_date . 'T' . $firstItem->scheduled_time,
                    'backgroundColor' => $bgColor,
                    'borderColor' => $borderColor,
                    'textColor' => $textColor,
                    'waktu_lengkap' => $formattedTime,
                    'layanan' => $treatmentList,
                    'total_asli' => number_format($booking->total_price, 0, ',', '.'),
                    'sudah_dp' => number_format($sudahDp, 0, ',', '.'),
                    'sisa' => number_format($sisaPelunasan, 0, ',', '.')
                ];
            }
        }

        // 4. Kirim Semua Variabel ke View
        return view('admin.dashboard', compact(
            'totalBookingsToday', 
            'pendingBookingsToday', 
            'totalCustomers', 
            'events'
        ));
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