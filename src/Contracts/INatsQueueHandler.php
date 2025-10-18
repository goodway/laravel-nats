<?php

namespace Goodway\LaravelNats\Contracts;

use Basis\Nats\Consumer\Consumer;
use Goodway\LaravelNats\DTO\NatsMessage;

interface INatsQueueHandler
{
    public function handle(NatsMessage $message, string $jetstream, string $queue, Consumer $consumer);
    public function handleEmpty(string $queue, Consumer $consumer);
    public function setBatchSize(int $batchSize): static;
    public function setIterations(int $iterations): static;
    public function setDelay(float $delay): static;
    public function allowToCreateConsumer(bool $state): static;
    public function canCreateConsumer(): bool;
    public function withEvent(bool $state): static;
    public function pop();
    public function interruptOn(NatsMessage $message, string $queue, Consumer $consumer): bool;

}