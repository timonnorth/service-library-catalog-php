<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller;

interface TransformerInterface
{
    /**
     * @param $data
     * @return array
     */
    public function transform($data): array;
}
