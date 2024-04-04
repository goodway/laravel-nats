<?php

namespace Goodway\LaravelNats\Queue\Handlers;

class NatsQueueMessageHandler
{
    /**
     * @param string $message serialized message from queue
     * @param string $subject queue/subject name
     */
    public function __construct(string $message, string $subject) {

        var_dump("\nNew message received from queue '$subject':\n" . $message);

    }
}
