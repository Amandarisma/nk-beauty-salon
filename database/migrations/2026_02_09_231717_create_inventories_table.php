<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_name'); // Nama Barang (ex: Shampo)
            $table->integer('stock')->default(0); // Jumlah Stok
            $table->string('unit'); // Satuan (ex: Botol)
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
        });

        // Insert Data Dummy Awal
        DB::table('inventories')->insert([
            ['item_name' => 'Shampo', 'stock' => 20, 'unit' => 'Botol'],
            ['item_name' => 'Krim Creambath', 'stock' => 15, 'unit' => 'Jar'],
            ['item_name' => 'Vitamin Rambut', 'stock' => 50, 'unit' => 'Kapsul'],
            ['item_name' => 'Cat Rambut (Hitam)', 'stock' => 10, 'unit' => 'Kotak'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};