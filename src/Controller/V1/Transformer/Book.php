<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1\Transformer;

use LibraryCatalog\Controller\TransformerException;
use LibraryCatalog\Controller\TransformerInterface;
use LibraryCatalog\Entity\Book as Entity;

class Book implements TransformerInterface
{
    /**
     * @param $data
     * @return array
     * @throws TransformerException
     */
    public function transform($data): array
    {
        if (!($data instanceof Entity)) {
            throw new TransformerException("Tried to transform not a Book");
        }
        return [
            'id'      => $data->id ?? null,
            'title'   => $data->title ?? null,
            'summary' => $data->summary ?? null,
        ];
    }
}
