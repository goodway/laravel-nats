<?php

namespace Goodway\LaravelNats\Helpers;

trait ArrayHelper
{
    public static function transformKeys(array|object $arrOrObject, callable $func): array
    {
        $arr = [];
        foreach ($arrOrObject as $key => $value) {
            $arr[$func($key)] = $value;
        }

        return $arr;
    }
}
