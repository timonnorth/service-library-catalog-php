<?php

declare(strict_types=1);

namespace LibraryCatalog\Repository;

use LibraryCatalog\Entity\Book;
use LibraryCatalog\Transformer\Serializer;

class BookRepositoryPdo implements BookRepositoryInterface
{
    use PdoTrait;

    protected const TABLE_NAME = 'books';
    protected const LIMIT_BOOKS = 5000;

    /** @var Serializer */
    protected Serializer $serializer;

    /**
     * BookRepositoryPdo constructor.
     * @param Serializer $serializer
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     */
    public function __construct(Serializer $serializer, string $host, string $user, string $password, string $dbname)
    {
        $this->prepareConnection($host, $user, $password, $dbname);
        $this->serializer = $serializer;
    }

    /**
     * @param mixed $id
     * @return Book|null
     * @throws Exception
     * @throws Serializer\HydrateException
     */
    public function load($id): ?Book
    {
        $data = $this->fetchOne(static::TABLE_NAME, $id);
        return $data ? $this->serializer->hydrate($data, Book::class) : null;
    }

    /**
     * @param mixed $authorId
     * @return Book[]
     * @throws Exception
     * @throws Serializer\HydrateException
     */
    public function loadByAuthorId($authorId): array
    {
        $data = $this->fetchList(static::TABLE_NAME, 'authorId = ?', [$authorId], self::LIMIT_BOOKS, 'title');
        $rows = [];
        foreach ($data as $row) {
            $rows[] = $this->serializer->hydrate($row, Book::class);
        }
        return $rows;
    }

    /**
     * @param Book $book
     * @throws Exception
     */
    public function save(Book $book): void
    {
        $book->id = $this->insert(static::TABLE_NAME, $this->serializer->extractFields($book));
    }
}
