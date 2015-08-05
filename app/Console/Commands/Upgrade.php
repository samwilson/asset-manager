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
            $this->info("Making " . $adminUser->name . " an Administrator.");
            $adminUser->roles()->save($adminRole);
        }
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function(Blueprint $table) {
                $table->increments('id');
                $table->string('identifier')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_categories')) {
            $this->info("Creating 'asset_categories' table.");
            Schema::create('asset_categories', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->integer('parent_id')->unsigned()->nullable();
                $table->foreign('parent_id')->references('id')->on('asset_categories');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('custodians')) {
            $this->info("Creating 'custodians' table.");
            Schema::create('custodians', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_custodian')) {
            $this->info("Creating 'asset_custodian' table.");
            Schema::create('asset_custodian', function(Blueprint $table) {
                $table->integer('asset_id')->unsigned()->nullable();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('custodian_id')->unsigned()->nullable();
                $table->foreign('custodian_id')->references('id')->on('custodians');
                $table->primary(['asset_id', 'custodian_id']);
            });
        }
        if (!Schema::hasTable('job_types')) {
            $this->info("Creating 'job_types' table.");
            Schema::create('job_types', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('jobs')) {
            $this->info("Creating 'jobs' table.");
            Schema::create('jobs', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->integer('type_id')->unsigned()->nullable();
                $table->foreign('type_id')->references('id')->on('job_types');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('job_packs')) {
            $this->info("Creating 'job_packs' table.");
            Schema::create('job_packs', function(Blueprint $table) {
                $table->increments('id');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('crews')) {
            $this->info("Creating 'crews' table.");
            Schema::create('crews', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_job_pack')) {
            $this->info("Creating 'asset_job_pack' table.");
            Schema::create('asset_job_pack', function(Blueprint $table) {
                $table->integer('asset_id')->unsigned()->nullable();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('job_pack_id')->unsigned()->nullable();
                $table->foreign('job_pack_id')->references('id')->on('job_packs');
                $table->primary(['asset_id', 'job_pack_id']);
            });
        }
        if (!Schema::hasTable('scheduled_job_packs')) {
            $this->info("Creating 'scheduled_job_packs' table.");
            Schema::create('scheduled_job_packs', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('job_pack_id')->unsigned();
                $table->foreign('job_pack_id')->references('id')->on('job_packs');
                $table->integer('crew_id')->unsigned();
                $table->foreign('crew_id')->references('id')->on('crews');
                $table->timestamps();
            });
        }
    }

}
