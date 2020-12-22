<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use Psr\Http\Message\ResponseInterface;
use LibraryCatalog\Controller\V1\Transformer\AuthorWithBooks as AuthorTransformer;

class Author extends AbstractController
{
    public function getOneHandler(string $uri, string $id): ResponseInterface
    {
        $author = $this->container->get('Catalogue')->fetchAuthor($id, true);

        if ($author) {
            $response = $this->createResponse((new AuthorTransformer())->transform($author));
        } else {
            $response = $this->notFoundError($uri, 'Author not found');
        }
        return $response;
    }
}
