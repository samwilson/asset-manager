<?php

namespace App\Model;

use DB;

class Job extends \Illuminate\Database\Eloquent\Model {

    public function crew() {
        return $this->belongsTo('App\Model\Crew');
    }

    public function jobList() {
        return $this->belongsTo('App\Model\JobList');
    }

    public function asset() {
        return $this->belongsTo('App\Model\Asset');
    }

    public function contactAttempts() {
        return $this->hasMany('App\Model\ContactAttempt');
    }

    public function scopeCurrent($query) {
        return $query
                        ->where('start_date', '<=', DB::raw('CURRENT_DATE'))
                        ->where(function($query) {
                            $query->where('end_date', '>=', DB::raw('CURRENT_DATE'))
                            ->orWhere('end_date', null);
                        });
    }

    public function contactRequired() {
        return $this->jobList->type->contact_required;
    }

    public function contactMade() {
        foreach ($this->contactAttempts as $attempt) {
            if ($attempt->contact_made) {
                return true;
            }
        }
        return false;
    }

    public function status() {
        if ($this->date_removed) {
            return 'Cancelled';
        }
        if ($this->date_resolved) {
            return 'Complete';
        }
        if ($this->contactRequired() && !$this->contactMade()) {
            return 'Contact attempt required';
        }
        return 'Incomplete';
    }

}
