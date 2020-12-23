<?php

// Prepare routing.
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'index');

    $r->addRoute('GET', '/healthcheck', 'healthcheck');
    $r->addRoute('GET', '/api/v1/healthcheck', 'healthcheck');

    $r->addRoute('GET', '/author/{id:\d+}', 'get_author_handler');
    $r->addRoute('GET', '/api/v1/author/{id:\d+}', 'get_author_handler');
    $r->addRoute('POST', '/author', 'post_author_handler');
    $r->addRoute('POST', '/api/v1/author', 'post_author_handler');

    $r->addRoute('GET', '/book/{id:\d+}', 'get_book_handler');
    $r->addRoute('GET', '/api/v1/book/{id:\d+}', 'get_book_handler');
    $r->addRoute('POST', '/book', 'post_book_handler');
    $r->addRoute('POST', '/api/v1/book', 'post_book_handler');
});

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Prepare request with extracted Auth and AuthService.
$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$request = $psr17Factory->createRequest($httpMethod, $uri);
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    // We could copy all headers but we take only what we want for speed optimization.
    $request = $request->withHeader('Authorization', $_SERVER['HTTP_AUTHORIZATION']);
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$response = null;
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = (new LibraryCatalog\Controller\V1\Error($container, $request))->notFoundError($uri);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = (new LibraryCatalog\Controller\V1\Error($container, $request))->error($uri, 405, 'Method not allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        try {
            switch ($handler) {
                case 'index':
                    $response = (new LibraryCatalog\Controller\V1\Index($container, $request))->indexAction();
                    break;
                case 'healthcheck':
                    $response = (new LibraryCatalog\Controller\V1\Index($container, $request))->healthcheckAction();
                    break;

                case 'get_author_handler':
                    $response = (new LibraryCatalog\Controller\V1\Author($container, $request))->getOneHandler($uri, $vars['id']);
                    break;
                case 'post_author_handler':
                    $response = (new LibraryCatalog\Controller\V1\Author($container, $request))->postOneHandler(
                        $uri,
                        $container->get('HttpTransformer')->deserialize(file_get_contents('php://input')),
                    );
                    break;

                case 'get_book_handler':
                    $response = (new LibraryCatalog\Controller\V1\Book($container, $request))->getOneHandler($uri, $vars['id']);
                    break;
                case 'post_book_handler':
                    $response = (new LibraryCatalog\Controller\V1\Book($container, $request))->postOneHandler(
                        $uri,
                        $container->get('HttpTransformer')->deserialize(file_get_contents('php://input')),
                        );
                    break;
            }
        } catch (\LibraryCatalog\Exception\HttpBadRequestException $e) {
            $response = (new LibraryCatalog\Controller\V1\Error($container, $request))->badRequestError($uri, $e->getMessage());
        } catch (\LibraryCatalog\Exception\HttpUnauthorizedException $e) {
            $response = (new LibraryCatalog\Controller\V1\Error($container, $request))->unauthorizedError($uri, $e->getMessage());
        } catch (\Exception $e) {
            //@todo Log
            throw $e;
            $response = (new LibraryCatalog\Controller\V1\Error($container, $request))->systemError($uri);
        } catch (\Throwable $e) {
            //@todo Log
            throw $e;
            $response = (new LibraryCatalog\Controller\V1\Error($container, $request))->systemError($uri);
        }
        break;
}
