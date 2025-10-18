<?php

namespace Goodway\LaravelNats\Queue\Handlers;

use Basis\Nats\Consumer\Consumer;
use Goodway\LaravelNats\DTO\NatsMessage;

/**
 * Default Strategy/Template that describes the options of receiving data from a queue
 */
class NatsQueueHandlerDefault extends NatsQueueHandler
{

    public int $batchSize = 20;
    public int $iterations = 3;

    /**
     * Default handler for a new message received
     * @param NatsMessage $message
     * @param string $jetstream
     * @param string $queue
     * @param Consumer $consumer
     * @return void
     */
    public function handle(NatsMessage $message, string $jetstream, string $queue, Consumer $consumer): void
    {
        new NatsQueueMessageHandler($message, $jetstream, $this->queue);
    }

    /**
     * Default handler for an empty response received
     * @param string $queue
     * @param Consumer $consumer
     * @return void
     */
    public function handleEmpty(string $queue, Consumer $consumer): void
    {
        var_dump('..');
    }


    public function interruptOn(NatsMessage $message, string $queue, Consumer $consumer): bool
    {
        return false;
    }
}