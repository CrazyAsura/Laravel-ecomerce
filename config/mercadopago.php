<?php

return [
    'access_token' => env('MP_ACCESS_TOKEN'),
    'public_key' => env('MP_PUBLIC_KEY'),
    'client_id' => env('MP_CLIENT_ID'),
    'client_secret' => env('MP_CLIENT_SECRET'),
    'sandbox' => env('MP_SANDBOX_MODE', true),
    'base_url' => env('MP_BASE_URL', 'https://api.mercadopago.com'),
];
