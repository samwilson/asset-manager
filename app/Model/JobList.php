<?php

namespace App\Model;

class JobList extends Taggable {

    protected $nullable = ['crew_id', 'start_date', 'due_date', 'comments'];

    public function type() {
        return $this->belongsTo('App\Model\JobType');
    }

    public function crew() {
        return $this->belongsTo('App\Model\Crew');
    }

    public function jobs() {
        return $this->hasMany('App\Model\Job');
    }

    /**
     * @return float
     */
    public function percentComplete() {
        return round((10 / $this->jobs->count()) * 100);
    }

    /**
     * @return float
     */
    public function percentIncomplete() {
        return 100 - $this->percentComplete();
    }

    public function status() {
        if (!is_null($this->crew_id)) {
            return 'Scheduled';
        }
        return 'Unscheduled';
    }

}
