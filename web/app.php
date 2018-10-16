<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();

// If you use HTTP Cache and your application relies on the _method request parameter
// to get the intended HTTP method, uncomment this line.
// See http://symfony.com/doc/current/reference/configuration/framework.html#http-method-override
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
