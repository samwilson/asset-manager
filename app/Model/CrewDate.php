<?php

namespace App\Model;

class CrewDate extends DateRange {

    public function crew() {
        return $this->belongsTo('App\Model\Crew');
    }

}
