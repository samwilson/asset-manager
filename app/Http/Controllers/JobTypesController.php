<?php

namespace App\Http\Controllers;

use App\Model\JobType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JobTypesController extends Controller
{

    public function index()
    {
        if (!$this->user || !$this->user->isAdmin()) {
            return redirect('/');
        }
        $this->view->title = 'Job Types';
        $this->view->job_types = JobType::get();
        return $this->view;
    }

    public function edit($id)
    {
        if (!$this->user || !$this->user->isAdmin()) {
            return redirect('job-types');
        }
        $this->view->title = 'Job Type';
        $this->view->job_type = JobType::find($id);
        return $this->view;
    }

    public function save(Request $request)
    {
        if (!$this->user || !$this->user->isAdmin() || $request->input('cancel')) {
            return redirect('job-types');
        }
        $jt = JobType::findOrNew($request->input('id'));
        $jt->name = $request->input('name');
        $jt->colour = $request->input('colour');
        $jt->background_colour = $request->input('background_colour');
        $jt->save();
        return redirect('job-types');
    }
}
