<?php

namespace App\Model;

class CrewUnavailability extends Unavailability {

    public function crew() {
        return $this->belongsTo('App\Model\Crew');
    }

}
