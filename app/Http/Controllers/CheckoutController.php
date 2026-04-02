<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Booking;
use App\Models\BookingItem;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * 🔥 STEP 1: PROCESS CHECKOUT
     * - Validasi
     * - Cek bentrok
     * - Simpan booking (pending)
     * - Redirect ke halaman pembayaran
     */
    public function process(Request $request)
    {
        // 🔥 AMBIL USER
        $user = Auth::user();

        if (!$user || $user->role !== 'user') {
            abort(403, 'Hanya user yang bisa booking.');
        }

        // 🔥 AMBIL CART
        $carts = $user->carts()->with('treatment')->get();

        if ($carts->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // 🔥 VALIDASI: SEMUA HARUS ADA TANGGAL & JAM
        if ($carts->contains(function ($c) {
            return !$c->booking_date || !$c->booking_time;
        })) {
            return back()->with('error', 'Masih ada layanan yang belum pilih tanggal/jam.');
        }

        // 🔥 AMBIL DATA AWAL
        $firstDate = $carts->first()->booking_date;
        $firstTime = $carts->first()->booking_time;

        // 🔥 VALIDASI: HARUS DI TANGGAL SAMA
        $sameDate = $carts->every(function ($c) use ($firstDate) {
            return $c->booking_date == $firstDate;
        });

        if (!$sameDate) {
            return back()->with('error', 'Semua layanan harus di tanggal yang sama.');
        }

        // 🔥 HITUNG DURASI TOTAL
        $totalDuration = $carts->sum(function ($item) {
            return $item->treatment->duration ?? 0;
        });

        // 🔥 HITUNG JAM
        $start = Carbon::parse($firstDate . ' ' . $firstTime);
        $end   = $start->copy()->addMinutes($totalDuration);

        // 🔥 VALIDASI MASA LALU
        if ($start->lt(now())) {
            return back()->with('error', 'Tidak bisa booking di waktu yang sudah lewat.');
        }

        // 🔥 CEK BENTROK (OVERLAP)
        $conflict = Booking::where('booking_date', $firstDate)
            ->where(function ($query) use ($start, $end) {
                $query->where('start_time', '<', $end->format('H:i:s'))
                      ->where('end_time', '>', $start->format('H:i:s'));
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Jam sudah terisi, silakan pilih jam lain.');
        }

        // 🔥 HITUNG TOTAL HARGA
        $totalPrice = $carts->sum(function ($c) {
            return $c->treatment->price ?? 0;
        });

        // 🔥 SIMPAN BOOKING (STATUS: BELUM BAYAR)
        $booking = Booking::create([
            'invoice_code'   => 'INV-' . now()->format('YmdHis') . '-' . $user->id,
            'user_id'        => $user->id,
            'booking_date'   => $firstDate,
            'start_time'     => $start->format('H:i:s'),
            'end_time'       => $end->format('H:i:s'),
            'total_price'    => $totalPrice,
            'dp_amount'      => $totalPrice * 0.3,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        // 🔥 SIMPAN DETAIL
        foreach ($carts as $cart) {
            BookingItem::create([
                'booking_id'       => $booking->id,
                'treatment_id'     => $cart->treatment_id,
                'scheduled_date'   => $cart->booking_date,
                'scheduled_time'   => $cart->booking_time,
                'price_at_booking' => $cart->treatment->price ?? 0,
            ]);
        }

        // 🔥 HAPUS CART
        Cart::where('user_id', $user->id)->delete();

        // 🔥 PINDAH KE HALAMAN PEMBAYARAN
        return redirect()->route('booking.payment', $booking->id)
    ->with('alert', [
        'type' => 'success',
        'title' => 'Booking Dibuat',
        'message' => 'Silakan lanjut ke pembayaran.',
        'context' => 'booking'
    ]);
    }


    /**
     * 🔥 STEP 2: HALAMAN PEMBAYARAN
     */
    public function showPayment($id)
    {
        $booking = Booking::with('items.treatment')->findOrFail($id);

        return view('booking.payment', compact('booking'));
    }


    /**
     * 🔥 STEP 3: KONFIRMASI PEMBAYARAN
     */
    public function confirmPayment($id)
    {
        $booking = Booking::findOrFail($id);

        // 🔥 UPDATE STATUS
        $booking->update([
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
        ]);

        // 🔥 POPUP SUKSES
       return redirect('/')
    ->with('alert', [
        'type' => 'success',
        'title' => 'Pembayaran Berhasil!',
        'message' => 'Booking kamu sudah dikonfirmasi.',
        'context' => 'payment'
    ]);
    }
}