<?php

namespace Goodway\LaravelNats\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NatsQueueMessageSent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $subject,
        public string $message
    ) {}

}
