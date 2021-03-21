<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read https://symfony.com/doc/current/setup.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'], true) || PHP_SAPI === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require __DIR__.'/../vendor/autoload.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

$response->headers->addCacheControlDirective('private', true);
$response->headers->addCacheControlDirective('no-cache', true);
$response->headers->addCacheControlDirective('no-store', true);
$response->headers->addCacheControlDirective('must-revalidate', true);
$response->headers->add(array(
    'X-Frame-Options' => 'deny',
    'X-Content-Type-Options' => 'nosniff',
    'Strict-Transport-Security' => 'max-age=15552000; includeSubDomains; preload',
    'Content-Security-Policy' => "" .
        "default-src 'self' ambiguss.fr ambiguss.test;" .
        "script-src 'self' 'unsafe-inline' *.google.com *.gstatic.com *.facebook.net *.twitter.com;" .
        "style-src 'self' 'unsafe-inline' *.googleapis.com;" .
        "img-src 'self' *.twitter.com;" .
        "font-src 'self' *.googleapis.com *.gstatic.com;" .
        "frame-src *.google.com *.facebook.com *.twitter.com;" .
        "block-all-mixed-content;" .
        "upgrade-insecure-requests;",
    'Pragma' => 'no-cache'
));

$response->send();
$kernel->terminate($request, $response);
