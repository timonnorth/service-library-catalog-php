<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller;

use DI\Container;
use LibraryCatalog\Controller\ValueObject\Error as ErrorDto;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

abstract class AbstractController
{
    /** @var Container */
    protected Container $container;
    /** @var SapiEmitter */
    protected SapiEmitter $emmiter;
    /** @var Psr17Factory */
    protected Psr17Factory $responseFactory;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $this->emmiter = new \Zend\HttpHandlerRunner\Emitter\SapiEmitter();
    }

    /**
     * @param string $uri
     * @param string $message
     * @param string $code
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function systemError(string $uri, string $message = 'System error', string $code = '')
    {
        $this->error($uri, 500, $message, $code);
    }

    /**
     * @param string $uri
     * @param string $message
     * @param string $code
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function notFoundError(string $uri, string $message = 'Page not found', string $code = '')
    {
        $this->error($uri, 404, $message, $code);
    }

    /**
     * @param string $uri
     * @param int $httpStatusCode
     * @param string $message
     * @param string $code
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function error(string $uri, int $httpStatusCode, string $message = 'Page not found', string $code = '')
    {
        $this->emmit($response = $this->responseFactory->createResponse($httpStatusCode)->withBody(
            $this->responseFactory->createStream($this->transform(ErrorDto::create($message)))
        ));
    }

    /**
     * @param ResponseInterface $response
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function emmit(ResponseInterface $response): void
    {
        foreach ($this->container->get('HttpTransformer')->getResponseHeaders() as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        $this->emmiter->emit($response);
    }

    /**
     * @param mixed $data
     * @return string
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function transform($data): string
    {
        return $this->container->get('HttpTransformer')->serialize($data);
    }
}
