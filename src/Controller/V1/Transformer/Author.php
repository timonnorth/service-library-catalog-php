<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1\Transformer;

use LibraryCatalog\Controller\TransformerException;
use LibraryCatalog\Controller\TransformerInterface;
use LibraryCatalog\Entity\Author as Entity;

class Author implements TransformerInterface
{
    /**
     * @param $data
     * @return array
     * @throws TransformerException
     */
    public function transform($data): array
    {
        if (!($data instanceof Entity)) {
            throw new TransformerException("Tried to transform not an Author");
        }
        return [
            'id'        => $data->id,
            'name'      => $data->name,
            'birthdate' => $data->birthdate,
            'deathdate' => $data->deathdate,
            'biography' => $data->biography,
            'summary'   => $data->summary,
        ];
    }
}
