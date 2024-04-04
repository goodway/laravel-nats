<?php

namespace Goodway\LaravelNats\Contracts;

use Basis\Nats\Message\Payload;

interface INatsMessageJob
{
    public function body(): mixed;
    public function headers(): array;
    public function setSubject(string $subject);
    public function getTimestamp(): int;
    public function handle(): Payload;

}