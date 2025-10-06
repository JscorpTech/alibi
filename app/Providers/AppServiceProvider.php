<?php

namespace App\Providers;

use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
	if (Env::get('REDIRECT_HTTPS', false)) {
	        \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('auth', function () {
            return Auth::check();
        });

       Blade::if('feature', fn($name) => config("feature.$name") === true);
    }
}
