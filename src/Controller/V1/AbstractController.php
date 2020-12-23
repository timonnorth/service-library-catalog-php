<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use DI\Container;
use LibraryCatalog\Controller\V1\ValueObject\Error as ErrorDto;
use LibraryCatalog\Controller\V1\ValueObject\ErrorWithValidation;
use LibraryCatalog\Controller\V1\ValueObject\Status;
use LibraryCatalog\Exception\HttpUnauthorizedException;
use LibraryCatalog\Service\AclInterface;
use LibraryCatalog\Service\AuthInInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractController
{
    /** @var Container */
    protected Container $container;
    /** @var Psr17Factory */
    protected Psr17Factory $responseFactory;
    /** @var RequestInterface */
    protected RequestInterface $request;
    /**
     * You can change this value in your controller.
     * @var bool
     */
    protected bool $needAuth = true;

    /**
     * AbstractController constructor.
     * @param Container $container
     * @param RequestInterface $request
     * @throws HttpUnauthorizedException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function __construct(Container $container, RequestInterface $request)
    {
        $this->container = $container;
        $this->responseFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $this->request = $request;

        // Check Auth.
        if ($this->needAuth) {
            /** @var AuthInInterface $auth */
            $auth = $this->getAuthIn()->setRequest($request);
            if (!$auth->authenticated()) {
                throw new HttpUnauthorizedException();
            }
        }
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
     * @param string $message
     * @param string $code
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function badRequestError(string $uri, string $message = 'Bad request', string $code = ''): ResponseInterface
    {
        return $this->error($uri, 400, $message, $code);
    }

    /**
     * @param string $uri
     * @param string $message
     * @param string $code
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function forbiddenError(string $uri, string $message = 'Forbidden', string $code = ''): ResponseInterface
    {
        return $this->error($uri, 403, $message, $code);
    }

    /**
     * @param string $uri
     * @param string $message
     * @param string $code
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function unauthorizedError(string $uri, string $message = 'Unauthorized', string $code = ''): ResponseInterface
    {
        return $this->error($uri, 401, $message, $code);
    }

    /**
     * @param string $uri
     * @param array $fields
     * @param string $code
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function validationError(string $uri, array $fields, string $code = ''): ResponseInterface
    {
        return $this->responseFactory->createResponse(400)->withBody(
            $this->responseFactory->createStream(
                $this->serialize(ErrorWithValidation::create('Validation error', $code)->withFields($fields))
            )
        );
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
    public function error(string $uri, int $httpStatusCode = 500, string $message = 'Page not found', string $code = ''): ResponseInterface
    {
        return $this->responseFactory->createResponse($httpStatusCode)->withBody(
            $this->responseFactory->createStream($this->serialize(ErrorDto::create($message, $code)))
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
        return $this->createResponse(Status::create($status));
    }

    /**
     * @param $data
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function createResponse($data): ResponseInterface
    {
        return $this->responseFactory->createResponse(200)->withBody(
            $this->responseFactory->createStream($this->serialize($data))
        );
    }

    /**
     * @param mixed $data
     * @return string
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function serialize($data): string
    {
        return $this->container->get('HttpTransformer')->serialize($data);
    }

    /**
     * @return AclInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function getAcl(): AclInterface
    {
        return $this->container->get('Acl');
    }

    /**
     * @return AuthInInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function getAuthIn(): AuthInInterface
    {
        return $this->container->get('AuthIn');
    }
}
