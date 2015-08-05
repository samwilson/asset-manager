<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobPack extends Model {

    public function type() {
        return $this->belongsTo('App\Model\JobType');
    }

    public function assets() {
        return $this->belongsToMany('App\Model\Asset');
    }

}
