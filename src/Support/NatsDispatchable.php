<?php

namespace Goodway\LaravelNats\Support;

use Goodway\LaravelNats\Contracts\INatsMessageJob;
use Illuminate\Support\Fluent;
use Closure;

/**
 * @property INatsMessageJob $job
 */
trait NatsDispatchable
{

    /**
     * @param ...$arguments
     * @return NatsPendingDispatch
     */
    public static function dispatchNats(...$arguments): NatsPendingDispatch
    {
        return new NatsPendingDispatch(new static(...$arguments));
    }

    /**
     * Dispatch the job with the given arguments if the given truth test passes.
     *
     * @param  bool|Closure  $boolean
     * @param  mixed  ...$arguments
     * @return NatsPendingDispatch|Fluent
     */
    public static function dispatchNatsIf($boolean, ...$arguments)
    {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return value($boolean, $dispatchable)
                ? new NatsPendingDispatch($dispatchable)
                : new Fluent;
        }

        return value($boolean)
            ? new NatsPendingDispatch(new static(...$arguments))
            : new Fluent;
    }

    /**
     * Dispatch the job with the given arguments unless the given truth test passes.
     *
     * @param  bool|Closure  $boolean
     * @param  mixed  ...$arguments
     * @return NatsPendingDispatch|Fluent
     */
    public static function dispatchNatsUnless($boolean, ...$arguments): NatsPendingDispatch|Fluent
    {
        if ($boolean instanceof Closure) {
            $dispatchable = new static(...$arguments);

            return ! value($boolean, $dispatchable)
                ? new NatsPendingDispatch($dispatchable)
                : new Fluent;
        }

        return ! value($boolean)
            ? new NatsPendingDispatch(new static(...$arguments))
            : new Fluent;
    }

}