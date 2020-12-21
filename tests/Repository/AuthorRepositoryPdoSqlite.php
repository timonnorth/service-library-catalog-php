<?php

declare(strict_types=1);

namespace Repository;

use LibraryCatalog\Repository\AuthorRepositoryPdo;

class AuthorRepositoryPdoSqlite extends AuthorRepositoryPdo
{
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
     * @return \PDO
     */
    public function pdo(): \PDO
    {
        return ($this->connection)();
    }
}
