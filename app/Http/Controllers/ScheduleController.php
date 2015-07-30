<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ScheduleController extends Controller {

    public function index() {
        $view = $this->getView('schedule/index');
        $view->current_tab = 'schedule';
        //$view->crews = Crew
        return $view;
    }

}
