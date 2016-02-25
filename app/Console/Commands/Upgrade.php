<?php

namespace App\Console\Commands;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Model\User;
use App\Model\Role;
use App\Model\JobResolution;

class Upgrade extends \Illuminate\Console\Command
{

    protected $name = "upgrade";
    protected $description = "Upgrade the application. Idempotent.";

    /**
     * Execute the 'upgrade' console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('down');
        $this->info("Upgrading application.");
        $this->users();
        $this->assets();
        $this->files();
        $this->contacts();
        $this->jobLists();
        $this->call('up');
    }

    protected function users()
    {
        if (!Schema::hasTable('users')) {
            $this->info("Creating 'users' table.");
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('username')->unique();
                $table->string('email')->nullable();
                $table->string('password', 60);
                $table->string('password_reminder', 100)->nullable();
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
            $adminUser->email = config('app.site_email');
            $adminUser->save();
        }
        if (!Schema::hasTable('roles')) {
            $this->info("Creating 'roles' table.");
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Role::find(Role::ADMIN)) {
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
            Schema::create('role_user', function (Blueprint $table) {
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
        if (!Schema::hasTable('unavailability_types')) {
            $this->info("Creating 'unavailability_types' table.");
            Schema::create('unavailability_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('background_colour');
                $table->string('colour');
            });
        }
        if (!Schema::hasTable('user_unavailabilities')) {
            $this->info("Creating 'user_unavailabilities' table.");
            Schema::create('user_unavailabilities', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('type_id')->unsigned()->nullable();
                $table->foreign('type_id')->references('id')->on('unavailability_types');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('queued_emails')) {
            $this->info("Creating 'queued_emails' table.");
            Schema::create('queued_emails', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('recipient_id')->unsigned()->nullable();
                $table->foreign('recipient_id')->references('id')->on('users');
                $table->string('subject');
                $table->string('template');
                $table->text('data');
                $table->timestamps();
            });
        }
    }

    public function files()
    {
        if (!Schema::hasTable('files')) {
            $this->info("Creating 'files' table.");
            Schema::create('files', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('mime_type');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_file')) {
            $this->info("Creating 'asset_file' table.");
            Schema::create('asset_file', function (Blueprint $table) {
                $table->integer('asset_id')->unsigned();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('file_id')->unsigned();
                $table->foreign('file_id')->references('id')->on('files');
                $table->primary(['asset_id', 'file_id']);
            });
        }
    }

    protected function assets()
    {
        if (!Schema::hasTable('countries')) {
            $this->info("Creating 'countries' table.");
            Schema::create('countries', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('states')) {
            $this->info("Creating 'states' table.");
            Schema::create('states', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->integer('country_id')->unsigned()->nullable();
                $table->foreign('country_id')->references('id')->on('countries');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('suburbs')) {
            $this->info("Creating 'suburbs' table.");
            Schema::create('suburbs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('assets')) {
            $this->info("Creating 'assets' table.");
            Schema::create('assets', function (Blueprint $table) {
                $table->increments('id');
                $table->string('identifier')->unique();
                $table->integer('state_id')->unsigned()->nullable();
                $table->foreign('state_id')->references('id')->on('states');
                $table->integer('suburb_id')->unsigned()->nullable();
                $table->foreign('suburb_id')->references('id')->on('suburbs');
                $table->string('street_address')->nullable();
                $table->string('location_description')->nullable();
                $table->decimal('latitude', 13, 10)->nullable();
                $table->decimal('longitude', 13, 10)->nullable();
                $table->text('comments')->nullable();
                $table->timestamps();
            });
        }
        if (Schema::hasTable('asset_category')) {
            $this->info("Dropping 'asset_category' table.");
            Schema::drop('asset_category');
        }
        if (Schema::hasTable('categories')) {
            $this->info("Dropping 'categories' table.");
            \DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            Schema::drop('categories');
            \DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }
        if (!Schema::hasTable('tags')) {
            $this->info("Creating 'tags' table.");
            Schema::create('tags', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_tag')) {
            $this->info("Creating 'asset_tag' table.");
            Schema::create('asset_tag', function (Blueprint $table) {
                $table->integer('asset_id')->unsigned();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('tag_id')->unsigned();
                $table->foreign('tag_id')->references('id')->on('tags');
                $table->primary(['asset_id', 'tag_id']);
            });
        }
    }

    protected function contacts()
    {
        if (!Schema::hasTable('contacts')) {
            $this->info("Creating 'contacts' table.");
            Schema::create('contacts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('phone_1')->nullable();
                $table->string('phone_2')->nullable();
                $table->decimal('latitude', 13, 10)->nullable();
                $table->decimal('longitude', 13, 10)->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('asset_contact')) {
            $this->info("Creating 'asset_contact' table.");
            Schema::create('asset_contact', function (Blueprint $table) {
                $table->integer('asset_id')->unsigned();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->integer('contact_id')->unsigned();
                $table->foreign('contact_id')->references('id')->on('contacts');
                $table->primary(['asset_id', 'contact_id']);
            });
        }
    }

    protected function jobLists()
    {
        if (!Schema::hasTable('job_types')) {
            $this->info("Creating 'job_types' table.");
            Schema::create('job_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->string('colour');
                $table->string('background_colour');
                $table->boolean('contact_required')->default(true);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('crews')) {
            $this->info("Creating 'crews' table.");
            Schema::create('crews', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->text('comments');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('crew_members')) {
            $this->info("Creating 'crew_members' table.");
            Schema::create('crew_members', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('crew_id')->unsigned()->nullable();
                $table->foreign('crew_id')->references('id')->on('crews');
                $table->integer('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users');
                $table->timestamps();
                $table->unique(['crew_id', 'user_id']);
            });
        }
        if (!Schema::hasTable('crew_unavailabilities')) {
            $this->info("Creating 'crew_unavailabilities' table.");
            Schema::create('crew_unavailabilities', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('crew_id')->unsigned()->nullable();
                $table->foreign('crew_id')->references('id')->on('crews');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('type_id')->unsigned()->nullable();
                $table->foreign('type_id')->references('id')->on('unavailability_types');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('job_lists')) {
            $this->info("Creating 'job_lists' table.");
            Schema::create('job_lists', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->integer('type_id')->unsigned();
                $table->foreign('type_id')->references('id')->on('job_types');
                $table->integer('crew_id')->unsigned()->nullable();
                $table->foreign('crew_id')->references('id')->on('crews');
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->text('comments')->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('job_list_tag')) {
            $this->info("Creating 'job_list_tag' table.");
            Schema::create('job_list_tag', function (Blueprint $table) {
                $table->integer('job_list_id')->unsigned();
                $table->foreign('job_list_id')->references('id')->on('job_lists');
                $table->integer('tag_id')->unsigned();
                $table->foreign('tag_id')->references('id')->on('tags');
                $table->primary(['job_list_id', 'tag_id']);
            });
        }
        if (!Schema::hasTable('job_resolutions')) {
            $this->info("Creating 'job_resolutions' table.");
            Schema::create('job_resolutions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique();
                $table->enum('type', [
                    JobResolution::SUCCEEDED,
                    JobResolution::CANCELLED,
                    JobResolution::FAILED,
                ])->default(JobResolution::SUCCEEDED);
                $table->text('comments');
                $table->timestamps();
            });
        }
        $jobResolutionSucceeded = JobResolution::whereType(JobResolution::SUCCEEDED)->first();
        if (!$jobResolutionSucceeded) {
            $this->info("Creating job-resolution 'succeeded'.");
            JobResolution::firstOrCreate([
                'id' => JobResolution::SUCCEEDED,
                'name' => trans('job-resolutions.succeeded'),
                'type' => JobResolution::SUCCEEDED,
            ]);
        }
        $jobResolutionCancelled = JobResolution::whereType(JobResolution::CANCELLED)->first();
        if (!$jobResolutionCancelled) {
            $this->info("Creating job-resolution 'cancelled'.");
            JobResolution::firstOrCreate([
                'id' => JobResolution::CANCELLED,
                'name' => trans('job-resolutions.cancelled'),
                'type' => JobResolution::CANCELLED,
            ]);
        }
        $jobResolutionFailed = JobResolution::whereType(JobResolution::FAILED)->first();
        if (!$jobResolutionFailed) {
            $this->info("Creating job-resolution 'failed'.");
            JobResolution::firstOrCreate([
                'id' => JobResolution::FAILED,
                'name' => trans('job-resolutions.failed'),
                'type' => JobResolution::FAILED,
            ]);
        }
        if (!Schema::hasTable('jobs')) {
            $this->info("Creating 'jobs' table.");
            Schema::create('jobs', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('job_list_id')->unsigned()->nullable();
                $table->foreign('job_list_id')->references('id')->on('job_lists');
                $table->integer('asset_id')->unsigned()->nullable();
                $table->foreign('asset_id')->references('id')->on('assets');
                $table->date('date_added')->comment('Date added to Job List.');
                $table->date('date_removed')->nullable()->comment('Date removed from Job List.');
                $table->date('date_resolved')->nullable()->comment('Date of completion or failure.');
                $table->integer('resolution_id')->unsigned()->nullable();
                $table->foreign('resolution_id')->references('id')->on('job_resolutions');
                $table->text('instructions')->nullable();
                $table->text('resolution_comments')->nullable();
                $table->timestamps();
                $table->unique(['job_list_id', 'asset_id']);
            });
        }
        if (!Schema::hasTable('contact_attempts')) {
            $this->info("Creating 'contact_attempts' table.");
            Schema::create('contact_attempts', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('job_id')->unsigned();
                $table->foreign('job_id')->references('id')->on('jobs');
                $table->integer('contact_id')->unsigned();
                $table->foreign('contact_id')->references('id')->on('contacts');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->dateTime('date_and_time_attempted');
                $table->boolean('contact_made')->default(false);
                $table->text('comments');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('job_tag')) {
            $this->info("Creating 'job_tag' table.");
            Schema::create('job_tag', function (Blueprint $table) {
                $table->integer('job_id')->unsigned();
                $table->foreign('job_id')->references('id')->on('jobs');
                $table->integer('tag_id')->unsigned();
                $table->foreign('tag_id')->references('id')->on('tags');
                $table->primary(['job_id', 'tag_id']);
            });
        }
    }
}
