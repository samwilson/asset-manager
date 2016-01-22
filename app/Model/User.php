<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\DB;

class User extends \Illuminate\Database\Eloquent\Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable,
        CanResetPassword;

    protected $fillable = ['name', 'email', 'username', 'password'];

    public function roles()
    {
        return $this->belongsToMany('App\Model\Role');
    }

    public function crewMemberships()
    {
        return $this->hasMany('App\Model\CrewMember');
    }

    public function userUnavailabilities()
    {
        return $this->hasMany('App\Model\UserUnavailability');
    }

    public function hasRole($roleId)
    {
        return $this->roles->where('id', $roleId)->count() > 0;
    }

    public function isClerk()
    {
        return $this->isAdmin() || $this->hasRole(Role::CLERK);
    }

    public function isManager()
    {
        return $this->isAdmin() || $this->hasRole(Role::MANAGER);
    }

    public function isAdmin()
    {
        return $this->hasRole(Role::ADMIN);
    }

    /**
     * Get all Administrators.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function administrators()
    {
        return self::whereHas('roles', function ($query) {
            $query->where('id', Role::ADMIN);
        })->get();
    }

    public function availableOn($date)
    {
        $available = true;
        foreach ($this->userUnavailabilities as $unavail) {
            $available = $available && $unavail->availableOn($date);
        }
        return $available;
    }

    public function sendPasswordReminder()
    {
        DB::beginTransaction();
        // Save the reset token.
        $passwordReminder = str_random(40);
        $this->password_reminder = bcrypt($passwordReminder);
        $this->save();

        // Queue.
        $queuedEmail = new QueuedEmail();
        $queuedEmail->recipient_id = $this->id;
        $queuedEmail->subject = 'Password reminder for ' . config('app.site_name');
        $queuedEmail->template = 'users/reminder_email';
        $queuedEmail->data = [
            'password_reminder' => $passwordReminder,
        ];
        $queuedEmail->save();
        DB::commit();
    }
}
