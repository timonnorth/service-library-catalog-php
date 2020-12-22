<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1\Transformer;

use LibraryCatalog\Controller\TransformerException;

class BookWithAuthor extends Book
{
    /**
     * @param $data
     * @return array
     * @throws TransformerException
     */
    public function transform($data): array
    {
        $res = parent::transform($data);

        if (isset($data->author)) {
            $res['author'] = (new Author())->transform($data->author);
        }

        return $res;
    }
}
