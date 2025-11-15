<?php

return [

    'paths' => ['api/*', 'login', 'signup', 'user/*','broadcasting/auth',
        'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://www.shaadimartbd.com',
        'https://shaadimartbd.com',
        'https://api.shaadimartbd.com',  # Your API domain
        'http://localhost:3000',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
