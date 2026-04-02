<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            
            // Nempel ke Booking Induk
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Layanan apa?
            $table->foreignId('treatment_id')->constrained();
            
            // Jadwal disimpan lagi disini (detail)
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            
            // Harga saat booking terjadi (Penting untuk laporan keuangan yang akurat)
            $table->decimal('price_at_booking', 12, 2); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};


