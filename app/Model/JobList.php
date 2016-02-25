<?php

namespace App\Model;

class JobList extends Taggable
{

    protected $fillable = ['name'];
    protected $nullable = ['crew_id', 'start_date', 'due_date', 'comments'];

    public function type()
    {
        return $this->belongsTo('App\Model\JobType');
    }

    public function crew()
    {
        return $this->belongsTo('App\Model\Crew');
    }

    public function jobs()
    {
        return $this->hasMany('App\Model\Job');
    }

    public function completeCount()
    {
        $complete = 0;
        $this->load('jobs');
        foreach ($this->jobs as $job) {
            if ($job->status() === trans('jobs.status-complete')) {
                $complete++;
            }
        }
        return $complete;
    }

    /**
     * @return float
     */
    public function percentComplete()
    {
        return round(($this->completeCount() / $this->jobs->count()) * 100);
    }

    /**
     * @return float
     */
    public function percentIncomplete()
    {
        return 100 - $this->percentComplete();
    }

    /**
     * @return boolean
     */
    public function isInProgress()
    {
        return ($this->percentComplete() > 0 && $this->percentComplete() < 100);
    }

    public function status()
    {
        if ($this->isInProgress()) {
            return trans('job-lists.status-in-progress');
        }
        if (!is_null($this->crew_id)) {
            return trans('job-lists.status-scheduled');
        }
        return trans('job-lists.status-unscheduled');
    }
}
