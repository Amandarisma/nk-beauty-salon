<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID Unik (1, 2, 3...)
            $table->string('name'); // Nama Lengkap
            $table->string('email')->unique(); // Email (harus unik)
            
            // --- TAMBAHAN KHUSUS NK SALON ---
            $table->string('phone')->nullable(); // No HP (Boleh kosong/nullable)
            $table->string('role')->default('user'); // Peran: 'admin' atau 'user'
            $table->string('customer_id_code')->nullable()->unique(); // Kode CRM (CUST-001)
            // --------------------------------
            
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Password terenkripsi
            $table->rememberToken();
            $table->timestamps(); // Mencatat waktu dibuat (created_at)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};