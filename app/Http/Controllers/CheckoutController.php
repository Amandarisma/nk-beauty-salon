<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Booking;
use App\Models\BookingItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * 🔥 STEP 1: PROCESS CHECKOUT
     */
    public function process(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'user') {
            abort(403, 'Hanya user yang bisa booking.');
        }

        $carts = $user->carts()->with('treatment')->get();

        if ($carts->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // 🔥 VALIDASI: HARUS ADA TANGGAL & JAM
        if ($carts->contains(fn($c) => !$c->booking_date || !$c->booking_time)) {
            return back()->with('error', 'Masih ada layanan yang belum pilih tanggal/jam.');
        }

        // 🔥 VALIDASI: HARUS 1 TANGGAL
        $firstDate = $carts->first()->booking_date;

        if (!$carts->every(fn($c) => $c->booking_date == $firstDate)) {
            return back()->with('error', 'Semua layanan harus di tanggal yang sama.');
        }

        // 🔥 SORT BERDASARKAN JAM
        $carts = $carts->sortBy('booking_time')->values();

        // 🔥 CEK BENTROK PER ITEM
        foreach ($carts as $cart) {

            $start = Carbon::parse($cart->booking_date . ' ' . $cart->booking_time);
            $end   = $start->copy()->addMinutes($cart->treatment->duration);

            // ❌ MASA LALU
            if ($start->lt(now())) {
                return back()->with('error', 'Tidak bisa booking di waktu yang sudah lewat.');
            }

            // 🔥 CEK OVERLAP DENGAN DATABASE
            $conflict = BookingItem::join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->where('booking_items.scheduled_date', $cart->booking_date)
                ->where(function ($query) use ($start, $end) {
                    $query->where('booking_items.scheduled_time', '<', $end->format('H:i:s'))
                          ->where(DB::raw("ADDTIME(booking_items.scheduled_time, SEC_TO_TIME(60 * 60))"), '>', $start->format('H:i:s'));
                })
                ->exists();

            if ($conflict) {
                return back()->with('error', 'Jadwal bentrok! Pilih waktu lain.');
            }
        }

        // 🔥 HITUNG TOTAL DURASI
        $totalDuration = $carts->sum(fn($c) => $c->treatment->duration);

        $startAll = Carbon::parse($firstDate . ' ' . $carts->first()->booking_time);
        $endAll   = $startAll->copy()->addMinutes($totalDuration);

// 🔥 HITUNG TOTAL HARGA
        $totalPrice = $carts->sum(fn($c) => $c->treatment->price);

        // 🔥 LOGIKA BARU: Cek Pilihan Pembayaran (DP 30% atau Lunas 100%)
        $paymentType = $request->input('payment_type', 'dp'); // Defaultnya DP
        $dpAmount = ($paymentType === 'full') ? $totalPrice : ($totalPrice * 0.3);

        // 🔥 SIMPAN BOOKING
        $booking = Booking::create([
            'invoice_code'   => 'INV-' . now()->format('YmdHis') . '-' . $user->id,
            'user_id'        => $user->id,
            'booking_date'   => $firstDate,
            'start_time'     => $startAll->format('H:i:s'),
            'end_time'       => $endAll->format('H:i:s'),
            'total_price'    => $totalPrice,
            'dp_amount'      => $dpAmount, // <-- Ini yang menentukan nominal masuknya!
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        // 🔥 SIMPAN ITEM (SEQUENTIAL TIME)
        $currentTime = $startAll->copy();

        foreach ($carts as $cart) {

            BookingItem::create([
                'booking_id'       => $booking->id,
                'treatment_id'     => $cart->treatment_id,
                'scheduled_date'   => $cart->booking_date,
                'scheduled_time'   => $currentTime->format('H:i:s'),
                'price_at_booking' => $cart->treatment->price,
            ]);

            // ⏩ geser waktu sesuai durasi
            $currentTime->addMinutes($cart->treatment->duration);
        }

        // 🔥 HAPUS CART
        Cart::where('user_id', $user->id)->delete();

        return redirect()->route('booking.payment', $booking->id)
            ->with('alert', [
                'type' => 'success',
                'title' => 'Booking Dibuat',
                'message' => 'Silakan lanjut ke pembayaran.',
                'context' => 'booking'
            ]);
    }

    /**
     * 🔥 STEP 2
     */
    public function showPayment($id)
    {
        $booking = Booking::with('items.treatment')->findOrFail($id);
        return view('booking.payment', compact('booking'));
    }

    /**
     * 🔥 STEP 3
     */
/**
     * 🔥 STEP 3
     */
    public function confirmPayment($id)
    {
        $booking = Booking::findOrFail($id);

        // 🔥 CEK OTOMATIS: Apakah uang masuk (dp_amount) sudah sama dengan Total?
        // Kalau sama berarti Lunas ('paid'), kalau kurang berarti cuma DP ('paid_dp').
        $statusPembayaran = ($booking->dp_amount >= $booking->total_price) ? 'paid' : 'paid_dp';

        $booking->update([
            'payment_status' => $statusPembayaran,
            'booking_status' => 'confirmed',
        ]);

        return redirect()->route('user.bookings') // Biar langsung balik ke halaman riwayat
            ->with('alert', [
                'type' => 'success',
                'title' => 'Pembayaran Berhasil!',
                'message' => 'Booking kamu sudah dikonfirmasi.',
                'context' => 'payment'
            ]);
    }

    /**
     * 🔥 MENCARI JADWAL YANG SUDAH DIBOOKING (AJAX)
     */
    public function getBookedSlots(Request $request)
    {
        $date = $request->query('date');
        if (!$date) return response()->json([]);

        // Cari semua item booking di tanggal tersebut
        $items = BookingItem::with('treatment')
            ->where('scheduled_date', $date)
            ->get();

        $blockedSlots = [];

        foreach ($items as $item) {
            if (!$item->treatment) continue;
            
            $start = Carbon::parse($item->scheduled_time);
            $end = $start->copy()->addMinutes($item->treatment->duration);

            // Cek setiap interval 30 menit (jam operasional 10:00 - 17:00)
            $current = Carbon::parse('10:00');
            $endOfDay = Carbon::parse('17:30');

            while ($current <= $endOfDay) {
                $slotStart = $current->copy();
                // Jika jam ini berada di tengah-tengah jadwal orang lain, blokir!
                if ($slotStart >= $start && $slotStart < $end) {
                    $blockedSlots[] = $current->format('H:i');
                }
                $current->addMinutes(30);
            }
        }

        // Kembalikan daftar jam yang harus diblokir ke tampilan depan
        return response()->json(array_values(array_unique($blockedSlots)));
    }
}