<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Repository;

use LibraryCatalog\Entity\Author;

interface AuthorRepositoryInterface
{
    /**
     * @param mixed $id
     * @return Author|null
     */
    public function load($id): ?Author;

    /**
     * @param Author $author
     */
    public function save(Author $author): void;
}
