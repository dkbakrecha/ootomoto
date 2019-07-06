<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopOffer extends Model {
    
    /*
     * Status Use in Offers
     * 0 = Rejected
     * 1 = Active
     * 2 = Inactive
     * 3 = Pending
     * 4 = Expire
     */

    public function shop() {
        return $this->belongsTo('App\User', 'shop_id')
                        ->select(["id", "unique_id", "name"]);
    }

}
