<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopReview extends Model {

    public function customer() {
        return $this->belongsTo('App\User', 'customer_id')
                        ->select(["id", "unique_id", "name", "profile_image"]);
    }

    public function shop() {
        return $this->belongsTo('App\User', 'shop_id')
                        ->select(["id", "unique_id", "name", "profile_image"]);
    }

}
