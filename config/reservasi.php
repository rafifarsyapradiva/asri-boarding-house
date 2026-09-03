<?php

return [
    'dp_percentage' => (float) env('RESERVASI_DP_PERCENTAGE', 0.30),
    'min_durasi' => [
        'harian' => 1,
        'mingguan' => 1,
        'bulanan' => 1,
    ],
    'max_durasi' => [
        'harian' => 30,
        'mingguan' => 8,
        'bulanan' => 12,
    ],
    'admin_wa' => env('ADMIN_WA_NUMBER', '62895330031313'),
    'expire_hours' => (int) env('RESERVASI_EXPIRE_HOURS', 24),
    'fallback_images' => [
        'vip' => env('FALLBACK_IMG_VIP', 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=600&q=80'),
        'deluxe' => env('FALLBACK_IMG_DELUXE', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80'),
        'standar' => env('FALLBACK_IMG_STANDAR', 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=600&q=80'),
    ],
];
