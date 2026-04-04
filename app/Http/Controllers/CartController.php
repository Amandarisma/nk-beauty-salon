<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
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

 public function store(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        // 🔥 LOGIKA SALON: Cegah layanan yang sama masuk 2x di keranjang
        $sudahAda = Cart::where('user_id', Auth::id())
                        ->where('treatment_id', $request->treatment_id)
                        ->exists();

        if ($sudahAda) {
            return redirect()->back()->with('alert', [
                'type' => 'warning', // Peringatan warna kuning
                'title' => 'Sudah Ada! 😅',
                'message' => 'Layanan ini sudah masuk di keranjangmu. Jika ingin memesan untuk orang lain, silakan buat booking terpisah ya!',
            ]);
        }

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
            'date' => \Carbon\Carbon::parse($request->booking_date)->translatedFormat('d F Y'),
            'time' => \Carbon\Carbon::parse($request->booking_time)->format('H:i') . ' WIB',
            'context' => 'cart_add' 
        ]);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);

        if ($cart->user_id == Auth::id()) {
            $cart->delete();
            return back()->with('alert', [
                'type' => 'success',
                'title' => 'Terhapus!',
                'message' => 'Item berhasil dibuang dari keranjang.',
                'context' => 'cart_delete' // <-- Penanda Pop Up Hapus
            ]);
        }

        return back()->with('error', 'Akses ditolak.');
    }

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