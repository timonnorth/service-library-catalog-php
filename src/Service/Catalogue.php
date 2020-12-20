<?php

declare(strict_types=1);

namespace LibraryCatalog\Service;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Repository\AuthorRepositoryInterface;

class Catalogue
{
    /** @var AuthorRepositoryInterface */
    protected AuthorRepositoryInterface $authorRepository;

    public function __construct(
        AuthorRepositoryInterface $authorRepository
    ) {
        $this->authorRepository = $authorRepository;
    }

    public function fetchAuthor($id): ?Author
    {
        return $this->authorRepository->load($id);
    }

    public function createAuthor(Author $author): void
    {
        //@todo Lock.
        $this->authorRepository->save($author);
    }
}
