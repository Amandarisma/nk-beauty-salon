<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // Membuat Akun Admin
    \App\Models\User::create([
        'name' => 'Admin Nafisa',
        'email' => 'nafisa123@gmail.com',
        'password' => bcrypt('nafisa123'),
        'role' => 'admin'
    ]);

    // Membuat Akun User Biasa
    \App\Models\User::create([
        'name' => 'User Lintang',
        'email' => 'lintang321@gmail.com',
        'password' => bcrypt('lintang321'),
        'role' => 'user'
    ]);
    $this->call([
        TreatmentSeeder::class,
    ]);
}

}
