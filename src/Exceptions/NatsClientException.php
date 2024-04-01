<?php

namespace Goodway\LaravelNats\Exceptions;

use Exception;

class NatsClientException extends Exception
{
    public function __construct(string $message, int $code = 400, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
