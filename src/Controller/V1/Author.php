<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1;

use LibraryCatalog\Controller\TransformerException;
use LibraryCatalog\Service\Acl;
use LibraryCatalog\Service\Catalogue;
use LibraryCatalog\Service\Validation\Rule\AuthorUniqueName;
use Psr\Http\Message\ResponseInterface;
use LibraryCatalog\Controller\V1\Transformer\AuthorWithBooks as AuthorTransformer;
use LibraryCatalog\Entity\Author as AuthorEntity;
use Rakit\Validation\RuleQuashException;
use Rakit\Validation\Validator;

class Author extends AbstractController
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
                Acl::AUTHOR,
                Acl::READ
            )
        ) {
            return $this->forbiddenError($uri);
        }

        $author = $this->container->get('Catalogue')->fetchAuthor($id, true);

        if ($author) {
            $response = $this->createResponse((new AuthorTransformer())->transform($author));
        } else {
            $response = $this->notFoundError($uri, 'Author not found');
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
                Acl::AUTHOR,
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
        $validator->addValidator('uniqueAuthor', new AuthorUniqueName($catalogue->getAuthorRepository(), $params['birthdate'] ?? ''));
        $validation = $validator->validate($params, [
            'name'                  => 'required|min:3|max:255|uniqueAuthor',
            'birthdate'             => 'required|date',
            'deathdate'             => 'date',
            'biography'             => 'max:65534',
            'summary'               => 'max:65534',
        ]);

        if ($validation->fails()) {
            $response = $this->validationError($uri, $validation->errors()->firstOfAll());
        } else {
            $author = (new AuthorEntity())->fill($params);
            $catalogue->createAuthor($author);
            $response = $this->createResponse((new AuthorTransformer())->transform($author));
        }

        return $response;
    }
}
