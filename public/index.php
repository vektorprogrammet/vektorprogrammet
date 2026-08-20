<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

// Load .env file if it exists
$envFile = dirname(__DIR__).'/.env';
if (is_file($envFile)) {
    (new Dotenv())->usePutenv()->bootEnv($envFile);
} elseif (is_file($envFile.'.test')) {
    (new Dotenv())->usePutenv()->loadEnv($envFile.'.test', 'APP_ENV', 'dev');
}

$resolveEnvironmentVariable = static function (string $name, string $default): string {
    foreach ([$_SERVER[$name] ?? null, $_ENV[$name] ?? null, getenv($name)] as $value) {
        if (is_string($value) && '' !== trim($value)) {
            return $value;
        }
    }

    return $default;
};

$_SERVER['APP_ENV'] = $resolveEnvironmentVariable('APP_ENV', 'dev');
$_SERVER['APP_DEBUG'] = $resolveEnvironmentVariable('APP_DEBUG', '1');

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
