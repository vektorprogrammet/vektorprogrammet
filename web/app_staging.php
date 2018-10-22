<?php

use Symfony\Component\HttpFoundation\Request;

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if ( isset( $_SERVER['HTTP_CLIENT_IP'] )
     || isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )
     || ! ( in_array( @$_SERVER['REMOTE_ADDR'], ['127.0.0.1','::1'], true )
            || in_array( @$_SERVER['SERVER_ADDR'], [ '82.196.15.63' ], true )
            || PHP_SAPI === 'cli-server' )
) {
	header( 'HTTP/1.0 403 Forbidden' );
	exit( 'You are not allowed to access this file. Check ' . basename( __FILE__ ) . ' for more information.' );
}

require __DIR__ . '/../vendor/autoload.php';

$kernel = new AppKernel( 'staging', false );
$kernel->loadClassCache();

// If you use HTTP Cache and your application relies on the _method request parameter
// to get the intended HTTP method, uncomment this line.
// See http://symfony.com/doc/current/reference/configuration/framework.html#http-method-override
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
