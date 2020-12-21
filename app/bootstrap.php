<?php

use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

//define('__APPDIR__', realpath(sprintf('%s/..', __DIR__)));

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/config.php');
$container = $containerBuilder->build();

// For working with ENV https://github.com/vlucas/phpdotenv can be used in future.
//
/*$store   = new \Symfony\Component\Lock\Store\RedisStore(new \Predis\Client(getenv('APP_REDIS_PARAMS')));
$store   = new \Symfony\Component\Lock\Store\RetryTillSaveStore($store);
$factory = new \Symfony\Component\Lock\LockFactory($store);
$container->set('Locker', $factory);*/

// Handle JSONRPC request.
//(new Server(new Api($container)))->reply();

/** @var LibraryCatalog\Service\Catalogue $catalogue */
$catalogue = $container->get('Catalogue');

//$author = LibraryCatalog\Entity\Author::createAuthor("Charles Dickens", "1812-02-07", "1870-06-09", "Born in Portsmouth, Dickens left school to work in a factory when his father was incarcerated in a debtors' prison.", "Charles John Huffam Dickens FRSA (/ˈdɪkɪnz/; 7 February 1812 – 9 June 1870) was an English writer and social critic. He created some of the world's best-known fictional characters and is regarded by many as the greatest novelist of the Victorian era.[1] His works enjoyed unprecedented popularity during his lifetime, and by the 20th century, critics and scholars had recognised him as a literary genius. His novels and short stories are still widely read today.[2][3] ");
//$catalogue->createAuthor($author);

//$author = $catalogue->fetchAuthor(6);
//var_dump($author);

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

include "routing.php";

// Show response.
if ($response) {
    foreach ($container->get('HttpTransformer')->getResponseHeaders() as $name => $value) {
        $response = $response->withHeader($name, $value);
    }
    (new \Zend\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
}
