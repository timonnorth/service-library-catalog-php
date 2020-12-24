<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Repository;

use LibraryCatalog\Entity\Book;

interface BookRepositoryInterface
{
    /**
     * @param mixed $id
     * @param bool $withAuthor
     * @return Book|null
     */
    public function load($id, bool $withAuthor = false): ?Book;

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
