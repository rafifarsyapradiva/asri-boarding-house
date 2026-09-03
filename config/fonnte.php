<?php

return [
    'token' => env('FONNTE_TOKEN'),
    'url' => env('FONNTE_URL', 'https://api.fonnte.com/send'),
    'timeout' => (int) env('FONNTE_TIMEOUT', 15),
    'connect_timeout' => (int) env('FONNTE_CONNECT_TIMEOUT', 5),
];
