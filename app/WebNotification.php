<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebNotification extends Model
{
    //
    /**
     * The attributes that are guarded. All others willbe fillable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo('App\User')
                        ->select(["id", "unique_id", "name", "user_type", "phone"]);
    }
}
