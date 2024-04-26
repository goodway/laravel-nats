<?php

namespace Goodway\LaravelNats\Queue\Handlers;

use Basis\Nats\Client as NatsClient;
use Basis\Nats\Consumer\Consumer;
use Goodway\LaravelNats\Contracts\INatsQueueHandler;
use Goodway\LaravelNats\DTO\NatsMessage;
use Goodway\LaravelNats\Events\NatsQueueMessageReceived;
use Goodway\LaravelNats\Exceptions\NatsConsumerException;

abstract class NatsQueueHandler implements INatsQueueHandler
{
    public int $batchSize = 10;
    public int $iterations = 2;
    public float $delay = 1;

    public bool $canCreateConsumer = false;
    public bool $fireEvent = true;


    abstract public function handle(NatsMessage $message, string $queue, Consumer $consumer);
    abstract public function handleEmpty(string $queue, Consumer $consumer);


    public function __construct(
        public NatsClient   $client,
        protected string    $jetStream,
        protected string    $consumer,
        protected string    $queue,
    ) {}

    public function setBatchSize(int $batchSize = 10): static
    {
        $this->batchSize = $batchSize;
        return $this;
    }

    public function setIterations(int $iterations = 3): static
    {
        $this->iterations = $iterations;
        return $this;
    }

    public function setDelay(float $delay = 1): static
    {
        $this->delay = $delay;
        return $this;
    }

    public function withEvent(bool $state = true): static
    {
        $this->fireEvent = $state;
        return $this;
    }

    public function allowToCreateConsumer(bool $state = true): static
    {
        $this->canCreateConsumer = $state;
        return $this;
    }

    public function canCreateConsumer(): bool
    {
        return $this->canCreateConsumer;
    }

    /**
     * Common queue pop mechanism
     * @return void
     */
    public function pop(): void
    {
        try {

            $stream = $this->client->getApi()->getStream($this->jetStream);
            $consumer = $stream->getConsumer($this->consumer);

            if (!$consumer->exists()) {
                $excMsg = "Consumer '" . $this->consumer . "' doesn't exist";
                if ($this->canCreateConsumer) {
                    $consumer->getConfiguration()->setSubjectFilter($this->queue);
                    $consumer->create();
                    $excMsg .= "\nTrying to create new consumer for a next listen ...";
                }

                throw new NatsConsumerException($excMsg, 404);
            }

            $consumer->setBatching($this->batchSize) // how many messages would be requested from nats stream
            ->setIterations($this->iterations) // how many times message request should be sent
            ->setDelay($this->delay)
            ->handle(
                function ($message) use ($consumer) {

                    $messageData = unserialize($message) ?: '';
                    $messageObj = NatsMessage::parse($messageData, true);

                    if ($this->fireEvent) {
                        event(new NatsQueueMessageReceived($this->queue, $messageObj));
                    }

                    $this->handle($messageObj, $this->queue, $consumer);

                    if ($this->interruptOn($messageObj, $this->queue, $consumer)) {
                        $consumer->interrupt();
                    }
                },
                function () use ($consumer) {
                    $this->handleEmpty($this->queue, $consumer);
                }
            );
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }

    }

    /**
     * True if you need to break on next iteration. The interrupt() method will be called,
     * batch will be processed to the end and the handling would be stopped
     * @param NatsMessage $message
     * @param string $queue
     * @param Consumer $consumer
     * @return bool
     */
    public function interruptOn(NatsMessage $message, string $queue, Consumer $consumer): bool
    {
        return false;
    }

}