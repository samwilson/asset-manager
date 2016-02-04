<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{

    use DispatchesJobs,
        ValidatesRequests;

    /** @var \Illuminate\View\View */
    protected $view;

    /** @var \App\Model\User */
    protected $user;

    public function __construct()
    {
        $this->instantiateView();
    }

    protected function instantiateView()
    {
        $route = \Illuminate\Support\Facades\Request::route()->getAction()['controller'];
        $parts = explode('@', $route);
        $controllerParts = explode('\\', $parts[0]);
        $controllerName = array_pop($controllerParts);
        $controller = str_slug(snake_case(substr($controllerName, 0, -strlen('Controller'))));
        $action = str_slug(snake_case($parts[1]));

        // Set up the view based on the module, controller, and action names.
        $viewFile = "$controller.$action";
        foreach (\Module::slugs() as $mod) {
            if (view()->exists("$mod::$viewFile")) {
                $this->view = view("$mod::$viewFile");
            }
        }
        if (!$this->view && view()->exists($viewFile)) {
            $this->view = view($viewFile);
        } elseif (!$this->view) {
            $this->view = view('base');
        }

        // Set up some more view variables.
        $this->view->controller = $controller;
        $this->view->action = $action;
        $this->view->app_env = env('APP_ENV');
        $this->view->app_version = config('app.version');
        $this->view->app_title = config('app.title');
        $this->view->site_name = config('app.site_name');
        $this->view->scripts = [];
        $this->view->return_to = substr(\Request::url(), strlen(url('')));
        $this->user = Auth::user();
        $this->view->user = $this->user;
        $this->view->tab = 'schedule';
        $this->view->alerts = \Session::get('alerts', array());
        $this->view->modules = \Module::slugs();
    }

    /**
     * Display a message to the user.
     * @link http://getbootstrap.com/components/#alerts
     * @param string $status One of: success, info, warning, or danger.
     * @param string $message The message to display.
     * @param boolean $delayed Whether to display it now, or flash it to the next request.
     */
    protected function alert($status, $message, $delayed = true)
    {
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
