<?php

namespace Goodway\LaravelNats;

use Goodway\LaravelNats\Client\NatsClientService;
use Goodway\LaravelNats\Contracts\INatsClientProvider;
use Illuminate\Support\ServiceProvider;

class NatsClientProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(INatsClientProvider::class, function () {
            return new NatsClientService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
