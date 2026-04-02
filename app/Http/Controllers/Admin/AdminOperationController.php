<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Treatment;
use App\Models\User;
use App\Models\BookingItem; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOperationController extends Controller
{
    // 1. DASHBOARD UTAMA (KALENDER)
    public function dashboard()
    {
        // Widget Ringkasan
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('booking_status', 'new')->count();
        $totalCustomers = User::where('role', 'user')->count();

        // LOGIKA BARU: Grouping per Booking Header
        $bookings = Booking::with(['user', 'items.treatment'])->get();
        
        $events = [];
        foreach($bookings as $booking) {
            // Kita ambil jadwal dari item pertama sebagai patokan waktu mulai
            $firstItem = $booking->items->first();
            
            if($firstItem) {
                // Tentukan Warna Event (Pastel Style)
                // Kuning Soft (Menunggu) vs Hijau Soft (Lunas/DP)
                $color = ($booking->payment_status == 'paid_dp' || $booking->payment_status == 'paid_full') 
                         ? '#10b981'  // Emerald Green
                         : '#f59e0b'; // Amber/Yellow

                // Nama Customer
                $customerName = $booking->user ? $booking->user->name : ($booking->guest_name . ' (Walk-in)');
                
                // List Treatments (Gabungkan nama treatment jadi satu string)
                $treatmentNames = $booking->items->map(function($item) {
                    return '• ' . $item->treatment->name;
                })->implode('<br>');

                // Hitung Sisa Pelunasan
                $sisaBayar = $booking->total_price - $booking->dp_amount;

                $events[] = [
                    'title' => $customerName, // Judul cuma Nama Orang
                    'start' => $firstItem->scheduled_date . 'T' . $firstItem->scheduled_time,
                    'color' => $color,
                    // Data tambahan untuk Pop-up
                    'extendedProps' => [
                        'treatments' => $treatmentNames,
                        'total_asli' => number_format($booking->total_price, 0, ',', '.'),
                        'sudah_bayar' => number_format($booking->dp_amount, 0, ',', '.'),
                        'sisa_bayar' => number_format($sisaBayar, 0, ',', '.'),
                        'status' => ucfirst($booking->payment_status)
                    ]
                ];
            }
        }

        return view('admin.dashboard', compact('totalBookings', 'pendingBookings', 'totalCustomers', 'events'));
    }

    // ... (Method createWalkIn dan storeWalkIn biarkan tetap sama seperti sebelumnya) ...
    // Agar tidak error, sertakan method-method tersebut di bawah ini:

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
            'treatment_id' => 'required|exists:treatments,id',
        ]);

        return DB::transaction(function () use ($request) {
            $treatment = Treatment::find($request->treatment_id);
            
            $booking = Booking::create([
                'invoice_code' => 'WIN-' . now()->format('dmY') . '-' . rand(100,999),
                'user_id' => $request->user_id, 
                'guest_name' => $request->guest_name, 
                'total_price' => $treatment->price,
                'dp_amount' => 0, 
                'payment_status' => 'paid_full', 
                'booking_status' => 'completed',
                // 'type' => 'walk_in' // Aktifkan jika kolom type ada di migrasi
            ]);

            BookingItem::create([
                'booking_id' => $booking->id,
                'treatment_id' => $treatment->id,
                'scheduled_date' => $request->date,
                'scheduled_time' => $request->time,
                'price_at_booking' => $treatment->price
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Transaksi Walk-in Berhasil Disimpan!');
        });
    }
}