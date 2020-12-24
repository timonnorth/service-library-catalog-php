<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Http;

class RawInputPhp implements RawInputInterface
{
    /**
     * @return string
     */
    public function get(): string
    {
        return (string)file_get_contents('php://input');
    }
}
