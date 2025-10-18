<?php

namespace Goodway\LaravelNats\Helpers;

trait StringHelper
{
    public static function isSerialized(string $data): bool
    {
        return @unserialize($data) !== false || $data === 'b:0;';
    }

    public static function isJson(string $data): bool
    {
        json_decode($data);
        return (json_last_error() === JSON_ERROR_NONE);
    }


}