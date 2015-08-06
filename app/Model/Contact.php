<?php

namespace App\Model;

class Contact extends \Illuminate\Database\Eloquent\Model {

    public function assets() {
        return $this->belongsToMany('App\Model\Asset');
    }

}
