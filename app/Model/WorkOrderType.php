<?php

namespace App\Model;

class WorkOrderType extends \Illuminate\Database\Eloquent\Model {

    public function workOrders() {
        return $this->hasMany('App\Model\WorkOrder');
    }

}
