<?php

namespace App\EIS\Providers;

use Illuminate\Support\ServiceProvider;
use App\EIS\Services\Http\HttpClientService;
use App\EIS\Services\Authentication\AuthenticationService;

class EisServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/eis.php',
            'eis'
        );

        $this->app->singleton(AuthenticationService::class);

        $this->app->singleton(HttpClientService::class, function ($app) {
            return new HttpClientService(
                $app->make(AuthenticationService::class)
            );
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('app/EIS/routes.php'));
    }
}