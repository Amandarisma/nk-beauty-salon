<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

protected $guarded = [];
//     protected $fillable = [
//     'invoice_code',
//     'user_id',
//     'booking_date',
//     'start_time',
//     'end_time',
//     'total_price',
//     'dp_amount',
//     'payment_status',
//     'booking_status',
// ];

    public function items()
{
    return $this->hasMany(\App\Models\BookingItem::class);
}

public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}