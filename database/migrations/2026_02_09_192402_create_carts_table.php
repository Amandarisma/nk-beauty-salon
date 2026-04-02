<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User (Milik siapa keranjang ini?)
            // cascade: kalau user dihapus, keranjang ikut hilang
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Relasi ke Treatment (Layanan apa yang dipilih?)
            $table->foreignId('treatment_id')->constrained('treatments');
            
            // Jadwal yang dipilih user
            $table->date('booking_date'); 
            $table->time('booking_time');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
