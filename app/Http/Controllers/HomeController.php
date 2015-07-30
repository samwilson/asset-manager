<?php

namespace App\Http\Controllers;

class HomeController extends \App\Http\Controllers\Controller {

    public function home() {
        return $this->getView('home');
    }

}
