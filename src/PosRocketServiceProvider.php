<?php

namespace Msh\POSRocket;

use Illuminate\Support\ServiceProvider;

class PosRocketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Msh\POSRocket\PosRocketController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
        $this->publishes([
            __DIR__.'/config/posrocket.php' =>  config_path('posrocket.php'),
        ], 'config');
    }
}
