<?php

namespace App\Model;

class UserDate extends DateRange {

    public function user() {
        return $this->belongsTo('App\Model\User');
    }

}
