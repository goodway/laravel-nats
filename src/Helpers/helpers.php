<?php

use Goodway\LaravelNats\Contracts\INatsMessageJob;
use Goodway\LaravelNats\Support\NatsPendingDispatch;

if (! function_exists('dispatchNats')) {
    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param INatsMessageJob $job
     * @return NatsPendingDispatch
     */
    function dispatchNats(INatsMessageJob $job): NatsPendingDispatch
    {
        return new NatsPendingDispatch($job);
    }
}