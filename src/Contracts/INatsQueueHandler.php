<?php

namespace Goodway\LaravelNats\Contracts;

use Basis\Nats\Consumer\Consumer;

interface INatsQueueHandler
{
    public function handle($message, string $queue, Consumer $consumer);
    public function handleEmpty(string $queue, Consumer $consumer);
    public function setBatchSize(int $batchSize): static;
    public function setIterations(int $iterations): static;
    public function allowToCreateConsumer(bool $state): static;
    public function canCreateConsumer(): bool;
    public function withEvent(bool $state): static;
    public function pop();
    public function interruptOn($message, string $queue, Consumer $consumer): bool;

}