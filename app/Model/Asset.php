<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model {

    protected $fillable = ['id', 'identifier'];

    public function setIdentifierAttribute($value) {
        $this->attributes['identifier'] = substr($value, 0, 150);
    }

    public function custodians() {
        return $this->belongsToMany('App\Model\Custodian');
    }

}
