<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'base_url' => env(
        'MIDTRANS_BASE_URL',
        env('MIDTRANS_IS_PRODUCTION', false)
            ? 'https://app.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2'
    ),
    'snap_url' => env(
        'MIDTRANS_SNAP_URL',
        env('MIDTRANS_IS_PRODUCTION', false)
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js'
    ),
    'sync_fallback_enabled' => (bool) env('MIDTRANS_SYNC_FALLBACK_ENABLED', true),
];
