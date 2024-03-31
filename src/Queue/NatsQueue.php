<?php

namespace Goodway\LaravelNats\Queue;

use Goodway\LaravelNats\Queue\Handlers\NatsQueueHandler;
use Basis\Nats\Client as NatsClient;
use Basis\Nats\Stream\RetentionPolicy;
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
        protected string     $consumerPrefix = 'con_',
        protected int        $consumerIterations = 3,
        protected int        $batchSize = 10
    )
    {}


    public function size($queue = null)
    {
        // TODO: Implement size() method.
    }

    public function push($job, $data = '', $queue = null)
    {
        $stream = $this->clientPub->getApi()->getStream($this->jetStream);

        $jobData = $job->handle();
        $jobData = !is_string($jobData) ? serialize($jobData) : $jobData;

        var_dump($jobData);
        $stream->create()->put($queue, $jobData);
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        // TODO: Implement pushRaw() method.
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        // TODO: Implement later() method.
    }

    public function getConsumerName(string $queue): string
    {
        $validate = $this->jetStreamRetentionPolicy === RetentionPolicy::WORK_QUEUE ?
            $this->consumerGroup : $this->consumerPrefix . '_' . $this->consumerGroup . '_' . $queue;
        return preg_replace(
            '~[\\\\/:*?"<>|+-.]~', '', $validate);
    }

    /**
     * SUB jetstream
     * @param $queue
     * @return void
     */
    public function pop($queue = null)
    {
        try {

            $stream = $this->clientSub->getApi()->getStream($this->jetStream);
            $consumer = $stream->getConsumer($this->getConsumerName($queue));

            /**
             * Sets:
             * how many messages would be requested from nats stream
             * how many times message request should be sent
             *
             */
            $consumer->setBatching($this->batchSize) // how many messages would be requested from nats stream
                ->setIterations($this->consumerIterations) // how many times message request should be sent
//                ->setExpires(10)
                ->handle(
                    function ($msg) use ($queue) {
                        new NatsQueueHandler($msg, $queue);
                        // if you need to break on next iteration simply call interrupt method
                        // batch will be processed to the end and the handling would be stopped
//                        $consumer->interrupt();
                    },
                    function () { var_dump(".."); }
            );
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }

    }
}
