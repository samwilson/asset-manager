<?php

namespace App\Model;

class QueuedEmail extends Model
{

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    public function recipient()
    {
        return $this->belongsTo('App\Model\User');
    }
//
//    public function setDataAttribute($data) {
//        $this->attributes['data'] = serialize($data);
//    }
//
//    public function getDataAttribute($data) {
//        return $this->attributes['data'] = serialize($data);
//    }
}
