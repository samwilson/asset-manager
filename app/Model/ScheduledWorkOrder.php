<?php

namespace App\Model;

use DB;

class ScheduledWorkOrder extends \Illuminate\Database\Eloquent\Model {

    public function crew() {
        return $this->belongsTo('App\Model\Crew');
    }

    public function workOrder() {
        return $this->belongsTo('App\Model\WorkOrder');
    }

    public function scopeCurrent($query) {
        return $query->where('start_date', '<=', DB::raw('CURRENT_DATE'))
            ->where(function($query) {
                $query->where('end_date', '>=', DB::raw('CURRENT_DATE'))
                ->orWhere('end_date', null);
            });
    }

}
