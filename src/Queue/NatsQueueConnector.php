<?php

namespace Goodway\LaravelNats\Queue;

use Basis\Nats\Client as NatsClient;
use Basis\Nats\Configuration as NatsConfiguration;
use Illuminate\Queue\Connectors\ConnectorInterface;

class NatsQueueConnector implements ConnectorInterface
{
    public function __construct() {
        //
    }

    public function connect(array $config): NatsQueue
    {
        $configuration = new NatsConfiguration(
            [
                'host' => $config['host'],
                'port' => $config['port'],
                'user' => $config['user'],
                'pass' => $config['password'],
                'token' => $config['token'],
                'nkey' => $config['nkey'],
                'jwt' => $config['jwt'],
                'reconnect' => $config['reconnect'],
                'timeout' => $config['connection_timeout'],
                'verbose' => $config['verbose_mode'],
                'inboxPrefix' => $config['inbox_prefix'],
                'pingInterval' => $config['ping_interval'],
                'tlsKeyFile' => $config['ssl_key'],
                'tlsCertFile' => $config['ssl_cert'],
                'tlsCaFile' => $config['ssl_ca'],
            ],
        );
//        $configuration->setDelay(0.001, NatsConfiguration::DELAY_LINEAR);

        $clientSub = new NatsClient($configuration);
        $clientPub = new NatsClient($configuration);

        return new NatsQueue(
            $clientSub,
            $clientPub,
            $config['consumer'],
            $config['jetstream'],
            $config['jetstream_retention_policy'],
            $config['queue_consumer_prefix'],
            $config['consumer_iterations'],
            $config['default_batch_size'],
        );

    }
}
