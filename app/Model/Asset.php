<?php

namespace App\Model;

class Asset extends Taggable
{

    protected $fillable = ['id', 'identifier'];

    public function setIdentifierAttribute($value)
    {
        $this->attributes['identifier'] = substr($value, 0, 150);
    }

    public function suburb()
    {
        return $this->belongsTo('App\Model\Suburb');
    }

    public function state()
    {
        return $this->belongsTo('App\Model\State');
    }

    public function contacts()
    {
        return $this->belongsToMany('App\Model\Contact');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Model\Category');
    }

    public function jobs()
    {
        return $this->hasMany('App\Model\Job');
    }
}
