<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller;

use DI\Container;
use LibraryCatalog\Controller\ValueObject\Error as ErrorDto;
use LibraryCatalog\Controller\ValueObject\Status;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractController
{
    /** @var Container */
    protected Container $container;
    /** @var Psr17Factory */
    protected Psr17Factory $responseFactory;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
    }

    /**
     * @param string $uri
     * @param string $message
     * @param string $code
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @return ResponseInterface
     */
    public function systemError(string $uri, string $message = 'System error', string $code = ''): ResponseInterface
    {
        return $this->error($uri, 500, $message, $code);
    }

    /**
     * @param string $uri
     * @param string $message
     * @param string $code
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @return ResponseInterface
     */
    public function notFoundError(string $uri, string $message = 'Page not found', string $code = ''): ResponseInterface
    {
        return $this->error($uri, 404, $message, $code);
    }

    /**
     * @param string $uri
     * @param int $httpStatusCode
     * @param string $message
     * @param string $code
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @return ResponseInterface
     */
    protected function error(string $uri, int $httpStatusCode = 500, string $message = 'Page not found', string $code = ''): ResponseInterface
    {
        return $this->responseFactory->createResponse($httpStatusCode)->withBody(
            $this->responseFactory->createStream($this->transform(ErrorDto::create($message)))
        );
    }

    /**
     * @param string $status
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function status(string $status = 'Ok'): ResponseInterface
    {
        return $this->responseFactory->createResponse(200)->withBody(
            $this->responseFactory->createStream($this->transform(Status::create($status)))
        );
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
