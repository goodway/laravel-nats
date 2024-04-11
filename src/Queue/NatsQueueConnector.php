<?php

namespace Goodway\LaravelNats\Queue;

use Goodway\LaravelNats\Exceptions\NatsClientException;
use Goodway\LaravelNats\NatsClientService;
use Goodway\LaravelNats\Queue\Handlers\NatsQueueHandler;
use Goodway\LaravelNats\Queue\Handlers\NatsQueueHandlerDefault;
use Illuminate\Queue\Connectors\ConnectorInterface;

class NatsQueueConnector implements ConnectorInterface
{
    public function __construct() {
        //
    }

    /**
     * @throws NatsClientException
     */
    public function connect(array $config): NatsQueue
    {
        $clientConfConsumer = $config['consumer_client'] ?? 'default';
        $clientConfPublisher = $config['publisher_client'] ?? 'default';
        $separateIdentical = isset($config['queue_separated_clients']) && $config['queue_separated_clients'];

        $clientSub = (new NatsClientService())->init($clientConfConsumer);
//        $clientSub->setDelay(0.01, NatsConfiguration::DELAY_LINEAR);

        $clientPub = $clientConfConsumer === $clientConfPublisher && !$separateIdentical ?
            $clientSub
            : (new NatsClientService())->init($clientConfPublisher)
        ;

        $queueHandler = isset($config['queue_handler'])
            && is_subclass_of($config['queue_handler'], NatsQueueHandler::class) ?
                $config['queue_handler']
                : NatsQueueHandlerDefault::class
        ;

        return new NatsQueue(
            $clientSub,
            $clientPub,
            $config['consumer'],
            $config['jetstream'],
            $config['jetstream_retention_policy'],
            consumerCreate:  $config['queue_consumer_create'] ?? false,
            consumerPrefix: $config['queue_consumer_prefix'],
            consumerIterations: $config['consumer_iterations'] ?? 0,
            consumerDelay: $config['consumer_delay'] ?? 0,
            batchSize: $config['default_batch_size'] ?? 0,
            fireEvents: $config['fire_events'] ?? null,
            queueHandler: $queueHandler,
            verbose: $config['verbose_mode'] ?? false,
        );

    }
}
