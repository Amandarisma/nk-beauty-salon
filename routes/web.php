<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// // Import Controllers: Memanggil semua controller yang dibutuhkan agar tidak perlu menulis path panjang di setiap rute
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\AdminOperationController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\TransactionController;

// // Public Route: Rute beranda yang bisa diakses oleh siapa saja (tanpa login)
Route::get('/', [HomeController::class, 'index'])->name('home');

//
// 🔥 API ENDPOINTS (Digunakan oleh JavaScript/AJAX)
//
// // API Fetch: Mengembalikan data jadwal dalam bentuk JSON untuk logika JavaScript di frontend (tanpa refresh halaman)
Route::get('/api/booked-slots', [CheckoutController::class, 'getBookedSlots']);
// // Middleware 'auth': Hanya user yang sudah login yang bisa mengecek total durasi dan mengubah jadwal keranjang
Route::get('/cart/total-duration/data', [CartController::class, 'getTotalDuration'])->middleware('auth');
Route::post('/cart/update-schedule', [CartController::class, 'updateSchedule'])->middleware('auth');

//
// 🔥 DASHBOARD (BERANDA)
//
// // Routing Redirect: Jika user mengakses /dashboard (biasanya bawaan auth Laravel), langsung diarahkan kembali ke beranda salon
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

//
// 🔥 RIWAYAT RESERVASI PELANGGAN
//
Route::get('/my-bookings', function () {
    // // Query Optimization: Menggunakan 'with('items.treatment')' (Eager Loading) untuk mencegah masalah N+1 Query agar web tidak lemot saat meload gambar/nama layanan
    $bookings = App\Models\Booking::with('items.treatment')
        ->where('user_id', Auth::id()) // Hanya ambil pesanan milik user yang sedang login
        ->latest() // Urutkan dari yang terbaru
        ->get();

    return view('user.bookings', compact('bookings'));

})->middleware(['auth'])->name('user.bookings');

//
// 🔥 ADMIN AREA (Manajemen Sistem)
//
// // Route Grouping & Middleware: Membungkus semua rute admin. Pengunjung WAJIB login ('auth') DAN memiliki role ('admin').
// // Prefix 'admin' membuat URL otomatis diawali /admin/..., Name 'admin.' membuat nama rute otomatis diawali admin.
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminOperationController::class, 'dashboard'])
            ->name('dashboard');

        // // RESTful Logic: Menggunakan method PATCH karena hanya mengubah sebagian data (status pesanan), bukan seluruh data.
        Route::patch('/bookings/{id}/status', [AdminOperationController::class, 'updateStatus'])
            ->name('bookings.status');

        // // Resource Route: Secara otomatis membuat 7 rute CRUD (index, create, store, show, edit, update, destroy) untuk entitas treatments (layanan)
        Route::resource('treatments', TreatmentController::class);

        // // Fitur Point of Sale (POS) untuk pelanggan Walk-in (datang langsung tanpa booking online)
        Route::get('/pos', [AdminOperationController::class, 'createWalkIn'])
            ->name('pos.create');
        Route::post('/pos', [AdminOperationController::class, 'storeWalkIn'])
            ->name('pos.store');

        Route::get('/invoice/{id}', [AdminOperationController::class, 'invoice'])
            ->name('invoice');

        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('customers.index');
            
        // DETAIL PELANGGAN
        // // Route Parameter: {id} menangkap ID pelanggan dari URL untuk diteruskan ke controller
        Route::get('/customers/{id}', [CustomerController::class, 'show'])
            ->name('customers.show');

        Route::get('/inventory', [InventoryController::class, 'index'])
            ->name('inventory.index');
            
        // 🔥 RUTE TRANSAKSI & EXPORT PDF 🔥
        Route::get('/transactions', [TransactionController::class, 'index'])
            ->name('transactions.index');
            
        // // Reporting: Endpoint khusus untuk men-generate dan mengunduh laporan transaksi dalam format PDF
        Route::get('/transactions/export-pdf', [TransactionController::class, 'exportPdf'])
            ->name('transactions.pdf');
    });

//
// 🔥 PROFILE MANAGEMENT
//
// // Rute bawaan Laravel (biasanya dari starter kit seperti Breeze) untuk mengelola data akun profil
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//
// 🔥 USER AREA (Pemesanan & Keranjang)
//
// // Middleware 'user': Memastikan bahwa admin tidak bisa mengakses keranjang belanja atau melakukan checkout layaknya pelanggan biasa
Route::middleware(['auth', 'user'])->group(function () {

    // CART (Keranjang)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // CHECKOUT & PEMBAYARAN
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/booking/payment/{id}', [CheckoutController::class, 'showPayment'])->name('booking.payment');
    Route::post('/booking/pay/{id}', [CheckoutController::class, 'confirmPayment'])->name('booking.pay');
    
    // 🔥 RUTE UNTUK BATALKAN BOOKING
    Route::patch('/booking/cancel/{id}', [CheckoutController::class, 'cancelBooking'])->name('booking.cancel');
});

//
// 🔥 UTILITY ROUTE (Server Hack)
//
// // Deployment Trick: Rute ini memanggil perintah php artisan storage:link secara paksa via web. 
// // Sangat berguna jika di CPanel/Hosting nanti kamu tidak memiliki akses terminal/SSH untuk membuat symbolic link gambar.
Route::get('/buat-jembatan', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Alhamdulillah, jembatan foto sukses dibuat!';
});

// // Auth Routes: Memuat rute-rute autentikasi bawaan (login, register, forgot password, dll) dari file auth.php
require __DIR__.'/auth.php';