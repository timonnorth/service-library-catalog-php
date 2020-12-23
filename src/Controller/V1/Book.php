<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use LibraryCatalog\Service\Catalogue;
use LibraryCatalog\Service\Validation\Rule\AuthorExists;
use Psr\Http\Message\ResponseInterface;
use LibraryCatalog\Controller\V1\Transformer\BookWithAuthor as BookTransformer;
use LibraryCatalog\Entity\Book as BookEntity;
use Rakit\Validation\Validator;

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

    /**
     * @param string $uri
     * @param array $params
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \LibraryCatalog\Controller\TransformerException
     * @throws \Rakit\Validation\RuleQuashException
     */
    public function postOneHandler(string $uri, array $params): ResponseInterface
    {
        /** @var Catalogue $catalogue */
        $catalogue = $this->container->get('Catalogue');

        $validator = new Validator();
        $validator->addValidator('authorExists', new AuthorExists($catalogue->getAuthorRepository()));
        $validation = $validator->validate($params, [
            'title'                 => 'required|min:3|max:4096',
            'summary'               => 'max:65534',
            'authorId'              => 'required|authorExists',
        ]);

        if ($validation->fails()) {
            $response = $this->validationError($uri, $validation->errors()->firstOfAll());
        } else {
            $book = (new BookEntity())->fill($params);
            $catalogue->createBook($book);
            $response = $this->createResponse((new BookTransformer())->transform($book));
        }

        return $response;
    }
}
