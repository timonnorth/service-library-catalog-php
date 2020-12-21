<?php

declare(strict_types=1);

namespace LibraryCatalog\Transformer;

use LibraryCatalog\Transformer\Encoder\EncoderInterface;
use LibraryCatalog\Transformer\Serializer\Exception;
use LibraryCatalog\Transformer\Serializer\HydrateException;

interface HttpSerializerInterface
{
    /**
     * @param $data
     * @return string
     */
    public function serialize($data): string;

    /**
     * @return array
     */
    public function getResponseHeaders(): array;
}
