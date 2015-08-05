<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobType extends Model {

    public function jobs() {
        return $this->hasMany('App\Model\Job');
    }

}
