<?php

namespace Goodway\LaravelNats;

use Goodway\LaravelNats\Contracts\INatsClientProvider;
use Goodway\LaravelNats\Queue\NatsQueueConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class LaravelNatsProvider extends ServiceProvider
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

        $this->app->register(NatsClientProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(QueueManager $manager, INatsClientProvider $natsClient): void
    {
        $this->offerPublishing();

        /**
         * Register connector for 'nats' queue driver
         */
        $manager->addConnector('nats', fn() => new NatsQueueConnector($natsClient));
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
