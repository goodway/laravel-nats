<?php

namespace Goodway\LaravelNats;

use Goodway\LaravelNats\Contracts\INatsMessageJob;
use Goodway\LaravelNats\DTO\NatsMessage;
use Goodway\LaravelNats\Enum\NatsMessageFormat;
use Goodway\LaravelNats\Support\NatsDispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class NatsMessageJob implements ShouldQueue, INatsMessageJob
{
    use Dispatchable, NatsDispatchable;
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * You can set specific subject and jetstream for your job message payload
     * with $subject and $jetstream variables
     */
    protected ?string $subject = null;
    protected ?string $jetstream = null;

    protected ?bool   $withEvents = null;

    /**
     * Defines the format used when sending a message. Nats message object or simple JSON
     * @var NatsMessageFormat
     */
    protected NatsMessageFormat $sendFormat = NatsMessageFormat::OBJECT_ORIGIN;

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
     * Sets a specific jetstream for job message
     * @param string $jetstream
     * @return $this
     */
    public function setJetstream(string $jetstream): static
    {
        $this->jetstream = $jetstream;
        return $this;
    }

    /**
     * Specifies whether to call events or not for a specific job
     */
    public function setWithEvents(bool $withEvents): static
    {
        $this->withEvents = $withEvents;
        return $this;
    }

    /**
     * Specifies the format used when sending a message. Nats message object or simple JSON
     * @param NatsMessageFormat $format
     * @return $this
     */
    public function setSendFormat(NatsMessageFormat $format): static
    {
        if (in_array($format, [NatsMessageFormat::OBJECT_ORIGIN, NatsMessageFormat::JSON])) {
            $this->sendFormat = $format;
        }
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getJetstream(): ?string
    {
        return $this->jetstream;
    }

    public function getWithEvents(): ?bool
    {
        return $this->withEvents;
    }

    public function getSendFormat(): NatsMessageFormat
    {
        return $this->sendFormat;
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
