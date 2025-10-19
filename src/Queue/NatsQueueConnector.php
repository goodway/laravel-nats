<?php

namespace Goodway\LaravelNats\Queue;

use Goodway\LaravelNats\Contracts\INatsClientProvider;
use Goodway\LaravelNats\DTO\NatsQueueCommandOptions;
use Goodway\LaravelNats\Queue\Handlers\NatsQueueHandler;
use Goodway\LaravelNats\Queue\Handlers\NatsQueueHandlerDefault;
use Illuminate\Queue\Connectors\ConnectorInterface;

class NatsQueueConnector implements ConnectorInterface
{
    public function __construct(
        public INatsClientProvider $natsClient,
        protected ?NatsQueueCommandOptions $commandOptions = null,
    ) {}

    /**
     */
    public function connect(array $config): NatsQueue
    {
        $clientConfConsumer = $config['consumer_client'] ?? 'default';
        $clientConfPublisher = $config['publisher_client'] ?? 'default';
        $separateIdentical = isset($config['queue_separated_clients']) && $config['queue_separated_clients'];

        $clientSub = $this->natsClient->init($clientConfConsumer);
        $clientPub = $clientConfConsumer === $clientConfPublisher && !$separateIdentical ?
            $clientSub
            : $this->natsClient->init($clientConfPublisher)
        ;

        $queueHandler = isset($config['queue_handler'])
            && is_subclass_of($config['queue_handler'], NatsQueueHandler::class) ?
                $config['queue_handler']
                : NatsQueueHandlerDefault::class
        ;

        $queue = new NatsQueue(
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
            checkJetstreamOnPublish: (bool)($config['check_jetstream_publish'] ?? false),
        );

        return $this->setQueueCommandOptions($queue);
    }


    private function setQueueCommandOptions(NatsQueue $queue): NatsQueue
    {
        if (!$this->commandOptions) {
            return $queue;
        }

        if ($this->commandOptions->jetstream) {
            $queue->setJetstream($this->commandOptions->jetstream);
        }

        if ($this->commandOptions->consumer) {
            $queue->setConsumer($this->commandOptions->consumer);
        }

        if ($this->commandOptions->batchSize) {
            $queue->setBatchSize($this->commandOptions->batchSize);
        }

        return $queue;
    }
}
