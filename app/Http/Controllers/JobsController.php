<?php

namespace App\Http\Controllers;

use App\Model\Job;
use Illuminate\Http\Request;

class JobsController extends Controller
{

    public function mobile($id)
    {
        return $this->view;
    }

    public function view(Request $request, $id)
    {
        $job = Job::find($id);
        if ($job === null) {
            abort(404);
        }
        $this->view->breadcrumbs = [
            'job-lists' => 'Job Lists',
            'job-lists/' . $job->job_list_id => $job->jobList->name,
            'jobs/' => 'Job #' . $job->id,
        ];
        $this->view->title = $job->id;
        $this->view->job = $job;
        return $this->view;
    }
}
