<?php

declare(strict_types=1);

namespace LibraryCatalog\Repository;

use LibraryCatalog\Entity\Book;

interface BookRepositoryInterface
{
    /**
     * @param mixed $id
     * @return Book|null
     */
    public function load($id): ?Book;

    /**
     * @param mixed $authorId
     * @return Book[]
     */
    public function loadByAuthorId($authorId): array;

    /**
     * @param Book $book
     */
    public function save(Book $book): void;
}
