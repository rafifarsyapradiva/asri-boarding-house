<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

/**
 * Helper closure to handle fatal bootstrap and execution errors gracefully (DRY).
 *
 * @param string $message
 * @param \Throwable|null $exception
 * @return void
 */
$terminateWithError = static function (string $message, ?\Throwable $exception = null): void {
    if ($exception !== null) {
        error_log((string) $exception);
    }

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
    }

    echo $message;
    exit(1);
};

// Determine paths to critical files
$maintenancePath = __DIR__ . '/../storage/framework/maintenance.php';
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$bootstrapPath = __DIR__ . '/../bootstrap/app.php';

// Determine if the application is in maintenance mode...
if (file_exists($maintenancePath)) {
    require $maintenancePath;
}

// Register the Composer autoloader...
if (!file_exists($autoloadPath)) {
    $terminateWithError('Error: Composer autoloader not found. Please run "composer install".');
}
require $autoloadPath;

// Bootstrap Laravel and handle the request...
if (!file_exists($bootstrapPath)) {
    $terminateWithError('Error: Laravel bootstrap file not found. Please verify your installation.');
}

/** @var Application $app */
$app = require_once $bootstrapPath;

try {
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    $terminateWithError('Fatal Error: Failed to handle the incoming request.', $e);
}
