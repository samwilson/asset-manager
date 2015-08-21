<?php

namespace App\Model;

class JobType extends \Illuminate\Database\Eloquent\Model {

    public function jobLists() {
        return $this->hasMany('App\Model\JobList');
    }

}
