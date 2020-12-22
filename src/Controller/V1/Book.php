<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use Psr\Http\Message\ResponseInterface;
use LibraryCatalog\Controller\V1\Transformer\BookWithAuthor as BookTransformer;

class Book extends AbstractController
{
    public function getOneHandler(string $uri, string $id): ResponseInterface
    {
        $book = $this->container->get('Catalogue')->fetchBook($id, true);

        if ($book) {
            $response = $this->createResponse((new BookTransformer())->transform($book));
        } else {
            $response = $this->notFoundError($uri, 'Book not found');
        }
        return $response;
    }
}
