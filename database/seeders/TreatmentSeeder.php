<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    \App\Models\Treatment::create([
        'name' => 'Facial Treatment',
        'description' => 'Membersihkan dan menyegarkan kulit wajah.',
        'price' => 150000,
        'duration' => 60, // Tambahkan baris ini (contoh: 60 menit)
    ]);

    \App\Models\Treatment::create([
        'name' => 'Hair Spa',
        'description' => 'Perawatan rambut agar lembut dan sehat.',
        'price' => 120000,
        'duration' => 45, // Tambahkan baris ini
    ]);
}
}
