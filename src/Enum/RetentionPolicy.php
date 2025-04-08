<?php

namespace Goodway\LaravelNats\Enum;

enum RetentionPolicy:string
{
    case INTEREST = 'interest';
    case LIMITS = 'limits';
    case WORK_QUEUE = 'workqueue';


    public function isInterest(): bool
    {
        return $this === self::INTEREST;
    }

    public function isLimits(): bool
    {
        return $this === self::LIMITS;
    }

    public function isWorkQueue(): bool
    {
        return $this === self::WORK_QUEUE;
    }
}
