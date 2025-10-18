<?php

namespace Goodway\LaravelNats\Support;

use Goodway\LaravelNats\Contracts\INatsMessageJob;
use Goodway\LaravelNats\Enum\NatsMessageFormat;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @property INatsMessageJob $job
 */
class NatsPendingDispatch extends PendingDispatch
{

    /**
     * Sets a specific jetstream when dispatching.
     * The jetstream specified in the global configuration and in the job class itself will be ignored.
     * @param string $jetstream
     * @return $this
     */
    public function onJetstream(string $jetstream): static
    {
        $this->job->setJetstream($jetstream);
        return $this;
    }

    /**
     * As an alternative to the onQueue() method. Provides the same effect
     * @param string $subject
     * @return $this
     */
    public function onSubject(string $subject): static
    {
        $this->job->setSubject($subject);
        return $this;
    }

    /**
     * To send message as simple JSON instead of serialized data
     * @return $this
     */
    public function asJson(): static
    {
        $this->job->setSendFormat(NatsMessageFormat::JSON);
        return $this;
    }

    /**
     * To call events on dispatch,
     * bypassing the global configuration and the attribute in the job class
     * @return $this
     */
    public function withEvents(): static
    {
        $this->job->setWithEvents(true);
        return $this;
    }

    /**
     * To prohibit calling events on dispatch,
     * bypassing the global configuration and the attribute in the job class
     */
    public function withoutEvents(): static
    {
        $this->job->setWithEvents(false);
        return $this;
    }

}