<?php

return [
    'driver' => 'nextpay', // or jibit

    'nextpay' => [
        'token' => env('NEXTPAY_TOKEN', '')
    ],
    'jibit' => [
        'access_token' => env('JIBIT_ACCESS_TOKEN', ''),
        'refresh_token' => env('JIBIT_REFRESH_TOKEN', ''),
        'api_key' => env('JIBIT_API_KEY', ''),
        'secret_key' => env('JIBIT_SECRET_KEY', ''),
    ]
];
