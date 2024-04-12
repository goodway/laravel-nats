<?php

namespace Goodway\LaravelNats\DTO;

use Goodway\LaravelNats\Exceptions\NatsMessageException;
use Goodway\LaravelNats\NatsMessageJobBase;

final class NatsMessage
{
    public function __construct(
        public string    $body = '', // serialized body
        public array     $headers = [],
        public string    $subject = 'default',
        public ?int      $timestamp = null,
    ) {}

    public function body(): string
    {
        return $this->body;
    }

    public function headers(): array
    {
        return $this->headers;
    }
    public function subject(): ?string
    {
        return $this->subject;
    }

    public function timestamp(): ?int
    {
        return $this->timestamp;
    }

    public function hasHeader(string $key): bool
    {
        return array_key_exists($key, $this->headers);
    }

    public function hasHeaders(): bool
    {
        return count($this->headers) > 0;
    }

    public function getHeader(string $key)
    {
        return $this->hasHeader($key) ? $this->headers[$key] : null;
    }

    public function isEmpty(): bool
    {
        return $this->body == '';
    }

    public function __toString(): string
    {
        return $this->body;
    }

    public function toArray(): array
    {
        return [
            'body' => $this->body,
            'headers' => $this->headers,
            'subject' => $this->subject,
            'timestamp' => $this->timestamp,
        ];
    }

    /**
     * Creates an object from nats payload
     * @param array|object|string $payload
     * @param bool $deserialize
     * @return NatsMessage
     */
    public static function parse(array|object|string $payload, bool $deserialize = false): NatsMessage
    {
        if (is_string($payload)) {
            if (!$deserialize) {
                return new self ($payload);
            }
            $payload = unserialize($payload);

            if ($payload instanceof self) {
                return $payload;
            }
        }
        if (is_array($payload)) {
            $payload = (object)$payload;
        }

        return new self (
            $payload->body && is_string($payload->body) ? $payload->body : '',
            $payload->headers && is_array($payload->headers) ? $payload->headers : [],
            $payload->subject && is_string($payload->subject) ? $payload->subject : 'default',
            $payload->timestamp && is_int($payload->timestamp) ? $payload->timestamp : null,
        );
    }

    /**
     * To broadcast current message to certain connection and queue
     * @param string $queueConnection
     * @param string|null $queue
     * @throws NatsMessageException
     */
    public function broadcast(string $queueConnection, ?string $queue = null): void
    {
        $body = unserialize($this->body);
        if (!$body) {
            throw new NatsMessageException('Can not deserialize message body to broadcast');
        }
        $job = new NatsMessageJobBase($body, $this->headers(), $this->subject());

        dispatch($job)
            ->onConnection($queueConnection)
            ->onQueue($queue ?: $this->subject())
        ;
    }


    public function render(): string
    {
        if (count($this->headers)) {
            $headers = "NATS/1.0\r\n";
            foreach ($this->headers as $k => $v) {
                $headers .= "$k: $v\r\n";
            }
            $headers .= "\r\n";

            $crc = strlen($headers) . ' ' . strlen($headers . $this->body);

            return $crc . "\r\n" . $headers . $this->body;
        }

        return strlen($this->body) . "\r\n" . $this->body;
    }

}