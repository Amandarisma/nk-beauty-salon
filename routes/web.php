<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// // Import Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\AdminOperationController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\TransactionController;

// // Public Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// 🔥 API ENDPOINTS
Route::get('/api/booked-slots', [CheckoutController::class, 'getBookedSlots']);
Route::get('/cart/total-duration/data', [CartController::class, 'getTotalDuration'])->middleware('auth');
Route::post('/cart/update-schedule', [CartController::class, 'updateSchedule'])->middleware('auth');

// 🔥 DASHBOARD (BERANDA)
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

// 🔥 RIWAYAT RESERVASI PELANGGAN
Route::get('/my-bookings', function () {
    $bookings = App\Models\Booking::with('items.treatment')
        ->where('user_id', Auth::id())
        ->latest()
        ->get();

    return view('user.bookings', compact('bookings'));
})->middleware(['auth'])->name('user.bookings');

// 🔥 ADMIN AREA (Manajemen Sistem)
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminOperationController::class, 'dashboard'])->name('dashboard');

        Route::patch('/bookings/{id}/status', [AdminOperationController::class, 'updateStatus'])->name('bookings.status');

        Route::resource('treatments', TreatmentController::class);

        Route::get('/pos', [AdminOperationController::class, 'createWalkIn'])->name('pos.create');
        Route::post('/pos', [AdminOperationController::class, 'storeWalkIn'])->name('pos.store');

        Route::get('/invoice/{id}', [AdminOperationController::class, 'invoice'])->name('invoice');

        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');

        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
            
        // 🔥 RUTE TRANSAKSI 
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::patch('/transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
            
        // 🔥 RUTE EXPORT PDF DINAMIS 🔥
        Route::get('/export-pdf', [AdminOperationController::class, 'exportPdf'])->name('export.pdf');
        
        // Alias rute lama biar tombol-tombol export di halaman lain tetap aman!
        Route::get('/transactions/export-pdf', [AdminOperationController::class, 'exportPdf'])->name('transactions.pdf');
    });

// 🔥 PROFILE MANAGEMENT
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔥 USER AREA (Pemesanan & Keranjang)
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/booking/payment/{id}', [CheckoutController::class, 'showPayment'])->name('booking.payment');
    Route::post('/booking/pay/{id}', [CheckoutController::class, 'confirmPayment'])->name('booking.pay');
    Route::patch('/booking/cancel/{id}', [CheckoutController::class, 'cancelBooking'])->name('booking.cancel');
});

// 🔥 UTILITY ROUTE (Server Hack)
Route::get('/buat-jembatan', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Alhamdulillah, jembatan foto sukses dibuat!';
});

require __DIR__.'/auth.php';