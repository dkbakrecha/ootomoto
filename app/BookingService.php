<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingService extends Model {

    public function service() {
        return $this->belongsTo('App\ShopService', 'service_id', 'service_id');
    }
    
    public function barber() {
        return $this->belongsTo('App\User', 'barber_id')
                        ->select('id', 'unique_id', 'name', 'profile_image', 'phone', 'email');
    }

}
