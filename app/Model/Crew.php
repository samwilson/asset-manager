<?php

namespace App\Model;

class Crew extends \Illuminate\Database\Eloquent\Model {

    public function scheduledWorkOrders() {
        return $this->hasMany('App\Model\ScheduledWorkOrder');
    }

}
