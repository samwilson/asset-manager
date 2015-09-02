<?php

namespace App\Model;

class Crew extends \Illuminate\Database\Eloquent\Model {

    public function jobLists() {
        return $this->belongsToMany('App\Model\JobList');
    }

    public function members() {
        return $this->hasMany('App\Model\CrewMember');
    }

}
