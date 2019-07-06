<?php

namespace App;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model {

    protected $table = 'feedbacks';

    protected static function boot() {
        parent::boot();
    }

    public function sender() {
        return $this->belongsTo('App\User', 'from_id')
                        ->select(["id", "unique_id", "name", "profile_image", "isAdmin", "status", "user_type"]);
    }

    public function receiver() {
        return $this->belongsTo('App\User', 'to_id')
                        ->select(["id", "unique_id", "name", "profile_image", "isAdmin", "status", "user_type"]);
    }

    /** Function to return first conversion ID between two users
     * Use as parent
     */
    public function findParent($user_1, $user_2) {
        $records = $this->where([
                    ['from_id', '=', $user_1],
                    ['to_id', '=', $user_2]
                ])
                ->orWhere([
                    ['from_id', '=', $user_2],
                    ['to_id', '=', $user_1]
                ])
                ->first();
        return (!empty($records)) ? $records->id : 0;
    }

}
