<?php

namespace App\Http\Controllers;

use App\Model\Asset;
use App\Model\User;
use App\Model\Crew;
use App\Model\JobList;
use App\Model\Job;
use App\Model\Tag;

class DashboardController extends \App\Http\Controllers\Controller
{

    public function home()
    {
        $this->view->title = $this->view->site_name;
        $this->view->asset_count = Asset::count();
        $this->view->user_count = User::count();
        $this->view->crew_count = Crew::count();
        $this->view->job_list_count = JobList::count();
        $this->view->job_count = Job::count();
        $this->view->tag_count = Tag::count();
        return $this->view;
    }
}
