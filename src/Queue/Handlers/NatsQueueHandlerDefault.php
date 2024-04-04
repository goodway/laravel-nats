<?php

namespace Goodway\LaravelNats\Queue\Handlers;

use Basis\Nats\Consumer\Consumer;

/**
 * Default Strategy/Template that describes the options of receiving data from a queue
 */
class NatsQueueHandlerDefault extends NatsQueueHandler
{

    public int $batchSize = 20;
    public int $iterations = 3;

    /**
     * Default handler for a new message received
     * @param $message
     * @param string $queue
     * @param Consumer $consumer
     * @return void
     */
    public function handle($message, string $queue, Consumer $consumer): void
    {
        var_dump(self::class . ' handle() called');
        new NatsQueueMessageHandler($message, $this->queue);
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


    public function interruptOn($message, string $queue, Consumer $consumer): bool
    {
        return false;
    }
}