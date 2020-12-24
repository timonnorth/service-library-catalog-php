<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Http;

use DI\Container;
use FastRoute\Dispatcher;
use LibraryCatalog\Controller\V1\Author;
use LibraryCatalog\Controller\V1\Book;
use LibraryCatalog\Controller\V1\Error;
use LibraryCatalog\Controller\V1\Index;
use Psr\Http\Message\ResponseInterface;

class Router
{
    /** @var Container */
    protected Container $container;
    /** @var Dispatcher */
    protected Dispatcher $dispatcher;
    /** @var string */
    protected string $uri;
    /** @var string */
    protected string $httpMethod;
    /** @var array */
    protected array $serverParams;

    /**
     * Router constructor.
     * @param Container $container
     * @param string $uri
     * @param string $httpMethod
     * @param array $serverParams
     */
    public function __construct(Container $container, string $uri, string $httpMethod, array $serverParams)
    {
        $this->container = $container;
        $this->uri = $uri;
        $this->httpMethod = $httpMethod;
        $this->serverParams = $serverParams;
    }

    /**
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \LibraryCatalog\Exception\HttpUnauthorizedException
     */
    public function dispatch(): ResponseInterface
    {
        // Prepare request with extracted Auth.
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $request = $psr17Factory->createRequest($this->httpMethod, $this->uri);
        if (isset($this->serverParams['HTTP_AUTHORIZATION'])) {
            // We could copy all headers but we take only what we want for speed optimization.
            $request = $request->withHeader('Authorization', $this->serverParams['HTTP_AUTHORIZATION']);
        }

        $routeInfo = $this->getDispatcher()->dispatch($this->httpMethod, $this->uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $response = (new Error($this->container, $request))->notFoundError($this->uri);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response = (new Error($this->container, $request))
                    ->error($this->uri, 405, 'Method not allowed');
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                try {
                    switch ($handler) {
                        case 'index':
                            $response = (new Index($this->container, $request))->indexAction();
                            break;
                        case 'healthcheck':
                            $response = (new Index($this->container, $request))->healthcheckAction();
                            break;

                        case 'get_author_handler':
                            $response = (new Author($this->container, $request))
                                ->getOneHandler($this->uri, $vars['id']);
                            break;
                        case 'post_author_handler':
                            $response = (new Author($this->container, $request))->postOneHandler(
                                $this->uri,
                                $this->container->get('HttpTransformer')
                                    ->deserialize($this->container->get('RawInput')->get()),
                            );
                            break;

                        case 'get_book_handler':
                            $response = (new Book($this->container, $request))
                                ->getOneHandler($this->uri, $vars['id']);
                            break;
                        case 'post_book_handler':
                            $response = (new Book($this->container, $request))->postOneHandler(
                                $this->uri,
                                $this->container->get('HttpTransformer')
                                    ->deserialize($this->container->get('RawInput')->get()),
                            );
                            break;
                    }
                } catch (\LibraryCatalog\Exception\HttpBadRequestException $e) {
                    $response = (new Error($this->container, $request))
                        ->badRequestError($this->uri, $e->getMessage());
                } catch (\LibraryCatalog\Exception\HttpUnauthorizedException $e) {
                    $response = (new Error($this->container, $request))
                        ->unauthorizedError($this->uri, $e->getMessage());
                } catch (\Exception $e) {
                    //@todo Log
                    //throw $e;
                    $response = (new Error($this->container, $request))->systemError($this->uri);
                } catch (\Throwable $e) {
                    //@todo Log
                    //throw $e;
                    $response = (new Error($this->container, $request))->systemError($this->uri);
                }
                break;
            default:
                // Undefined behavior.
                $response = (new Error($this->container, $request))->systemError($this->uri);
                break;
        }

        return $response;
    }

    /**
     * @return Dispatcher
     */
    protected function getDispatcher(): Dispatcher
    {
        if (!isset($this->dispatcher)) {
            $this->dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
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
        }

        return $this->dispatcher;
    }
}
