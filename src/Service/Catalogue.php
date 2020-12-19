<?php

declare(strict_types=1);

namespace Service;

use Entity\Author;
use Repository\AuthorRepositoryInterface;

class Catalogue
{
    /** @var AuthorRepositoryInterface */
    protected AuthorRepositoryInterface $authorFrontRepository;
    /** @var AuthorRepositoryInterface */
    protected AuthorRepositoryInterface $authorBackRepository;

    public function __construct(
        AuthorRepositoryInterface $authorFrontRepository,
        AuthorRepositoryInterface $authorBackRepository
    ) {
        $this->authorFrontRepository = $authorFrontRepository;
        $this->authorBackRepository = $authorBackRepository;
    }

    public function createAuthor(Author $author): void
    {
        //@todo Lock.
        $this->authorBackRepository->save($author);
        if ($this->authorFrontRepository) {
            $this->authorFrontRepository->save($author);
        }
    }
}
