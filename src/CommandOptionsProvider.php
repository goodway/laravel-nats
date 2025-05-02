<?php

namespace Goodway\LaravelNats;

use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\InputOption;

final class CommandOptionsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->extend(WorkCommand::class, function (WorkCommand $command) {
            $command->getDefinition()->addOption(
                new InputOption(
                    'jetstream',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Specify the jetstream name for the queue'
                )
            );
            $command->getDefinition()->addOption(
                new InputOption(
                    'consumer',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Specify the consumer for the queue'
                )
            );
            $command->getDefinition()->addOption(
                new InputOption(
                    'batch',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Specify the batch size for the consumer'
                )
            );

            return $command;
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
