<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BarberService extends Model {

    public function barber() {
        return $this->belongsTo('App\user', 'barber_id')
                        ->select('id', 'unique_id', 'name', 'profile_image', 'phone', 'email');
    }

}
