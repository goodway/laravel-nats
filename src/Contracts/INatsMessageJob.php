<?php

namespace Goodway\LaravelNats\Contracts;

use Goodway\LaravelNats\DTO\NatsMessage;

interface INatsMessageJob
{
    public function body(): string;
    public function headers(): array;
    public function setSubject(string $subject);
    public function getTimestamp(): int;
    public function handle(): NatsMessage;

}