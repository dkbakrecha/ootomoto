<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model {

    public function bookings() {
        return $this->hasMany('App\Booking', 'area_id')->whereIn('status',[1,2]);
    }

}
