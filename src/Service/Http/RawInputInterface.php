<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Http;

interface RawInputInterface
{
    /**
     * Read from HTTP raw body and return as string.
     *
     * @return string
     */
    public function get(): string;
}
