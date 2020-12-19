<?php

declare(strict_types=1);

namespace Repository;

use Entity\Author;

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
