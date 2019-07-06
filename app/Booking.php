<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public function customer() {
        return $this->belongsTo('App\User', 'customer_id')
                        ->select(["id", "unique_id", "name", "user_type", "phone"]);
    }
    
    public function shop() {
        return $this->belongsTo('App\User', 'shop_id')
                        ->select(["id", "unique_id", "name", "user_type", "address", "commission"]);
    }
    
    public function bookingservices() {
        return $this->hasMany('App\BookingService', 'booking_id');
    }
}
