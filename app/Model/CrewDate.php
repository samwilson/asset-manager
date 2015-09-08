<?php

namespace App\Model;

class CrewDate extends Model {

    public function crew() {
        return $this->belongsTo('App\Model\Crew');
    }

    public function availableOn($date) {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        $start = new \DateTime($this->start_date);
        $end = new \DateTime($this->end_date);
        $afterStart = $start <= $date || is_null($this->start_date);
        $beforeEnd = $end >= $date || is_null($this->end_date);
        return $afterStart && $beforeEnd;
    }

    public function setStartDateAttribute($value) {
        if ($value) {
            $this->attributes['start_date'] = $value;
        }
    }

    public function setEndDateAttribute($value) {
        if ($value) {
            $this->attributes['end_date'] = $value;
        }
    }

}
