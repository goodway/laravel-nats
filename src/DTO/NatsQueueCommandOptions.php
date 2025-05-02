<?php

namespace Goodway\LaravelNats\DTO;

use Illuminate\Support\Collection;

final class NatsQueueCommandOptions
{
    public function __construct(
        public ?string  $jetstream = null,
        public ?string  $consumer = null,
        public ?int     $batchSize = null,
    ) {}


    public function toArray(): array
    {
        return [
            'jetstream' => $this->jetstream,
            'consumer' => $this->consumer,
            'batchSize' => $this->batchSize,
        ];
    }

    /**
     * Parse options from command
     * @return self
     */
    public static function parseFromCommand(): self
    {
        $jetstream = $consumer = $batchSize = null;

        $args = request()->server('argv');
        if ($args) {
            $arguments = collect($args);
            $jetstream = self::getJetstreamOption($arguments);
            $consumer = self::getConsumerOption($arguments);
            $batchSize = self::getBatchSizeOption($arguments);
        }

        return new self(
            jetstream: $jetstream,
            consumer: $consumer,
            batchSize: $batchSize,
        );
    }

    private static function getConsumerOption(Collection $arguments): ?string
    {
        $option = $arguments->first(fn($arg) => str_starts_with($arg, '--consumer='));
        return $option ? self::getOptionValue($option) : null;
    }

    private static function getJetstreamOption(Collection $arguments): ?string
    {
        $option = $arguments->first(fn($arg) => str_starts_with($arg, '--jetstream='));
        return $option ? self::getOptionValue($option) : null;
    }

    private static function getBatchSizeOption(Collection $arguments): ?int
    {
        $option = $arguments->first(fn($arg) => str_starts_with($arg, '--batch='));
        return $option ? (int) self::getOptionValue($option) : null;
    }


    private static function getOptionValue(string $option): string
    {
        return explode('=', $option)[1];
    }
}