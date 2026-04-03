<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Treatment; // <--- Wajib panggil Model ini

class HomeController extends Controller
{

        public function index()
    {
        // 🔥 JIKA ADMIN MENGAKSES HALAMAN DEPAN, LANGSUNG LEMPAR KE DASHBOARDNYA
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $treatments = \App\Models\Treatment::all();
        return view('welcome', compact('treatments'));
            }
}

            // public function index()
    // {
    //     // Ambil semua data layanan dari database
    //     $treatments = Treatment::all();
        
    //     // Kirim data '$treatments' ke tampilan 'welcome'
    //     // Kata 'compact' inilah yang mengirim paket datanya
    //     return view('welcome', compact('treatments'));

