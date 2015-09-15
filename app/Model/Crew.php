<?php

namespace App\Model;

class Crew extends \Illuminate\Database\Eloquent\Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public function jobLists() {
        return $this->hasMany('App\Model\JobList');
    }

    public function members() {
        return $this->hasMany('App\Model\CrewMember');
    }

    public function crewDates() {
        return $this->hasMany('App\Model\CrewDate');
    }

    public function setNameAttribute($newName) {
        if (empty($newName)) {
            throw new \Exception('A Crew name must not be empty.');
        }
        $this->attributes['name'] = trim($newName);
    }

    public function availableOn($date) {
        $available = true;
        foreach ($this->crewDates as $d) {
             $available = $available && $d->availableOn($date);
        }
        return $available;
    }

}
