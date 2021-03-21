<?php

use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$kernel = new AppKernel('prod', false);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
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
