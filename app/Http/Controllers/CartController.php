<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * MENAMPILKAN HALAMAN KERANJANG
     * Blok ini bertugas mengambil semua item yang sudah dipilih user
     * dan menampilkannya di halaman daftar belanja (keranjang).
     * 
     * Buat masukin layanan sebelum dibayar
     */
    public function index()
    {
        // Keamanan: Hanya user biasa yang boleh punya keranjang
        if (auth()->user()->role !== 'user') {
            abort(403);
        }

        // Ambil data keranjang milik user yang sedang login beserta detail layanannya (treatment)
        $carts = Cart::with('treatment')
            ->where('user_id', Auth::id())
            ->get();

        return view('cart.index', compact('carts'));
    }

    /**
     * MENAMBAHKAN LAYANAN KE KERANJANG
     * Blok ini berjalan saat user klik tombol "Tambah ke Keranjang" di halaman detail layanan.
     */
    public function store(Request $request)
    {
        // 1. Validasi: Pastikan data yang dikirim user (ID layanan, tanggal, jam) benar dan masuk akal
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);

        // 🔥🔥2. Cek Duplikasi: Mencegah user memesan layanan yang sama dua kali di satu keranjang🔥🔥
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

        // 3. Eksekusi Simpan: Masukkan data pesanan ke dalam tabel 'carts' di database
        Cart::create([
            'user_id' => Auth::id(),
            'treatment_id' => $request->treatment_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
        ]);

        // 4. Notifikasi: Ambil nama layanan untuk ditampilkan di pesan sukses SweetAlert
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

    /**
     * MENGHAPUS ITEM DARI KERANJANG
     * Blok ini bertugas mengeluarkan satu item layanan dari keranjang jika user berubah pikiran.
     */
    public function destroy($id)
    {
        // Cari data keranjang berdasarkan ID-nya
        $cart = Cart::findOrFail($id);

        // Keamanan: Pastikan user hanya bisa menghapus keranjang miliknya sendiri
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
     * Blok ini biasanya dipanggil via JavaScript saat user mengganti tanggal/jam 
     * di halaman keranjang agar semua item ikut berubah jadwalnya.
     */
    public function updateSchedule(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date',
            'booking_time' => 'required',
        ]);

        $user = auth()->user();

        // Update semua isi keranjang user ini ke tanggal dan jam yang baru dipilih
        Cart::where('user_id', $user->id)
            ->update([
                'booking_date' => $request->booking_date,
                'booking_time' => $request->booking_time,
            ]);

        // Berikan respon balik ke JavaScript (JSON) agar halaman tidak perlu refresh
        return response()->json(['success' => true]);
    }

    /**
     * MENGHITUNG TOTAL DURASI LAYANAN (AJAX)
     * Blok ini menghitung total menit dari semua layanan yang ada di keranjang
     * untuk memperkirakan berapa lama treatment akan berlangsung.
     */
    public function getTotalDuration()
    {
        $user = auth()->user();
        
        // Ambil semua isi keranjang dan jumlahkan durasi (menit) dari tiap treatment
        $total = Cart::with('treatment')
            ->where('user_id', $user->id)
            ->get()
            ->sum(function ($item) {
                return $item->treatment->duration;
            });

        // Kirim hasil hitungan ke JavaScript
        return response()->json(['duration' => $total]);
    }
}