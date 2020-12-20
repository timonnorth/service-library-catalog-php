<?php

declare(strict_types=1);

namespace LibraryCatalog\Repository;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Transformer\Entity;

trait PdoTrait
{
    /** @var \PDO */
    protected \PDO $pdo;
    /** @var bool */
    protected bool $pdoReady = false;
    /** @var \Closure */
    protected \Closure $connection;

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
                    sprintf('mysql:dbname=%s;host=%s', $dbname, $host),
                    $user,
                    $password
                );
                $this->pdoReady = true;
            }
            return $this->pdo;
        };
    }

    /**
     * @param string $table
     * @param mixed $id
     * @return array|null
     */
    protected function fetchOne(string $table, $id): ?array
    {
        /** @var \PDO $pdo */
        $pdo = ($this->connection)();

        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        // Convert false to null.
        return $res ? $res : null;
    }

    /**
     * Inserts data and returns its ID.
     *
     * @param string $table
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    protected function insert(string $table, array $data)
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
