<?php

namespace Goodway\LaravelNats;

use Goodway\LaravelNats\Contracts\INatsMessageJob;
use Goodway\LaravelNats\DTO\NatsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class NatsMessageJob implements ShouldQueue, INatsMessageJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * You can set specific subject for your job message payload with $subject variable
     * @var string
     */
    protected string $subject = 'default';

    /**
     * Generates a message body to serialize
     * @return mixed
     */
    abstract public function body(): string;

    /**
     * Summary data that will be transmitted to your queue
     * @return NatsMessage
     */
    public function handle(): NatsMessage
    {
        return (new NatsMessage(
            $this->body(),
            $this->headers(),
            $this->subject,
            $this->getTimestamp(),
        ));
    }

    public function headers(): array
    {
        return [];
    }

    /**
     * Sets/updates subject for job message
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Returns default timestamp for message payload
     * @return int
     */
    public function getTimestamp(): int
    {
        /**
         * Optional as additional info.
         * Nats operate with nanoseconds, but it is not supported in PHP. Returns ms instead.
         */
        return now()->getTimestampMs();
    }

}
