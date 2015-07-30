<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController {

    use DispatchesJobs,
        ValidatesRequests;

    /**
     * 
     * @param string $viewName
     * @return type
     */
    public function getView($viewName) {
        $view = view($viewName);
        $view->app_env = env('APP_ENV');
        $view->app_version = config('app.version');
        $view->app_title = config('app.title');
        $view->site_name = config('app.site_name');
        $view->scripts = array(
            'jquery.js',
            'foundation.min.js',
            'app.js',
        );
        $view->user = Auth::user();
        $view->tab = 'schedule';
        $view->alerts = \Session::get('alerts', array());
        return $view;
    }

    /**
     * Display a message to the user.
     * @link http://getbootstrap.com/components/#alerts
     * @param string $status One of: success, info, warning, or danger.
     * @param string $message The message to display.
     * @param boolean $delayed Whether to display it now, or flash it to the next request.
     */
    protected function alert($status, $message, $delayed = true) {
        $alert = array(
            'status' => $status,
            'message' => $message,
        );
        if ($delayed) {
            $alerts = \Session::get('alerts', []);
            $alerts[] = $alert;
            \Session::flash('alerts', $alerts);
        } else {
            $this->view->alerts[] = $alert;
        }
    }

}
