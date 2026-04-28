<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * MENAMPILKAN HALAMAN FORM DAFTAR
     * Blok ini bertugas untuk memanggil file view (tampilan)
     * supaya user bisa melihat form pendaftaran.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * MEMPROSES DATA PENDAFTARAN (INPUT USER)
     * Blok ini adalah mesin utama yang bekerja saat user klik tombol "Register".
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi: Ngecek apakah input user sudah sesuai aturan.
        // - Name: Harus diisi, berupa tulisan, maksimal 255 huruf.
        // - Email: Harus format email, unik (belum pernah dipakai), maksimal 255 huruf.
        // - Password: Harus diisi, ada konfirmasinya (ketik 2x), dan memenuhi standar keamanan.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Simpan Data: Membuat baris baru di tabel 'users'.
        // 🔥🔥Password di-"Hash" (diacak) supaya aman, jadi admin pun nggak bisa baca password aslinya.🔥🔥
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Trigger Event: Memberitahu sistem bahwa ada user baru yang terdaftar.
        // Biasanya ini dipakai untuk otomatis kirim email verifikasi atau welcome email.
        event(new Registered($user));

        // 4. Auto Login: Setelah sukses daftar, user nggak perlu login manual lagi.
        // Sistem langsung menganggap user tersebut sudah dalam kondisi "Logged In".
        Auth::login($user);

        // 5. Redirect: Melempar user ke halaman utama (biasanya Dashboard) setelah beres semua.
        return redirect(RouteServiceProvider::HOME);
    }
}