<?php

declare(strict_types=1);

namespace Tests;

use DI\Container;
use DI\ContainerBuilder;
use LibraryCatalog\Transformer\Serializer;
use Psr\Http\Message\ResponseInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var Container */
    protected ?Container $container;

    /**
     * TestCase constructor.
     * @param string|null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        if (!defined('__APPDIR__')) {
            define('__APPDIR__', realpath(sprintf('%s/..', __DIR__)));
        }
        $this->container = null;

        parent::__construct($name, $data, $dataName);
    }

    /**
     * @return Container
     * @throws \Exception
     */
    protected function getContainer(): Container
    {
        if ($this->container === null) {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->addDefinitions(__DIR__ . '/config.php');
            $this->container = $containerBuilder->build();
        }

        return $this->container;
    }

    /**
     * @param string $httpMethod
     * @param string $uri
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function route(string $httpMethod, string $uri): ResponseInterface
    {
        $container = $this->getContainer();
        include __APPDIR__ . "/app/routing.php";
        return $response;
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function runDbMigration(): void
    {
        require_once __APPDIR__ . "/app/migrations/20111101000145_CreateAuthorsTable.php";
        require_once __APPDIR__ . "/app/migrations/20111101000146_CreateBooksTable.php";

        foreach ([
            (new \CreateAuthorsTable(0))->sqlSqlite(),
            (new \CreateBooksTable(0))->sqlSqlite(),
        ] as $sql) {
            $stmt = $this->pdo()->prepare($sql);
            if ($stmt === false) {
                throw new \Exception(json_encode($this->pdo()->errorInfo()));
            }
            if (!$stmt->execute()) {
                throw new \Exception(json_encode($this->pdo()->errorInfo()));
            }
        }
    }

    /**
     * @return \PDO
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function pdo(): \PDO
    {
        return $this->getContainer()->get('AuthorRepository')->pdo();
    }

    /**
     * @return Serializer
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function getSerializer(): Serializer
    {
        return $this->getContainer()->get('Serializer');
    }
}