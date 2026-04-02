<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $fillable = [
    //     'booking_id',
    //     'treatment_id',
    //     'scheduled_date',
    //     'scheduled_time',
    //     'price_at_booking'
    // ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}