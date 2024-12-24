<?php

namespace App\Providers;

use Aacotroneo\Saml2\Saml2Auth;
use Illuminate\Support\ServiceProvider;

class Saml2ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('Saml2Auth', function ($app) {
            return new Saml2Auth($app['request']);
        });
    }

    public function boot(): void
    {
        // Si necesitas realizar alguna configuración adicional durante el boot
    }
}
