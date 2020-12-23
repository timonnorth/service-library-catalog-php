<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use LibraryCatalog\Controller\TransformerException;
use LibraryCatalog\Service\Acl;
use LibraryCatalog\Service\Catalogue;
use LibraryCatalog\Service\Validation\Rule\AuthorExists;
use Psr\Http\Message\ResponseInterface;
use LibraryCatalog\Controller\V1\Transformer\BookWithAuthor as BookTransformer;
use LibraryCatalog\Entity\Book as BookEntity;
use Rakit\Validation\RuleQuashException;
use Rakit\Validation\Validator;

class Book extends AbstractController
{
    /**
     * @param string $uri
     * @param string $id
     * @return ResponseInterface
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws TransformerException
     */
    public function getOneHandler(string $uri, string $id): ResponseInterface
    {
        // Check ACL first.
        if (
            !$this->getAcl()->isAllowed(
                $this->getAcl()->parseUserPayload($this->getAuthIn()->getPayload()),
                Acl::BOOK,
                Acl::READ
            )
        ) {
            return $this->forbiddenError($uri);
        }

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
     * @throws TransformerException
     * @throws RuleQuashException
     */
    public function postOneHandler(string $uri, array $params): ResponseInterface
    {
        // Check ACL first.
        if (
            !$this->getAcl()->isAllowed(
                $this->getAcl()->parseUserPayload($this->getAuthIn()->getPayload()),
                Acl::BOOK,
                Acl::ADD
            )
        ) {
            return $this->forbiddenError($uri);
        }

        /** @var Catalogue $catalogue */
        $catalogue = $this->container->get('Catalogue');

        /**
         * In a simple project I do not want to add Request-Hydrators and do validation directly in controller.
         */

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
