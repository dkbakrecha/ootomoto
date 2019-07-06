<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopService extends Model {

    protected $fillable = [
        'id', 'shop_id', 'unique_id', 'category_id', 'name', 'duration', 'price'
    ];

    public function category() {
        return $this->belongsTo('App\Category', 'category_id')
                        ->select(["id", "unique_id", "name"]);
    }
    
}
