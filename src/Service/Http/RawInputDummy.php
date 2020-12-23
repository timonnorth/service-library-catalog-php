<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Http;

class RawInputDummy implements RawInputInterface
{
    /** @var string */
    protected string $body = '';

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function set(string $body)
    {
        $this->body = $body;
    }
}
