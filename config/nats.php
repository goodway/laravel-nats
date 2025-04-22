<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nats client configurations
    |--------------------------------------------------------------------------
    */
    'client' => [
        'configurations' => [
            'default' => [
                'host' => env('NATS_HOST', 'localhost'),
                'port' => intval(env('NATS_PORT', 4222)),
                'user' => env('NATS_USER'),
                'password' => env('NATS_PASSWORD'),
                'token' => env('NATS_TOKEN'), // Sets an authorization token for a connection
                'nkey' => env('NATS_NKEY'), // new, highly secure public-key signature system based on Ed25519
                'jwt' => env('NATS_JWT'), // Token for JWT Authentication
                'reconnect' => env('NATS_RECONNECT', true),
                'connection_timeout' => floatval(env('NATS_CONNECTION_TIMEOUT', 1)), // Number of seconds the client will wait for a connection to be established
                'verbose_mode' => env('NATS_VERBOSE_MODE', false), // Turns on +OK protocol acknowledgements
                'inbox_prefix' => env('NATS_INBOX_PREFIX', '_INBOX'), // Sets default prefix for automatically created inboxes
                'ping_interval' => intval(env('NATS_PING_INTERVAL', 2)), // Number of seconds between client-sent pings
                'ssl_key' => env('NATS_SSL_KEY_FILE'),
                'ssl_cert' => env('NATS_SSL_CERT_FILE'),
                'ssl_ca' => env('NATS_SSL_CA_FILE'),
            ],
            /*
            |--------------------------------------------------------------------------
            | Many configurations example
            |--------------------------------------------------------------------------
            */

//            'additional_configuration' => [
//                'host' => env('NATS_S2_HOST', 'localhost'),
//                'port' => intval(env('NATS_S2_PORT', 4222)),
//                'user' => env('NATS_S2_USER'),
//                ...
//            ],
//            'queue_consumer' => [
//                'host' => env('NATS_CON_HOST', 'localhost'),
//                'port' => intval(env('NATS_CON_PORT', 4222)),
//                'user' => env('NATS_CON_USER'),
//                ...
//            ],
//            'queue_publisher' => [
//                'host' => env('NATS_PUB_HOST', 'localhost'),
//                'port' => intval(env('NATS_PUB_PORT', 4222)),
//                'user' => env('NATS_PUB_USER'),
//                ...
//            ],
        ],
    ],


];
