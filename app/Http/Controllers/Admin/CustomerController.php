<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar pelanggan untuk analisis CRM.
     */
    public function index()
    {
        // Ambil data user yang role-nya 'user' (bukan admin)
        // Sekaligus hitung jumlah booking mereka menggunakan withCount
        // Urutkan dari yang paling sering booking (Loyal)
        $customers = User::where('role', 'user')
                    ->withCount('bookings') 
                    ->orderByDesc('bookings_count') 
                    ->get();

        return view('admin.customers.index', compact('customers'));
    }
}