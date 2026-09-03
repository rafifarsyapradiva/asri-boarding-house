<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$html = app()->make(App\Http\Controllers\Public\LandingController::class)->index()->render();

echo "=== VERIFIKASI KONTEN LANDING PAGE ===" . PHP_EOL;
echo "1. Fasilitas (Air Conditioner): " . (str_contains($html, 'Air Conditioner') ? 'OK (Ditemukan)' : 'GAGAL') . PHP_EOL;
echo "2. Tipe Kamar (Nomor Kamar 101): " . (str_contains($html, '101') ? 'OK (Ditemukan)' : 'GAGAL') . PHP_EOL;
echo "3. Testimoni (Rian Hidayat): " . (str_contains($html, 'Rian Hidayat') ? 'OK (Ditemukan)' : 'GAGAL') . PHP_EOL;
echo "4. FAQ (Bagaimana cara memesan): " . (str_contains($html, 'Bagaimana cara memesan kamar') ? 'OK (Ditemukan)' : 'GAGAL') . PHP_EOL;
echo "5. Lokasi Maps (google.com/maps/embed): " . (str_contains($html, 'google.com/maps/embed') ? 'OK (Ditemukan)' : 'GAGAL') . PHP_EOL;
echo "======================================" . PHP_EOL;
