<?php

declare(strict_types=1);

namespace Repository;

use Entity\Author;
use Exception\AppException;

class AuthorRepositoryMysql implements AuthorRepositoryInterface
{
    /** @var \mysqli */
    protected $connectionLink;
    /** @var \Closure */
    protected $connection;

    public function __construct(string $host, string $user, string $password, string $dbname)
    {
        $this->connection = function () use ($host, $user, $password, $dbname): \mysqli {
            if ($this->connectionLink === null) {
                $this->connectionLink = mysqli_connect($host, $user, $password, $dbname);
                if ($this->connectionLink === false) {
                    // ahalay
                    throw (new Exception())->setCode('error.mysql.connect');
                }
            }
        };
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
     */
    public function save(Author $author): void
    {
    }
}
