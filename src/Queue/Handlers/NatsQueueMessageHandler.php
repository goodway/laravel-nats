<?php

namespace Goodway\LaravelNats\Queue\Handlers;

use Goodway\LaravelNats\DTO\NatsMessage;

class NatsQueueMessageHandler
{
    /**
     * @param NatsMessage $message deserialized message from queue
     * @param string $jetstream jetstream name
     * @param string $subject queue/subject name
     */
    public function __construct(NatsMessage $message, string $jetstream, string $subject) {

        var_dump(
            "\nNew message received from jetstream $jetstream , subject '$subject':\n"
            . $message->render()
        );

    }
}
