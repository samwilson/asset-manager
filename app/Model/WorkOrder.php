<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model {

    public function type() {
        return $this->belongsTo('App\Model\WorkOrderType', 'work_order_type_id');
    }

    public function assets() {
        return $this->belongsToMany('App\Model\Asset');
    }

    public function schedules() {
        return $this->hasMany('App\Model\ScheduledWorkOrder');
    }

}
