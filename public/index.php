<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

// Populate $_SERVER from real env vars (PHP built-in server doesn't do this)
$_SERVER['APP_ENV'] = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: ($_SERVER['APP_ENV'] === 'dev' ? '1' : '0');

// Load .env file if it exists (overrides above defaults with file values)
$envFile = dirname(__DIR__).'/.env';
if (is_file($envFile)) {
    (new Dotenv())->usePutenv()->bootEnv($envFile);
} elseif (is_file($envFile.'.test')) {
    (new Dotenv())->usePutenv()->loadEnv($envFile.'.test', 'APP_ENV', 'dev');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
