<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'treatment_id',
        'booking_date',
        'booking_time'
    ];

    // [BLOK KODE: RELASI DATABASE]
    // Fungsi: Menandakan 1 barang di keranjang ini milik 1 User spesifik.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Fungsi: Menandakan 1 barang di keranjang ini merujuk ke 1 Treatment salon.
    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}