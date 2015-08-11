<?php

namespace App\Http\Controllers;

use App\Model\Asset;

class DashboardController extends \App\Http\Controllers\Controller {

    public function home() {
        $this->view->title = $this->view->site_name;
        $this->view->asset_count = Asset::count();
        return $this->view;
    }

}
