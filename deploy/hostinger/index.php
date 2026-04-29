<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Resolve Laravel Base Path
|--------------------------------------------------------------------------
|
| Shared hostingte app klasoru degisebilir.
| Once LARAVEL_BASE_PATH, sonra varsayilan laravel klasoru denenir.
|
*/
$candidates = array_filter([
    getenv('LARAVEL_BASE_PATH') ?: null,
    __DIR__.'/../laravel',
]);

$basePath = null;

foreach ($candidates as $candidate) {
    $resolved = realpath($candidate);

    if ($resolved
        && file_exists($resolved.'/bootstrap/app.php')
        && file_exists($resolved.'/vendor/autoload.php')) {
        $basePath = $resolved;
        break;
    }
}

if ($basePath === null) {
    http_response_code(500);
    exit('Laravel base path could not be resolved. Check public_html/index.php settings.');
}

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
