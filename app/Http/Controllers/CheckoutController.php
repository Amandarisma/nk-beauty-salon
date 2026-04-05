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

        // Validasi: Harus ada tanggal & jam
        if ($carts->contains(fn($c) => !$c->booking_date || !$c->booking_time)) {
            return back()->with('error', 'Masih ada layanan yang belum pilih tanggal/jam.');
        }

        // Validasi: Harus 1 tanggal
        $firstDate = $carts->first()->booking_date;

        if (!$carts->every(fn($c) => $c->booking_date == $firstDate)) {
            return back()->with('error', 'Semua layanan harus di tanggal yang sama.');
        }

        // Sort berdasarkan jam
        $carts = $carts->sortBy('booking_time')->values();

        // 🔥 CEK BENTROK PER ITEM (ABAIKAN YANG PENDING ATAU BATAL)
        foreach ($carts as $cart) {
            $start = Carbon::parse($cart->booking_date . ' ' . $cart->booking_time);
            $end   = $start->copy()->addMinutes($cart->treatment->duration);

            if ($start->lt(now())) {
                return back()->with('error', 'Tidak bisa booking di waktu yang sudah lewat.');
            }

            // Cek overlap: HANYA PEDULI JADWAL YANG UDAH BAYAR DAN GAK BATAL
            $conflict = BookingItem::join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->where('booking_items.scheduled_date', $cart->booking_date)
                ->whereIn('bookings.payment_status', ['paid', 'paid_dp'])
                ->where('bookings.booking_status', '!=', 'cancelled')
                ->where(function ($query) use ($start, $end) {
                    $query->where('booking_items.scheduled_time', '<', $end->format('H:i:s'))
                          ->where(DB::raw("ADDTIME(booking_items.scheduled_time, SEC_TO_TIME(60 * 60))"), '>', $start->format('H:i:s'));
                })
                ->exists();

            if ($conflict) {
                return back()->with('error', 'Jadwal bentrok! Pilih waktu lain.');
            }
        }

        // Hitung total durasi dan harga
        $totalDuration = $carts->sum(fn($c) => $c->treatment->duration);
        $startAll = Carbon::parse($firstDate . ' ' . $carts->first()->booking_time);
        $endAll   = $startAll->copy()->addMinutes($totalDuration);
        $totalPrice = $carts->sum(fn($c) => $c->treatment->price);

        // Opsi Pembayaran Lunas/DP
        $paymentType = $request->input('payment_type', 'dp');
        $dpAmount = ($paymentType === 'full') ? $totalPrice : ($totalPrice * 0.3);

        // Simpan Booking ke Database
        $booking = Booking::create([
            'invoice_code'   => 'INV-' . now()->format('YmdHis') . '-' . $user->id,
            'user_id'        => $user->id,
            'booking_date'   => $firstDate,
            'start_time'     => $startAll->format('H:i:s'),
            'end_time'       => $endAll->format('H:i:s'),
            'total_price'    => $totalPrice,
            'dp_amount'      => $dpAmount,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        // Simpan Item Layanan
        $currentTime = $startAll->copy();
        foreach ($carts as $cart) {
            BookingItem::create([
                'booking_id'       => $booking->id,
                'treatment_id'     => $cart->treatment_id,
                'scheduled_date'   => $cart->booking_date,
                'scheduled_time'   => $currentTime->format('H:i:s'),
                'price_at_booking' => $cart->treatment->price,
            ]);
            $currentTime->addMinutes($cart->treatment->duration);
        }

        // Hapus Cart
        Cart::where('user_id', $user->id)->delete();

        return redirect()->route('booking.payment', $booking->id);
    }

    /**
     * 🔥 STEP 2: TAMPILKAN HALAMAN PEMBAYARAN
     */
    public function showPayment($id)
    {
        $booking = Booking::with('items.treatment')->findOrFail($id);
        return view('booking.payment', compact('booking'));
    }

    /**
     * 🔥 STEP 3: KONFIRMASI PEMBAYARAN (SISTEM REBUTAN JADWAL)
     */
    public function confirmPayment($id)
    {
        $booking = Booking::with('items.treatment')->findOrFail($id);

        // 🔥 CEK REBUTAN: Apakah jadwal ini sudah direbut orang lain yang bayar duluan?
        foreach ($booking->items as $item) {
            $start = Carbon::parse($item->scheduled_date . ' ' . $item->scheduled_time);
            $end   = $start->copy()->addMinutes($item->treatment->duration ?? 60);

            $keduluan = BookingItem::join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                ->where('bookings.id', '!=', $booking->id) // Jangan cek diri sendiri
                ->where('booking_items.scheduled_date', $item->scheduled_date)
                ->whereIn('bookings.payment_status', ['paid', 'paid_dp']) // Cek yg udah bayar
                ->where('bookings.booking_status', '!=', 'cancelled')
                ->where(function ($query) use ($start, $end) {
                    $query->where('booking_items.scheduled_time', '<', $end->format('H:i:s'))
                          ->where(DB::raw("ADDTIME(booking_items.scheduled_time, SEC_TO_TIME(60 * 60))"), '>', $start->format('H:i:s'));
                })
                ->exists();

            if ($keduluan) {
                // Batalkan otomatis booking yang telat bayar ini
                $booking->update(['booking_status' => 'cancelled']);
                
                return redirect()->route('user.bookings')->with('alert', [
                    'type' => 'error',
                    'title' => 'Keduluan! 😭',
                    'message' => 'Maaf, jadwal ini baru saja dibayar oleh pelanggan lain. Silakan buat reservasi ulang di jam yang kosong ya!',
                    'context' => 'payment_failed'
                ]);
            }
        }

        // JIKA AMAN, LANJUT SIMPAN PEMBAYARAN
        $isLunas = $booking->dp_amount >= $booking->total_price;
        $statusPembayaran = $isLunas ? 'paid' : 'paid_dp';
        $pesanPopUp = $isLunas 
            ? 'Booking kamu sudah dikonfirmasi secara LUNAS. Terima kasih!' 
            : 'Booking kamu sudah dikonfirmasi dengan pembayaran DP (Uang Muka).';

        $booking->payment_status = $statusPembayaran;
        $booking->booking_status = 'confirmed';
        $booking->save();

        return redirect()->route('user.bookings')
            ->with('alert', [
                'type' => 'success',
                'title' => 'Pembayaran Berhasil!',
                'message' => $pesanPopUp,
                'context' => 'payment'
            ]);
    }

    /**
     * 🔥 FUNGSI USER MEMBATALKAN BOOKING SENDIRI
     */
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id == Auth::id() && $booking->payment_status == 'pending') {
            $booking->update([
                'booking_status' => 'cancelled'
            ]);

            return back()->with('alert', [
                'type' => 'success',
                'title' => 'Dibatalkan!',
                'message' => 'Reservasi kamu berhasil dibatalkan.',
            ]);
        }

        return back()->with('error', 'Reservasi tidak dapat dibatalkan.');
    }

    /**
     * 🔥 MENCARI JADWAL YANG SUDAH DIBOOKING (HANYA YANG UDAH BAYAR)
     */
    public function getBookedSlots(Request $request)
    {
        $date = $request->query('date');
        if (!$date) return response()->json([]);

        // 🔥 KUNCI: HANYA BLOKIR JAM JIKA STATUSNYA SUDAH BAYAR DAN TIDAK BATAL
        $items = BookingItem::with('treatment')
            ->whereHas('booking', function($query) {
                $query->whereIn('payment_status', ['paid', 'paid_dp'])
                      ->where('booking_status', '!=', 'cancelled');
            })
            ->where('scheduled_date', $date)
            ->get();

        $blockedSlots = [];

        foreach ($items as $item) {
            if (!$item->treatment) continue;
            
            $start = Carbon::parse($item->scheduled_time);
            $end = $start->copy()->addMinutes($item->treatment->duration);

            $current = Carbon::parse('10:00');
            $endOfDay = Carbon::parse('17:30');

            while ($current <= $endOfDay) {
                $slotStart = $current->copy();
                if ($slotStart >= $start && $slotStart < $end) {
                    $blockedSlots[] = $current->format('H:i');
                }
                $current->addMinutes(30);
            }
        }

        return response()->json(array_values(array_unique($blockedSlots)));
    }
}