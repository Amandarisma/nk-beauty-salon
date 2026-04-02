<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // 1. MENAMPILKAN ISI KERANJANG
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

    // 2. TAMBAH KE CART
    public function store(Request $request)
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        Cart::create([
            'user_id' => Auth::id(),
            'treatment_id' => $request->treatment_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
        ]);

        return redirect()->back()->with('alert', [
    'type' => 'success',
    'title' => 'Berhasil!',
    'message' => 'Berhasil ditambahkan ke keranjang.',
    'context' => 'cart'
]);
    }

    // 3. HAPUS CART
    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);

        if ($cart->user_id == Auth::id()) {
            $cart->delete();
            return back()->with('alert', [
    'type' => 'success',
    'title' => 'Berhasil!',
    'message' => 'Item berhasil dihapus dari keranjang.',
    'context' => 'cart'
]);
        }

        return back()->with('error', 'Akses ditolak.');
    }

    // 🔥 4. UPDATE SCHEDULE REALTIME
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

    // 🔥 5. TOTAL DURASI (SMART SLOT)
    public function getTotalDuration()
    {
        $user = auth()->user();

        $total = Cart::with('treatment')
            ->where('user_id', $user->id)
            ->get()
            ->sum(function ($item) {
                return $item->treatment->duration;
            });

        return response()->json([
            'duration' => $total
        ]);
    }
    
}