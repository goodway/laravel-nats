<?php

namespace Goodway\LaravelNats\Queue\Handlers;


use Illuminate\Support\Facades\Log;

class NatsQueueHandler
{
    /**
     * @param $message
     * @param string $topic
     */
    public function __construct($message, string $topic) {

        /**
         * Some handler code here
         */
        $obj1 = unserialize($message);

    }
}
