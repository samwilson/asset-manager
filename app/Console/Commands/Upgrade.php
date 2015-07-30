<?php

namespace App\Console\Commands;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Model\User;
use App\Model\Role;

class Upgrade extends \Illuminate\Console\Command {

    protected $name = "upgrade";
    protected $description = "Upgrade the application. Idempotent.";

    /**
     * Execute the 'upgrade' console command.
     *
     * @return void
     */
    public function fire() {
        $this->call('down');
        $this->info("Upgrading application.");
        $this->initial_install();
        $this->call('up');
    }

    public function initial_install() {
        if (!Schema::hasTable('users')) {
            $this->info("Creating 'users' table.");
            Schema::create('users', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('username')->unique();
                $table->string('email')->nullable();
                $table->string('password', 60);
                $table->rememberToken();
                $table->timestamps();
            });
        }
        if (\App\Model\User::count() == 0) {
            $this->info("No users found; creating 'admin'.");
            $adminUser = new \App\Model\User();
            $adminUser->username = 'admin';
            $adminUser->name = 'Administrator';
            $adminUser->password = bcrypt('admin');
            $adminUser->save();
        }
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        $adminRole = Role::find(Role::ADMIN);
        if (!$adminRole) {
            $this->info("Creating admin role.");
            Role::firstOrCreate(['id' => Role::ADMIN, 'name' => trans('roles.admin')]);
        }
        $managerRole = Role::find(Role::MANAGER);
        if (!$managerRole) {
            $this->info("Creating manager role.");
            Role::firstOrCreate(['id' => Role::MANAGER, 'name' => trans('roles.manager')]);
        }
        $clerkRole = Role::find(Role::CLERK);
        if (!$clerkRole) {
            $this->info("Creating clerk role.");
            Role::firstOrCreate(['id' => Role::CLERK, 'name' => trans('roles.clerk')]);
        }
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function(Blueprint $table) {
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->integer('role_id')->unsigned();
                $table->foreign('role_id')->references('id')->on('roles');
                $table->primary(['user_id', 'role_id']);
            });
        }
        // If there are no administrators, make the first user an admin.
        if (!User::administrators()) {
            $adminUser = User::first();
            $this->info("Making ".$adminUser->name." an Administrator.");
            $adminUser->roles()->save($adminRole);
        }
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function(Blueprint $table) {
                $table->increments('id');
                $table->string('identifier')->unique();
                $table->timestamps();
            });
        }
    }

}
