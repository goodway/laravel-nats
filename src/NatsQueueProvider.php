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
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $manager = $this->app['queue'];
        /**
         * Register connector for 'nats' queue driver
         */
        $manager->addConnector('nats', function() {
            return new NatsQueueConnector();
        });
    }
}
