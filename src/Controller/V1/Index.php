<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use Psr\Http\Message\ResponseInterface;

class Index extends AbstractController
{
    /** @var bool */
    protected bool $needAuth = false;

    /**
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function indexAction(): ResponseInterface
    {
        return $this->status();
    }

    /**
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function healthcheckAction(): ResponseInterface
    {
        //return $this->systemError('healthcheck');
        return $this->status();
    }
}
