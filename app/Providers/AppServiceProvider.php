<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if($this->app->environment('production')) {
            // Forzar HTTPS
            URL::forceScheme('https');

            // Configurar la detección de HTTPS detrás de proxy
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                $this->app['request']->server->set('HTTPS', 'on');
                $this->app['request']->server->set('SERVER_PORT', 443);
            }

            // Asegurar que las URLs generadas usen el host correcto
            if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                URL::forceRootUrl('https://' . $_SERVER['HTTP_X_FORWARDED_HOST']);
            }
        }
    }
}
