<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobTypesController extends Controller {

    public function index() {
        if (!$this->user || !$this->user->isAdmin()) {
            return redirect('/');
        }
        $this->view->title = 'Job Types';
        $this->view->job_types = \App\Model\JobType::get();
        return $this->view;
    }

    public function edit($id) {
        $this->view->title = 'Job Type';
        $this->view->job_type = \App\Model\JobType::find($id);
        return $this->view;
    }

}
