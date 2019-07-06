<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileSessions extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'device_token', 'device_type', 'app_version', 'status'
    ];
}
