<?php

namespace Goodway\LaravelNats\Helpers;

trait StringHelper
{
    public static function isSerialized(string $data): bool
    {
        return @unserialize($data) !== false || $data === 'b:0;';
    }
}