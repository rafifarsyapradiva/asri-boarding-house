<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tagihan = App\Models\Tagihan::find(2);

// User auth
$user = App\Models\User::find($tagihan->penyewa->user_id);
Auth::login($user);

// Simulate the request WITHOUT middleware checking CSRF for this test
$request = Illuminate\Http\Request::create(route('penyewa.pembayaran.token', $tagihan), 'POST');
$request->headers->set('Accept', 'application/json');

// Get the controller and call generate directly to bypass middleware
$controller = app(App\Http\Controllers\Api\SnapTokenController::class);
$response = $controller->generate($tagihan);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
