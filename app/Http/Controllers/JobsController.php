<?php

namespace App\Http\Controllers;

use App\Model\Job;
use App\Model\JobResolution;
use Illuminate\Http\Request;

class JobsController extends Controller
{

    public function index()
    {
        $jobs = Job::paginate();
        $this->view->title = 'Jobs';
        $this->view->jobs = $jobs;
        return $this->view;
    }

    public function edit(Request $request, $id)
    {
        $job = Job::find($id);
        if (!$job) {
            abort(404);
        }
        $this->view->return_to = $request->query('return_to');
        $this->view->title = 'Job #' . $job->id;
        $this->view->job = $job;
        $this->view->breadcrumbs = [
            'job-lists' => 'Job Lists',
            'job-lists/' . $job->job_list_id => $job->jobList->name,
            'jobs/' . $job->id => 'Job #' . $job->id,
        ];
        $this->view->jobResolutions = JobResolution::all();
        return $this->view;
    }

    public function save(Request $request, $id)
    {
        if (!$this->user || !$this->user->isClerk()) {
            $this->alert('warning', 'Only Clerks are allowed to edit jobs.', false);
            return $this->view;
        }
        $job = Job::find($id);
        $job->resolution_id = $request->input('resolution_id');
        $job->date_resolved = $request->input('date_resolved');
        $job->resolution_comments = $request->input('resolution_comments');
        $job->save();
        $returnTo = $request->input('return_to', "jobs/$job->id");
        return redirect($returnTo);
    }

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
        $this->view->title = 'Job #' . $job->id;
        $this->view->job = $job;
        $this->view->quick_t = 'j';
        $this->view->quick_s = $job->id;
        return $this->view;
    }
}
