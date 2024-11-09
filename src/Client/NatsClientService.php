<?php

namespace Goodway\LaravelNats\Client;

use Basis\Nats\Client as NatsClient;
use Basis\Nats\Configuration as NatsConfiguration;
use Goodway\LaravelNats\Contracts\INatsClientProvider;
use Goodway\LaravelNats\DTO\NatsClientConfiguration;
use Goodway\LaravelNats\Exceptions\NatsClientException;

class NatsClientService implements INatsClientProvider
{
    public function __construct(
    ) {}


    /**
     * Initialize Nats Client with specific configuration
     * @param string|array $configuration If string then import from $configurationFileName.php configuration file
     * @param string $configurationFileName Name of your nats configuration php file
     * @return NatsClient
     * @throws NatsClientException
     */
    public function init(
        string|array    $configuration = 'default',
        string          $configurationFileName = 'nats',
    ): NatsClient
    {
        $configArray = is_array($configuration) ? $configuration
            : config("$configurationFileName.client.configurations.$configuration")
        ;

        if (
            !is_array($configArray)
            || !isset($configArray['host'], $configArray['port'])
        )
        {
            throw new NatsClientException('Invalid client configuration');
        }

        $natsConfiguration = new NatsConfiguration(
            NatsClientConfiguration::fromArray($configArray)->provideNats()
        );

        return new NatsClient($natsConfiguration);
    }

}
