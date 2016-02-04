<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $paths = [
            base_path('vendor/zurb/foundation/dist/foundation.min.css') => public_path('css/foundation.min.css'),
            base_path('vendor/zurb/foundation/dist/foundation.min.js') => public_path('js/foundation.min.js'),
        ];
        $this->publishes($paths, 'public');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
