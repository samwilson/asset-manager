<?php

namespace App\Model;

use DB;

class Job extends Model
{

    const STATUS_INCOMPLETE = 10;
    const STATUS_COMPLETE = 20;

    public function crew()
    {
        return $this->belongsTo('App\Model\Crew');
    }

    public function jobList()
    {
        return $this->belongsTo('App\Model\JobList');
    }

    public function resolution()
    {
        return $this->belongsTo('App\Model\JobResolution');
    }

    public function asset()
    {
        return $this->belongsTo('App\Model\Asset');
    }

    public function contactAttempts()
    {
        return $this->hasMany('App\Model\ContactAttempt');
    }

    public function scopeCurrent($query)
    {
        return $query
                ->where('start_date', '<=', DB::raw('CURRENT_DATE'))
                ->where(function ($query) {
                    $query->where('end_date', '>=', DB::raw('CURRENT_DATE'))
                    ->orWhere('end_date', null);
                });
    }

    public function contactRequired()
    {
        return $this->jobList->type->contact_required;
    }

    public function contactMade()
    {
        foreach ($this->contactAttempts as $attempt) {
            if ($attempt->contact_made) {
                return true;
            }
        }
        return false;
    }

    public function status()
    {
        if ($this->resolution_id) {
            return self::STATUS_COMPLETE;
        }
        if ($this->contactRequired() && !$this->contactMade()) {
            return 'Contact attempt required';
        }
        return self::STATUS_INCOMPLETE;
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        // If only a date of resolution is set, assume it's successful.
        if ($this->date_resolved && !$this->resolution_id) {
            $this->resolution_id = JobResolution::SUCCEEDED;
        }
        parent::save($options);
    }
}
