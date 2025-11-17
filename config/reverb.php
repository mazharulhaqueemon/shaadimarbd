<?php

return [
    'default' => env('REVERB_SERVER', 'reverb'),

    'servers' => [
    'reverb' => [
        'host' => '0.0.0.0',
        'port' => 6001, // Hardcoded
        'path' => '/ws',
        'hostname' => 'api.shaadimartbd.com',
        'options' => [
            'tls' => [],
        ],
        'max_request_size' => 10000,
        'scaling' => [
            'enabled' => false,
            'channel' => 'reverb',
            'server' => [
                'host' => '127.0.0.1',
                'port' => '6379',
                'database' => '0',
            ],
        ],
        'pulse_ingest_interval' => 15,
        'telescope_ingest_interval' => 15,
    ],
],

    'apps' => [
        'provider' => 'config',
        'apps' => [
            [
                'key' => env('REVERB_APP_KEY'),
                'secret' => env('REVERB_APP_SECRET'),
                'app_id' => env('REVERB_APP_ID'),
                'options' => [
                    'host' => env('REVERB_HOST', 'api.shaadimartbd.com'),
                    'port' => env('REVERB_PORT', 443), // ✅ Frontend connects to 443
                    'scheme' => env('REVERB_SCHEME', 'https'), // ✅ Use HTTPS
                    'useTLS' => env('REVERB_SCHEME', 'https') === 'https', // ✅ Enable TLS
                ],
                'allowed_origins' => ['*'],
                'ping_interval' => env('REVERB_APP_PING_INTERVAL', 60),
                'activity_timeout' => env('REVERB_APP_ACTIVITY_TIMEOUT', 30),
                'max_connections' => env('REVERB_APP_MAX_CONNECTIONS'),
                'max_message_size' => env('REVERB_APP_MAX_MESSAGE_SIZE', 10_000),
            ],
        ],
    ],
];