<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

//use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable {

    use Notifiable;

    /**
     * Status 
     * 0 = blocked / inactive
     * 1 = active
     * 3 = pending
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'isAdmin', 'phone', 'area_id',
        'gender', 'preferred_language', 'token', 'unique_id', 'user_type', 'status',
        'address', 'map', 'lat', 'long',
        'incharge_name', 'owner_name', 'owner_phone',
        'crn', 'lincense', 'comment', 'man', 'women', 'kid', 'accept_payment', 'profession',
        'commission', 'auto_approve', 'last_login_date', 'confirmation_alert'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function generateToken($devicetoken = "") {
        $this->api_token = str_random(60);

        if (!empty($devicetoken)) {
            $this->device_token = $devicetoken;
        }
        $this->save();

        return $this->api_token;
    }

    public function workingHours() {
        return $this->hasMany('App\ShopWorkingHour', 'shop_id')
                        ->select('shop_id', 'is_open', 'shop_weekday', 'shop_starttime', 'shop_closetime');
    }

    public function shopImages() {
        return $this->hasMany('App\ShopImage', 'shop_id');
    }

    public function shopServices() {
        return $this->hasMany('App\ShopService', 'shop_id');
    }

    public function area() {
        return $this->belongsTo('App\Area', 'area_id')
                        ->select(["id", "unique_id", "name"]);
    }
    
    public function shop() {
        return $this->belongsTo('App\User', 'shop_id')
                        ->select(["id", "unique_id", "name"]);
    }

    /*
      public function sendPasswordResetNotification($token) {
      // Your your own implementation.
      $this->notify(new ResetPasswordNotification($token));
      }
     */

    public function mobileSessions() {
        return $this->hasMany('App\MobileSessions');
    }
}