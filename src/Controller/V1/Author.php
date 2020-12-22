<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use Psr\Http\Message\ResponseInterface;
use LibraryCatalog\Controller\V1\Transformer\AuthorWithBooks as AuthorTransformer;
use LibraryCatalog\Entity\Author as AuthorEnity;
use Rakit\Validation\Validator;

class Author extends AbstractController
{
    /**
     * @param string $uri
     * @param string $id
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \LibraryCatalog\Controller\TransformerException
     */
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

    public function postOneHandler(string $uri, array $params): ResponseInterface
    {
        $validator = new Validator();
        $validation = $validator->validate($params, [
            'name'                  => 'required|min:3|max:255',
            'birthdate'             => 'required|date',
            'deathdate'             => 'date',
            'biography'             => 'max:65534',
            'summary'               => 'max:65534',
        ]);

        if ($validation->fails()) {
            $response = $this->validationError($uri, $validation->errors()->firstOfAll());
        } else {
            $author = $this->container->get('Serializer')->hydrate($params, AuthorEnity::class);
            $this->container->get('Catalogue')->createAuthor($author);
            $response = $this->createResponse((new AuthorTransformer())->transform($author));
        }

        return $response;
    }
}
