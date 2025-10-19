<?php

namespace Goodway\LaravelNats\DTO;

use Goodway\LaravelNats\Enum\NatsMessageFormat;
use Goodway\LaravelNats\Helpers\StringHelper;
use Goodway\LaravelNats\NatsMessageJobBase;

final class NatsMessage
{
    use StringHelper;

    public function __construct(
        public string    $body = '',
        public array     $headers = [],
        public ?string   $jetstream = null,
        public ?string   $subject = null,
        public ?int      $timestamp = null,
    ) {}

    public function __serialize(): array
    {
        return [
            'body' => $this->body,
            'headers' => $this->headers,
            'jetstream' => $this->jetstream,
            'subject' => $this->subject,
            'timestamp' => $this->timestamp,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->body = $data['body'];
        $this->headers = $data['headers'];
        $this->jetstream = $data['jetstream'];
        $this->subject = $data['subject'];
        $this->timestamp = $data['timestamp'];
    }


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

    public function jetstream(): ?string
    {
        return $this->jetstream;
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
            'jetstream' => $this->jetstream,
            'subject' => $this->subject,
            'timestamp' => $this->timestamp,
        ];
    }

    /**
     * Creates an object from nats payload
     * @param mixed $payload
     * @return NatsMessage
     */
    public static function parse(mixed $payload): NatsMessage
    {
        /** Protect from double-serialized messages */
        $payload = is_string($payload) && self::isSerialized($payload) ? unserialize($payload) : $payload;

        $format = NatsMessageFormat::fromMessage($payload);

        return match(true) {
            $format->isObjectOrigin() => $payload, // returns origin dto object
            $format->isObject() => self::parseFromObject($payload),
            $format->isArray() => self::parseFromObject((object)$payload),
            $format->isJsonString() => self::parseFromObject((object)json_decode($payload, true)),
            $format->isPlainString() => new self ($payload),
            $format->isUnknown() => new self ('Unknown message format'),
        };
    }

    /**
     * Parse from simple object
     * @param object $payload
     * @return NatsMessage
     */
    public static function parseFromObject(object $payload): NatsMessage
    {
        return new self (
            isset($payload->body) && is_string($payload->body) ? $payload->body : '',
            isset($payload->headers) && is_array($payload->headers) ? $payload->headers : [],
            isset($payload->jetstream) && is_string($payload->jetstream) ? $payload->jetstream : null,
            isset($payload->subject) && is_string($payload->subject) ? $payload->subject : null,
            isset($payload->timestamp) && is_int($payload->timestamp) ? $payload->timestamp : null,
        );
    }

    public function setHeaders(array $headers): NatsMessage
    {
        $this->headers = $headers;
        return $this;
    }

    public function putHeaders(array $headers): NatsMessage
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function setJetstream(string $jetstream): NatsMessage
    {
        $this->jetstream = $jetstream;
        return $this;
    }

    public function setSubject(string $subject): NatsMessage
    {
        $this->subject = $subject;
        return $this;
    }

    public function setTimestamp(int $timestamp): NatsMessage
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * Sets the timestamp if it is not set
     * @return NatsMessage
     */
    public function setTimestampIfNull(): NatsMessage
    {
        if (is_null($this->timestamp)) {
            $this->timestamp = now()->getTimestampMs();
        }
        return $this;
    }

    /**
     * To broadcast current message to certain connection and queue
     * @param string $queueConnection
     * @param string|null $queue
     */
    public function broadcast(string $queueConnection, ?string $queue = null): void
    {
        $job = new NatsMessageJobBase($this->body(), $this->headers(), $this->subject());

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