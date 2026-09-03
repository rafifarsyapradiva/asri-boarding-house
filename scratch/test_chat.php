<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Reservasi;
use App\Models\ChatMessage;

$reservasi = Reservasi::find(8);
if (!$reservasi) {
    echo "Reservasi 8 tidak ditemukan di database.\n";
    exit;
}

echo "--- DATA RESERVASI ID 8 ---\n";
echo "ID: " . $reservasi->id . "\n";
echo "Order ID: " . $reservasi->order_id . "\n";
echo "User ID: " . $reservasi->user_id . " (" . ($reservasi->user->nama ?? 'Unknown') . ")\n";
echo "Status: " . $reservasi->status . "\n";

$msgCount = ChatMessage::where('reservasi_id', 8)->count();
echo "Total Pesan Chat: " . $msgCount . "\n";
