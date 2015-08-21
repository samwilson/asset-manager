<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JobList extends Model {

    public function type() {
        return $this->belongsTo('App\Model\JobType');
    }

    public function jobs() {
        return $this->hasMany('App\Model\Job');
    }

}
