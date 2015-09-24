<?php

namespace App\Http\Controllers;

use App\Model\Asset;
use App\Model\User;
use App\Model\Crew;

class DashboardController extends \App\Http\Controllers\Controller {

    public function home() {
        $this->view->title = $this->view->site_name;
        $this->view->asset_count = Asset::count();
        $this->view->user_count = User::count();
        $this->view->crew_count = Crew::count();
        return $this->view;
    }

}
