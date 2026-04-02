<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code')->unique(); // INV-20231025-001
            
            // User ID boleh kosong (nullable) jika yang datang Tamu Walk-in (bukan member online)
            $table->foreignId('user_id')->nullable()->constrained(); 
            
            // Data Tamu Manual (Diisi Admin untuk Walk-in)
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();

            // Info Uang
            $table->decimal('total_price', 12, 2); // Total Tagihan
            $table->decimal('dp_amount', 12, 2)->default(0); // DP 30%
            
            // Status: pending (belum bayar), paid_dp, paid_full, failed
            $table->string('payment_status')->default('pending'); 
            
            // Status Booking: new (baru), confirmed, completed, cancelled
            $table->string('booking_status')->default('new'); 
            
            // Khusus Tripay
            $table->string('tripay_reference')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};