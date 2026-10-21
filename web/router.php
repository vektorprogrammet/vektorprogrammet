<?php

// Router for PHP's local development server: php -S 127.0.0.1:8000 -t web web/router.php
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$file = realpath(__DIR__.$path);
if ($file !== false && str_starts_with($file, __DIR__.DIRECTORY_SEPARATOR) && is_file($file)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/app_dev.php';
$_SERVER['PHP_SELF'] = '/app_dev.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/app_dev.php';
require __DIR__.'/app_dev.php';
