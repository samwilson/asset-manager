<?php

namespace App\Model;

class JobList extends Taggable {

    protected $nullable = ['crew_id', 'start_date', 'due_date', 'comments'];

    public function type() {
        return $this->belongsTo('App\Model\JobType');
    }

    public function jobs() {
        return $this->hasMany('App\Model\Job');
    }

}
