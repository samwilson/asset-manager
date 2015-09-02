<?php

namespace App\Model;

class JobType extends Model {

    public function jobLists() {
        return $this->hasMany('App\Model\JobList');
    }

    public function getContactRequiredAttribute($value) {
        return $value === true;
    }

}
