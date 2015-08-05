<?php

namespace App\Model;

class Custodian extends \Illuminate\Database\Eloquent\Model {

    public function assets() {
        return $this->belongsToMany('App\Model\Asset');
    }

}
