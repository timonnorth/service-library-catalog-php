<?php

declare(strict_types=1);

namespace LibraryCatalog\Repository;

use LibraryCatalog\Entity\Author;

interface AuthorRepositoryInterface
{
    /**
     * @param mixed $id
     * @param bool $withBooks
     * @return Author|null
     */
    public function load($id, bool $withBooks = false): ?Author;

    /**
     * @param Author $author
     */
    public function save(Author $author): void;
}
