<?php

namespace Goodway\LaravelNats\Events;

use Goodway\LaravelNats\DTO\NatsMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NatsQueueMessageReceived
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly string $jetstream,
        public readonly string $subject,
        public readonly NatsMessage $message
    ) {}

}
