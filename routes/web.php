<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\AdminOperationController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\InventoryController;

Route::get('/', [HomeController::class, 'index'])->name('home');

//
// 🔥 API
//
Route::get('/api/booked-slots', [CheckoutController::class, 'getBookedSlots']);
Route::get('/cart/total-duration/data', [CartController::class, 'getTotalDuration'])->middleware('auth');
Route::post('/cart/update-schedule', [CartController::class, 'updateSchedule'])->middleware('auth');

//
// 🔥 DASHBOARD
//
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $bookings = App\Models\Booking::with('items.treatment')
        ->where('user_id', Auth::id())
        ->latest()
        ->get();

    return view('dashboard', compact('bookings'));

})->middleware(['auth', 'verified'])->name('dashboard');

//
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.') // 🔥 INI PENTING
    ->group(function () {

        Route::get('/dashboard', [AdminOperationController::class, 'dashboard'])
            ->name('dashboard');

        Route::resource('treatments', TreatmentController::class);

        Route::get('/pos', [AdminOperationController::class, 'createWalkIn'])
            ->name('pos.create'); // ✅ FIX

        Route::post('/pos', [AdminOperationController::class, 'storeWalkIn'])
            ->name('pos.store'); // ✅ FIX

        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('customers.index');

        Route::get('/inventory', [InventoryController::class, 'index'])
            ->name('inventory.index');

        Route::put('/inventory/{id}', [InventoryController::class, 'update'])
            ->name('inventory.update');
    });

//
// 🔥 USER
//
Route::middleware(['auth', 'user'])->group(function () {

    // PROFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CART
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // CHECKOUT
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/booking/payment/{id}', [CheckoutController::class, 'showPayment'])->name('booking.payment');
    Route::post('/booking/pay/{id}', [CheckoutController::class, 'confirmPayment'])->name('booking.pay');

    Route::post('/checkout', [CheckoutController::class, 'process'])
    ->name('checkout.process');
});

require __DIR__.'/auth.php';