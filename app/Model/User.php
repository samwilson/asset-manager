<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends \Illuminate\Database\Eloquent\Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable,
        CanResetPassword;

    protected $fillable = ['name', 'email', 'username', 'password'];

    public function roles() {
        return $this->belongsToMany('App\Model\Role');
    }

    public function crewMemberships() {
        return $this->hasMany('App\Model\CrewMember');
    }

    public function hasRole($roleId) {
        return $this->roles->where('id', $roleId)->count() > 0;
    }

    public function isClerk() {
        return $this->isAdmin() || $this->hasRole(Role::CLERK);
    }

    public function isManager() {
        return $this->isAdmin() || $this->hasRole(Role::MANAGER);
    }

    public function isAdmin() {
        return $this->hasRole(Role::ADMIN);
    }

    /**
     * Get all Administrators.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function administrators() {
        return self::whereHas('roles', function($query) {
                    $query->where('id', Role::ADMIN);
                })->get();
    }

}
