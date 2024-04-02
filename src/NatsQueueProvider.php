<?php

namespace Goodway\LaravelNats;

use Goodway\LaravelNats\Queue\NatsQueueConnector;
use Illuminate\Support\ServiceProvider;

class NatsQueueProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/nats.php',
            'nats'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->offerPublishing();

        $manager = $this->app['queue'];
        /**
         * Register connector for 'nats' queue driver
         */
        $manager->addConnector('nats', function() {
            return new NatsQueueConnector();
        });
    }

    protected function offerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../config/nats.php' => config_path('nats.php'),
        ], 'nats-config');

    }
}
