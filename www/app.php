<?php

use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();

// When using the HTTP Cache to improve application performance, the application
// kernel is wrapped by the AppCache class to activate the built-in reverse proxy.
// See http://symfony.com/doc/current/book/http_cache.html#symfony-reverse-proxy
$kernel = new AppCache($kernel);

// If you use HTTP Cache and your application relies on the _method request parameter
// to get the intended HTTP method, uncomment this line.
// See http://symfony.com/doc/current/reference/configuration/framework.html#http-method-override
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
