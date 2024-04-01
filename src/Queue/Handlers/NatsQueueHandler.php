<?php

namespace Goodway\LaravelNats\Queue\Handlers;


use Goodway\LaravelNats\Events\NatsQueueMessageReceived;
use Illuminate\Support\Facades\Log;

class NatsQueueHandler
{
    /**
     * @param string $message serialized message from queue
     * @param string $topic topic name
     */
    public function __construct(string $message, string $topic) {

        event(new NatsQueueMessageReceived($topic, $message));

    }
}
