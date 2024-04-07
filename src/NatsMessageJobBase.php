<?php

namespace Goodway\LaravelNats;

class NatsMessageJobBase extends NatsMessageJob
{

    public function __construct(
        private readonly mixed $body,
        private readonly array $headers = [],
        protected string $subject = 'default',
    ) {}

    public function body(): mixed
    {
        return $this->body;
    }

    public function headers(): array
    {
        return $this->headers;
    }
}