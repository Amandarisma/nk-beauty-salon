<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama Layanan, misal: "Creambath"
            $table->text('description')->nullable(); // Deskripsi singkat
            $table->integer('duration'); // Durasi dalam menit (contoh: 60)
            $table->decimal('price', 12, 2); // Harga (contoh: 150000.00)
            $table->string('image')->nullable(); // Foto layanan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};