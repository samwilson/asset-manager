<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model {

    public function type() {
        return $this->belongsTo('App\Model\WorkOrderType');
    }

    public function assets() {
        return $this->belongsToMany('App\Model\Asset');
    }

}
