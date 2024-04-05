<?php

namespace Goodway\LaravelNats\Queue;

use Basis\Nats\Client as NatsClient;
use Basis\Nats\Stream\RetentionPolicy;
use Goodway\LaravelNats\Contracts\INatsQueueHandler;
use Goodway\LaravelNats\Events\NatsQueueMessageSent;
use Goodway\LaravelNats\Exceptions\NatsJetstreamException;
use Goodway\LaravelNats\Queue\Handlers\NatsQueueHandlerDefault;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;

class NatsQueue extends Queue implements QueueContract
{


    public function __construct(
        protected NatsClient $clientSub,
        protected NatsClient $clientPub,
        protected string     $consumerGroup,
        protected string     $jetStream,
        protected string     $jetStreamRetentionPolicy = 'workqueue',
        protected ?bool      $consumerCreate = null,
        protected string     $consumerPrefix = 'con',
        protected int        $consumerIterations = 0,
        protected int        $batchSize = 0,
        protected ?bool      $fireEvents = null,
        protected string     $queueHandler = NatsQueueHandlerDefault::class // you can put your custom queue handler
    ) {}


    /**
     * Generates consumer name based on prefix, group and queue
     * @param string $queue
     * @return string
     */
    public function getConsumerName(string $queue): string
    {
        $validate = $this->jetStreamRetentionPolicy === RetentionPolicy::WORK_QUEUE ?
            $this->consumerGroup : ($this->consumerPrefix ? $this->consumerPrefix . '_' : '') . $this->consumerGroup . '_' . $queue;
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

        if (!$stream->exists()) {
            throw new NatsJetstreamException('Jetstream ' . $this->jetStream . ' not found', 404);
        }

        $jobData = $job->handle();
        $jobData = !is_string($jobData) ? serialize($jobData) : $jobData;

        $stream->put($queue, $jobData);

        event(new NatsQueueMessageSent($queue, $jobData));
        var_dump($jobData);
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        // TODO: Implement pushRaw() method.
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.
    }

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
        if (!is_null($this->consumerCreate)) {
            $handler->allowToCreateConsumer($this->consumerCreate);
        }
        if (!is_null($this->fireEvents)) {
            $handler->withEvent($this->fireEvents);
        }
    }
}
