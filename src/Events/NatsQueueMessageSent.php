<?php

namespace Goodway\LaravelNats\Events;

use Goodway\LaravelNats\DTO\NatsMessage;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NatsQueueMessageSent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly string $subject,
        public readonly NatsMessage $message
    ) {}

}
