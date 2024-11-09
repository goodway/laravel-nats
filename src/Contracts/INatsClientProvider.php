<?php

namespace Goodway\LaravelNats\Contracts;

use Basis\Nats\Client as NatsClient;

interface INatsClientProvider
{
    public function init(
        string|array    $configuration = 'default',
        string          $configurationFileName = 'nats',
    ): NatsClient;
}