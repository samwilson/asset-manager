<?php

namespace App\Model;

class ContactAttempt extends Model
{

    public function job()
    {
        return $this->belongsTo('App\Model\Job');
    }

    public function contact()
    {
        return $this->belongsTo('App\Model\Contact');
    }

    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }
}
