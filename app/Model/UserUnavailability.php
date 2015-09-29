<?php

namespace App\Model;

class UserUnavailability extends Unavailability {

    public function user() {
        return $this->belongsTo('App\Model\User');
    }

}
