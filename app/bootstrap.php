<?php

use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

define('__APPDIR__', realpath(sprintf('%s/..', __DIR__)));

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__APPDIR__ . '/app/config.php');
$container = $containerBuilder->build();

// Start routing.
$router = new \LibraryCatalog\Service\Http\Router(
    $container,
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD'],
    $_SERVER
);
$response = $router->dispatch();

// Show response.
if ($response) {
    foreach ($container->get('HttpTransformer')->getResponseHeaders() as $name => $value) {
        $response = $response->withHeader($name, $value);
    }
    (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
}
