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
        $ven = base_path() . '/vendor';
        $assets = [
            "$ven/shramov/leaflet-plugins/layer/tile/Bing.js" => public_path('vendor/Bing.js'),
        ];
        $this->publishes($assets, 'app');
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
