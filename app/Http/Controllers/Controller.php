<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController {

    use DispatchesJobs,
        ValidatesRequests;

    /** @var \Illuminate\View\View */
    protected $view;

    public function __construct() {
        $this->instantiate_view();
    }

    protected function instantiate_view() {
        $route = \Illuminate\Support\Facades\Request::route()->getAction()['controller'];
        $parts = explode('@', $route);
        $controllerParts = explode('\\', $parts[0]);
        $controllerName = array_pop($controllerParts);
        $controller = snake_case(substr($controllerName, 0, -strlen('Controller')));
        $action = snake_case($parts[1]);
        $viewFile = "$controller.$action";
        if (!file_exists(app_path("../resources/views/$controller/$action.twig"))) {
            $this->view = view('home');
        } else {
            $this->view = view($viewFile);
        }
        $this->view->app_env = env('APP_ENV');
        $this->view->app_version = config('app.version');
        $this->view->app_title = config('app.title');
        $this->view->site_name = config('app.site_name');
        $this->view->scripts = array(
            'jquery.js',
            'foundation.min.js',
            'app.js',
        );
        $this->view->user = Auth::user();
        $this->view->tab = 'schedule';
        $this->view->alerts = \Session::get('alerts', array());
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
