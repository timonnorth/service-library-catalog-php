<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use Psr\Http\Message\ResponseInterface;

class Author extends AbstractController
{
    public function getOneHandler(string $uri, string $id): ResponseInterface
    {
        $author = $this->container->get('Catalogue')->fetchAuthor($id);

        if ($author) {
            $response = $this->status();
        } else {
            $response = $this->notFoundError($uri, 'Author not found');
        }
        return $response;
    }
}
