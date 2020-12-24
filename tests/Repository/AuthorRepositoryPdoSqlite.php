<?php

declare(strict_types=1);

namespace Repository;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Infrastructure\Persistence\AuthorRepositoryPdo;
use LibraryCatalog\Service\Repository\Exception;

class AuthorRepositoryPdoSqlite extends AuthorRepositoryPdo
{
    /** @var mixed */
    protected $id;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     */
    protected function prepareConnection(string $host, string $user, string $password, string $dbname): void
    {
        $this->connection = function () use ($host, $user, $password, $dbname): \PDO {
            if (!$this->pdoReady) {
                $this->pdo = new \PDO(
                    'sqlite::memory:',
                    null,
                    null,
                    array(\PDO::ATTR_PERSISTENT => true),
                );
                $this->pdoReady = true;
            }
            return $this->pdo;
        };
    }

    /**
     * @param Author $author
     * @return void
     * @throws Exception
     */
    public function save(Author $author): void
    {
        if (isset($this->id) && $this->id != '') {
            $author->id = $this->id;
        }
        parent::save($author);
        $this->id = null;
    }

    /**
     * @param $id
     */
    public function setIdForce($id): void
    {
        $this->id = $id;
    }
}
