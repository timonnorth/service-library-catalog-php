<?php

declare(strict_types=1);

namespace LibraryCatalog\Service;

use Psr\Http\Message\RequestInterface;

interface AuthInInterface
{
    /**
     * @param RequestInterface $request
     * @return AuthInInterface
     */
    public function setRequest(RequestInterface $request): AuthInInterface;

    /**
     * Returns true if your request authenticated.
     *
     * @return bool
     */
    public function authenticated(): bool;

    /**
     * Payload after decoding.
     *
     * @return string
     */
    public function getPayload(): string;
}
