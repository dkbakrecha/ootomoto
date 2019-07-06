<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'unique_id'
    ];
    
    public function services() {
        return $this->hasMany('App\Service', 'category_id');
    }
    
    public function barbers() {
        return $this->hasMany('App\BarberService', 'category_id');
    }
    
}