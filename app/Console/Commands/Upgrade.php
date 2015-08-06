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
        $this->users();
        $this->assets();
        $this->contacts();
        $this->workOrdersAndCrews();
        $this->call('up');
    }

    protected function users() {
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
        if (User::count() == 0) {
            $this->info("No users found; creating 'admin'.");
            $adminUser = new User();
            $adminUser->username = 'admin';
            $adminUser->name = 'Administrator';
            $adminUser->password = bcrypt('admin');
            $adminUser->save();
        }
        if (!Schema::hasTable('roles')) {
            $this->info("Creating 'roles' table.");
            Schema::create('roles', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        $adminRole = Role::find(Role::ADMIN);
        if (!$adminRole) {
            $this->info("Creating admin role.");
            Role::firstOrCreate(['id' => Role::ADMIN, 'name' => trans_choice('roles.admin', 1)]);
        }
        $managerRole = Role::find(Role::MANAGER);
        if (!$managerRole) {
            $this->info("Creating manager role.");
            Role::firstOrCreate(['id' => Role::MANAGER, 'name' => trans_choice('roles.manager', 1)]);
        }
        $clerkRole = Role::find(Role::CLERK);
        if (!$clerkRole) {
            $this->info("Creating clerk role.");
            Role::firstOrCreate(['id' => Role::CLERK, 'name' => trans_choice('roles.clerk', 1)]);
        }
        if (!Schema::hasTable('role_user')) {
            $this->info("Creating 'role_user' table.");
            Schema::create('role_user', function(Blueprint $table) {
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->integer('role_id')->unsigned();
                $table->foreign('role_id')->references('id')->on('roles');
                $table->primary(['user_id', 'role_id']);
            });
        }
        // If there are no administrators, make the first user an admin.
        $adminRole = Role::find(Role::ADMIN);
        if (count(User::administrators()) === 0) {
            $adminUser = User::first();
            $this->info("Making " . $adminUser->name . " an Administrator.");
            $adminUser->roles()->save($adminRole);
        }
    }

    protected function assets() {
        if (!Schema::hasTable('assets')) {
            $this->info("Creating 'assets' table.");
            Schema::create('assets', function(Blueprint $table) {
                $table->increments('id');
                $table->string('identifier')->unique();
                $table->decimal('latitude', 13, 10);
                $table->decimal('longitude', 13, 10);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('categories')) {
            $this->info("Creating 'categories' table.");
            Schema::create('categories', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->integer('parent_id')->unsigned()->nullable();
                $table->foreign('parent_id')->references('id')->on('categories');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_category')) {
            $this->info("Creating 'asset_category' table.");
            Schema::create('asset_category', function(Blueprint $table) {
                $table->integer('asset_id')->unsigned();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('category_id')->unsigned();
                $table->foreign('category_id')->references('id')->on('categories');
                $table->primary(['asset_id', 'category_id']);
            });
        }
    }

    protected function contacts() {
        if (!Schema::hasTable('contacts')) {
            $this->info("Creating 'contacts' table.");
            Schema::create('contacts', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('phone_1');
                $table->string('phone_2');
                $table->decimal('latitude', 13, 10);
                $table->decimal('longitude', 13, 10);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_contact')) {
            $this->info("Creating 'asset_contact' table.");
            Schema::create('asset_contact', function(Blueprint $table) {
                $table->integer('asset_id')->unsigned();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('contact_id')->unsigned();
                $table->foreign('contact_id')->references('id')->on('contacts');
                $table->primary(['asset_id', 'contact_id']);
            });
        }
    }

    protected function workOrdersAndCrews() {
        if (!Schema::hasTable('work_order_types')) {
            $this->info("Creating 'work_order_types' table.");
            Schema::create('work_order_types', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('work_orders')) {
            $this->info("Creating 'work_orders' table.");
            Schema::create('work_orders', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->integer('work_order_type_id')->unsigned()->nullable();
                $table->foreign('work_order_type_id')->references('id')->on('work_order_types');
                $table->date('due_date')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_work_order')) {
            $this->info("Creating 'asset_work_order' table.");
            Schema::create('asset_work_order', function(Blueprint $table) {
                $table->integer('asset_id')->unsigned();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('work_order_id')->unsigned();
                $table->foreign('work_order_id')->references('id')->on('work_orders');
                $table->primary(['asset_id', 'work_order_id']);
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
        if (!Schema::hasTable('scheduled_work_orders')) {
            $this->info("Creating 'scheduled_work_orders' table.");
            Schema::create('scheduled_work_orders', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('work_order_id')->unsigned();
                $table->foreign('work_order_id')->references('id')->on('work_orders');
                $table->integer('crew_id')->unsigned();
                $table->foreign('crew_id')->references('id')->on('crews');
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->timestamps();
            });
        }
    }

}
