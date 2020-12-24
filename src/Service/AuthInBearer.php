<?php

declare(strict_types=1);

namespace LibraryCatalog\Service;

use Psr\Http\Message\RequestInterface;

class AuthInBearer implements AuthInInterface
{
    /** @var RequestInterface */
    protected RequestInterface $request;
    /** @var bool */
    protected bool $parsed = false;
    /** @var string */
    protected string $payload;
    /** @var bool */
    protected bool $isAuthenticated = false;
    /** @var string */
    protected string $secret;

    /**
     * AuthInBearer constructor.
     * @param string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param RequestInterface $request
     * @return AuthInInterface
     */
    public function setRequest(RequestInterface $request): AuthInInterface
    {
        $this->request = $request;
        $this->parsed = false;
        return $this;
    }

    /**
     * @return bool
     */
    public function authenticated(): bool
    {
        $this->parse();
        return $this->isAuthenticated;
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        $this->parse();
        return $this->payload ?? '';
    }

    /**
     * @return void
     */
    protected function parse(): void
    {
        if (!$this->parsed) {
            if (isset($this->request)) {
                $bearer = $this->request->getHeader('Authorization');
                if (count($bearer) >= 1) {
                    $ar = explode(' ', $bearer[0]);
                    if (count($ar) >= 2 && $ar[0] === 'Bearer') {
                        $this->proceedData((string)base64_decode($ar[1]));
                    }
                }
            }
            $this->parsed = true;
        }
    }

    /**
     * @param string $data
     * @return void
     */
    protected function proceedData(string $data): void
    {
        $decoded = @json_decode($data);
        if ($decoded) {
            if (isset($decoded->secret) && $decoded->secret === $this->secret) {
                // Authenticate only if secret is valid.
                $this->isAuthenticated = true;
                if (isset($decoded->payload)) {
                    $this->payload = (string)$decoded->payload;
                }
            }
        }
    }
}
