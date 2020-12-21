<?php

// Prepare routing.
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'index');

    $r->addRoute('GET', '/healthcheck', 'healthcheck');
    $r->addRoute('GET', '/api/v1/healthcheck', 'healthcheck');

    $r->addRoute('GET', '/author/{id:\d+}', 'get_author_handler');

    $r->addRoute('GET', '/users', 'get_all_users_handler');
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$response = null;
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = (new \LibraryCatalog\Controller\Error($container))->notFoundError($uri);
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        switch ($handler) {
            case 'index':
                $response = (new \LibraryCatalog\Controller\Index($container))->indexAction();
                break;
            case 'healthcheck':
                $response = (new \LibraryCatalog\Controller\Index($container))->healthcheckAction();
                break;
            case 'get_author_handler':
                $response = (new \LibraryCatalog\Controller\Author($container))->getOneHandler($uri, $vars['id']);
                break;
        }
        break;
}
