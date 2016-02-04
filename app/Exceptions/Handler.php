<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }

    protected function renderHttpException(HttpException $e)
    {
        $status = $e->getStatusCode();
        if (view()->exists("errors.{$status}")) {
            $viewData = [
                'app_env' => env('APP_ENV'),
                'app_version' => config('app.version'),
                'app_title' => config('app.title'),
                'site_name' => config('app.site_name'),
                'scripts' => [
                    'jquery.js',
                    'jquery-ui.min.js',
                    'foundation.min.js',
                    'tag-it.min.js',
                    'app.js',
                ],
                'modules' => \Module::slugs(),
                'exception' => $e,
            ];
            return response()->view("errors.{$status}", $viewData, $status);
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }
}
