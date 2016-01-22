<?php

namespace App\Model;

class Task extends \Illuminate\Database\Eloquent\Model
{

    public function workOrder()
    {
        return $this->belongsTo('Model\\Model\\WorkOrder');
    }
}
