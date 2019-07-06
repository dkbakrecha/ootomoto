<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopFavorite extends Model {

    protected $fillable = [
        'id', 'provider_id', 'user_id'
    ];

}
