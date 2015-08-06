<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model {

    protected $fillable = ['id', 'identifier'];

    public function setIdentifierAttribute($value) {
        $this->attributes['identifier'] = substr($value, 0, 150);
    }

    public function contacts() {
        return $this->belongsToMany('App\Model\Contact');
    }

    public function categories() {
        return $this->belongsToMany('App\Model\Category');
    }

    public function workOrders() {
        return $this->belongsToMany('App\Model\WorkOrder');
    }

}
