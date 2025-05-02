<?php

namespace Goodway\LaravelNats\Queue;

use Basis\Nats\Client as NatsClient;
use Goodway\LaravelNats\Contracts\INatsQueueHandler;
use Goodway\LaravelNats\Enum\RetentionPolicy;
use Goodway\LaravelNats\Events\NatsQueueMessageSent;
use Goodway\LaravelNats\Exceptions\NatsJetstreamException;
use Goodway\LaravelNats\Queue\Handlers\NatsQueueHandlerDefault;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;

class NatsQueue extends Queue implements QueueContract
{

    /** To use concrete durable consumer name instead of queue name
     * @var string|null
     */
    protected ?string $consumer = null;


    public function __construct(
        protected NatsClient                $clientSub,
        protected NatsClient                $clientPub,
        protected string                    $consumerGroup,
        protected string                    $jetStream,
        protected string|RetentionPolicy    $jetStreamRetentionPolicy = RetentionPolicy::WORK_QUEUE,
        protected ?bool                     $consumerCreate = null,
        protected string                    $consumerPrefix = 'con',
        protected int                       $consumerIterations = 0,
        protected float                     $consumerDelay = 0,
        protected int                       $batchSize = 0,
        protected ?bool                     $fireEvents = null,
        protected string                    $queueHandler = NatsQueueHandlerDefault::class, // you can put your custom queue handler
        protected bool                      $verbose = false,
        protected bool                      $checkJetstreamOnPublish = true
    ) {}


    public function setJetstream(string $jetstream): static
    {
        $this->jetStream = $jetstream;
        return $this;
    }

    public function setConsumer(string $consumer): static
    {
        $this->consumer = $consumer;
        return $this;
    }

    public function setBatchSize(int $batchSize): static
    {
        $this->batchSize = $batchSize;
        return $this;
    }


    /**
     * Generates consumer name based on prefix, group and queue
     * @param string $queue
     * @return string
     * @throws NatsJetstreamException
     */
    public function getConsumerName(string $queue): string
    {
        if ($this->consumer) {
            return $this->consumer;
        }

        /** If consumer is not set generate consumer name based on queue */

        $policy = is_string($this->jetStreamRetentionPolicy) ?
            RetentionPolicy::tryFrom($this->jetStreamRetentionPolicy) : $this->jetStreamRetentionPolicy
        ;
        if (!$policy) {
            throw new NatsJetstreamException('Invalid jetStream retention policy', 422);
        }

        $validate = $policy->isWorkQueue() ?
            $this->consumerGroup : ($this->consumerPrefix ? $this->consumerPrefix . '_' : '') . ($this->consumerGroup ? $this->consumerGroup . '_' : '') . $queue;

        return preg_replace(
            '~[\\\\/:*?"<>|+-.]~', '', $validate);
    }


    public function size($queue = null)
    {
        // TODO: Implement size() method.
    }

    /**
     * @throws NatsJetstreamException
     */
    public function push($job, $data = '', $queue = null)
    {
        $stream = $this->clientPub->getApi()->getStream($this->jetStream);

        if ($this->checkJetstreamOnPublish && !$stream->exists()) {
            throw new NatsJetstreamException('Jetstream ' . $this->jetStream . ' not found', 404);
        }

        $jobData = $job->handle();
        $serialized = !is_string($jobData) ? serialize($jobData) : $jobData;

        $stream->put($queue, $serialized);

        if ($this->fireEvents) {
            event(new NatsQueueMessageSent($queue, $jobData));
        }

        if ($this->verbose) {
            var_dump($jobData);
        }

    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        // TODO: Implement pushRaw() method.
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.
    }

    /**
     *
     * @throws NatsJetstreamException
     */
    public function pop($queue = null)
    {

        $consumerName = $this->getConsumerName($queue);

        /**
         * @var INatsQueueHandler $handler
         */
        $handler = new $this->queueHandler(
            $this->clientSub,
            $this->jetStream,
            $consumerName,
            $queue,
        );

        $this->overridesHandlerAttributes($handler);

        $handler->pop();

    }

    /**
     * Overrides handler attributes with values coming from the queue configuration
     * @param INatsQueueHandler $handler
     */
    private function overridesHandlerAttributes(INatsQueueHandler $handler): void
    {
        if ($this->batchSize) {
            $handler->setBatchSize($this->batchSize);
        }
        if ($this->consumerIterations) {
            $handler->setIterations($this->consumerIterations);
        }
        if ($this->consumerDelay) {
            $handler->setDelay($this->consumerDelay);
        }
        if (!is_null($this->consumerCreate)) {
            $handler->allowToCreateConsumer($this->consumerCreate);
        }
        if (!is_null($this->fireEvents)) {
            $handler->withEvent($this->fireEvents);
        }
    }
}
