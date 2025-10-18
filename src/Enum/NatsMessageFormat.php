<?php

namespace Goodway\LaravelNats\Enum;

use Goodway\LaravelNats\DTO\NatsMessage;
use Goodway\LaravelNats\Helpers\StringHelper;

enum NatsMessageFormat:string
{
    use StringHelper;

    case SERIALIZED = 'serialized';
    case JSON = 'json';
    case PLAIN_STRING = 'plain_string';
    case OBJECT = 'object';
    case OBJECT_ORIGIN = 'object_origin';
    case ARRAY = 'array';
    case UNKNOWN = 'unknown';


    /**
     * Get a correct enum type based on the data from the NATS message
     */
    public static function fromMessage(mixed $data): self
    {
        if ($data instanceof NatsMessage) {
            return self::OBJECT_ORIGIN;
        }

        if (is_object($data)) {
            return self::OBJECT;
        }

        if (is_array($data)) {
            return self::ARRAY;
        }

        if (!is_string($data)) {
            return self::UNKNOWN;
        }

        if (self::isSerialized($data)) {
            return self::SERIALIZED;
        }

        if (self::isJson($data)) {
            return self::JSON;
        }

        return self::PLAIN_STRING;
    }

    public function isStructuredString(): bool
    {
        return in_array($this, [self::SERIALIZED, self::JSON]);
    }

    public function isSerializedString(): bool
    {
        return $this === self::SERIALIZED;
    }

    public function isJsonString(): bool
    {
        return $this === self::JSON;
    }

    public function isPlainString(): bool
    {
        return $this === self::PLAIN_STRING;
    }

    public function isUnknown(): bool
    {
        return $this === self::UNKNOWN;
    }

    public function isArray(): bool
    {
        return $this === self::ARRAY;
    }

    public function isObject(): bool
    {
        return $this === self::OBJECT;
    }

    public function isObjectOrigin(): bool
    {
        return $this === self::OBJECT_ORIGIN;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
