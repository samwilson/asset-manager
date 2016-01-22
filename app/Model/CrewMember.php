<?php

namespace App\Model;

class CrewMember extends Model
{

    public function crew()
    {
        return $this->belongsTo('App\Model\Crew');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }
}
