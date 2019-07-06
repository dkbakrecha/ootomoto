<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    public function customer() {
        return $this->belongsTo('App\User', 'customer_id')
                        ->select(["id", "unique_id", "name", "user_type", "phone"]);
    }

    public function shop() {
        return $this->belongsTo('App\User', 'shop_id')
                        ->select(["id", "unique_id", "name", "user_type", "address", "phone"]);
    }
    
    public function booking() {
        return $this->belongsTo('App\Booking', 'booking_id')
                        ->select(["id", "unique_id"]);
    }
}
