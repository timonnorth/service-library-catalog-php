<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller;

use Psr\Http\Message\ResponseInterface;

class Index extends AbstractController
{
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
