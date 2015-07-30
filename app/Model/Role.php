<?php

namespace App\Model;

class Role extends \Illuminate\Database\Eloquent\Model {

    const ADMIN = 1;
    const MANAGER = 2;
    const CLERK = 3;

    public $fillable = ['id', 'name'];

    public function users() {
        return $this->belongsToMany('App\Model\User');
    }

}
