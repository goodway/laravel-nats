<?php

namespace Goodway\LaravelNats\Contracts;

use Goodway\LaravelNats\DTO\NatsMessage;
use Goodway\LaravelNats\Enum\NatsMessageFormat;

interface INatsMessageJob
{
    public function body(): string;
    public function headers(): array;
    public function handle(): NatsMessage;
    public function setSubject(string $subject): INatsMessageJob;
    public function setJetstream(string $jetstream): INatsMessageJob;
    public function setWithEvents(bool $withEvents): INatsMessageJob;
    public function setSendFormat(NatsMessageFormat $format): INatsMessageJob;
    public function getSubject(): ?string;
    public function getJetstream(): ?string;
    public function getWithEvents(): ?bool;
    public function getSendFormat(): NatsMessageFormat;
    public function getTimestamp(): int;

}