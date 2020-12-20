<?php

declare(strict_types=1);

namespace LibraryCatalog\Repository;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Transformer\Entity;

class AuthorRepositoryPdo implements AuthorRepositoryInterface
{
    protected const TABLE_NAME = 'authors';

    /** @var \PDO */
    protected \PDO $pdo;
    /** @var bool */
    protected bool $pdoReady = false;
    /** @var \Closure */
    protected \Closure $connection;
    /** @var Entity */
    protected Entity $entitySerializer;

    /**
     * AuthorRepositoryPdo constructor.
     * @param Entity $entitySerializer
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     */
    public function __construct(Entity $entitySerializer, string $host, string $user, string $password, string $dbname)
    {
        $this->connection = function () use ($host, $user, $password, $dbname): \PDO {
            if (!$this->pdoReady) {
                $this->pdo = new \PDO(
                    sprintf('mysql:dbname=%s;host=%s', $dbname, $host),
                    $user,
                    $password
                );
                $this->pdoReady = true;
            }
            return $this->pdo;
        };
        $this->entitySerializer = $entitySerializer;
    }

    /**
     * @param mixed $id
     * @return Author|null
     */
    public function load($id): ?Author
    {
    }

    /**
     * @param Author $author
     * @return void
     * @throws Exception
     */
    public function save(Author $author): void
    {
        $author->id = $this->insert($this->entitySerializer->extractFields($author), static::TABLE_NAME);
    }

    /**
     * Inserts data and returns its ID.
     *
     * @param array $data
     * @param string $table
     * @return mixed
     * @throws Exception
     */
    protected function insert(array $data, string $table)
    {
        $rowNames = '';
        $rowValues = '';
        foreach ($data as $name => $value) {
            if ($value !== null) {
                if ($rowNames !== '') {
                    $rowNames .= ',';
                }
                if ($rowValues !== '') {
                    $rowValues .= ',';
                }
                $rowNames .= $name;
                $rowValues .= ':' . $name;
            } else {
                unset($data[$name]);
            }
        }

        /** @var \PDO $pdo */
        $pdo = ($this->connection)();
        $sql = "INSERT INTO $table ($rowNames) VALUES ($rowValues)";
        if (!$pdo->prepare($sql)->execute($data)) {
            throw new Exception("Can not insert data in PDO");
        }

        return $pdo->lastInsertId();
    }
}
