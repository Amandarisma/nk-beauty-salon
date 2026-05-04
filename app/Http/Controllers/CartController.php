<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * MENAMPILKAN HALAMAN KERANJANG
     */
    public function index()
    {
        if (auth()->user()->role !== 'user') {
            abort(403);
        }

        $carts = Cart::with('treatment')
            ->where('user_id', Auth::id())
            ->get();

        return view('cart.index', compact('carts'));
    }

    /**
     * MENAMBAHKAN LAYANAN KE KERANJANG
     */
    public function store(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        // 🔥 PROTEKSI BARU: Cek Jam Terlewat Pakai Waktu Jakarta! 🔥
        $nowJakarta = Carbon::now('Asia/Jakarta');
        $todayDate = $nowJakarta->format('Y-m-d');
        $bookingDateStr = Carbon::parse($request->booking_date)->format('Y-m-d');
        
        // Ambil angkanya aja (misal "10:00 WIB" jadi "10:00")
        $timeStr = substr($request->booking_time, 0, 5); 

        // Kalau bookingnya hari ini, dan jamnya udah lewat dari jam sekarang: TOLAK!
        if ($bookingDateStr === $todayDate && $timeStr <= $nowJakarta->format('H:i')) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'title' => 'Waktu Terlewat! ⏰',
                'message' => "Jam {$request->booking_time} hari ini sudah lewat. Silakan pilih jam lain yang masih tersedia ya!",
            ]);
        }

        // 🔥 Cek Duplikasi
        $sudahAda = Cart::where('user_id', Auth::id())
                        ->where('treatment_id', $request->treatment_id)
                        ->exists();

        if ($sudahAda) {
            return redirect()->back()->with('alert', [
                'type' => 'warning',
                'title' => 'Sudah Ada! 😅',
                'message' => 'Layanan ini sudah masuk di keranjangmu. Jika ingin memesan untuk orang lain, silakan buat booking terpisah ya!',
            ]);
        }

        // Eksekusi Simpan
        Cart::create([
            'user_id' => Auth::id(),
            'treatment_id' => $request->treatment_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
        ]);

        $treatment = \App\Models\Treatment::find($request->treatment_id);

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'title' => 'Masuk Keranjang! 🛒',
            'message' => 'Layanan <b>' . $treatment->name . '</b> siap untuk di-checkout.',
            'date' => Carbon::parse($request->booking_date)->translatedFormat('d F Y'),
            'time' => Carbon::parse($request->booking_time)->format('H:i') . ' WIB',
            'context' => 'cart_add' 
        ]);
    }

    /**
     * MENGHAPUS ITEM DARI KERANJANG
     */
    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);

        if ($cart->user_id == Auth::id()) {
            $cart->delete();
            return back()->with('alert', [
                'type' => 'success',
                'title' => 'Terhapus!',
                'message' => 'Item berhasil dibuang dari keranjang.',
                'context' => 'cart_delete'
            ]);
        }

        return back()->with('error', 'Akses ditolak.');
    }

    /**
     * UPDATE JADWAL SECARA MASAL (AJAX)
     */
    public function updateSchedule(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date',
            'booking_time' => 'required',
        ]);

        $user = auth()->user();

        Cart::where('user_id', $user->id)
            ->update([
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * MENGHITUNG TOTAL DURASI LAYANAN (AJAX)
     */
    public function getTotalDuration()
    {
        $user = auth()->user();
        
        $total = Cart::with('treatment')
            ->where('user_id', $user->id)
            ->get()
            ->sum(function ($item) {
                return $item->treatment->duration;
            });

        return response()->json(['duration' => $total]);
    }
}