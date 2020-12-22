<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1\Transformer;

use LibraryCatalog\Controller\TransformerException;

class AuthorWithBooks extends Author
{
    /**
     * @param $data
     * @return array
     * @throws TransformerException
     */
    public function transform($data): array
    {
        $res = parent::transform($data);

        $books = [];
        if (isset($data->books)) {
            $bookTransformer = new Book();
            foreach ($data->books as $book) {
                $books[] = $bookTransformer->transform($book);
            }
            $res['books'] = $books;
        }

        return $res;
    }
}
