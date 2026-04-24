<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/health', 'GET');
$response = $kernel->handle($request);
http_response_code($response->getStatusCode());
header('Content-Type: application/json');
echo $response->getContent();
