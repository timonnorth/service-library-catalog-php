<?php

declare(strict_types=1);

namespace LibraryCatalog\Repository;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Transformer\Serializer;

class AuthorRepositoryPdo implements AuthorRepositoryInterface
{
    use PdoTrait;

    protected const TABLE_NAME = 'authors';

    /** @var Serializer */
    protected Serializer $serializer;

    /**
     * AuthorRepositoryPdo constructor.
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
     * @return Author|null
     * @throws Exception
     * @throws Serializer\HydrateException
     */
    public function load($id): ?Author
    {
        $data = $this->fetchOne(static::TABLE_NAME, $id);
        return $data ? $this->serializer->hydrate($data, Author::class) : null;
    }

    /**
     * @param Author $author
     * @return void
     * @throws Exception
     */
    public function save(Author $author): void
    {
        $author->id = $this->insert(static::TABLE_NAME, $this->serializer->extractFields($author));
    }
}
