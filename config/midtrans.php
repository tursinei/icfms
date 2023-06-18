<?php

return [
    'merchant_id'   => env('MIDTRANST_MERCHANT_ID'),
    'client_key'    => env('MIDTRANST_CLIENT_KEY'),
    'server_key'    => env('MIDTRANST_SERVER_KEY'),
    'is_production' => env('MIDTRANST_IS_PRODUCTION', false),
    'is_sanitized'  => false,
    'is_3ds'        => false,
];
