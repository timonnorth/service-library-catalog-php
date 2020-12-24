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

            // Prepare Redis mock.
            $factory          = new \M6Web\Component\RedisMock\RedisMockFactory();
            $myRedisMockClass = $factory->getAdapterClass('\Predis\Client');
            $myRedisMock      = new $myRedisMockClass([]);
            $this->container->set('Redis', $myRedisMock);
        }

        return $this->container;
    }

    /**
     * @param string $httpMethod
     * @param string $uri
     * @param string $auth;
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function route(string $httpMethod, string $uri, string $auth = ''): ResponseInterface
    {
        $params = [];
        if ($auth !== '') {
            $params['HTTP_AUTHORIZATION'] = $auth;
        }
        $router = new \LibraryCatalog\Service\Http\Router($this->getContainer(), $uri, $httpMethod, $params);
        return $router->dispatch();
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
     * Get Auth string. Do it before controller/action.
     *
     * @param string $payload
     * @param string $secret
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @return string
     */
    protected function getAuthorization(string $payload, string $secret = 'test_secret'): string
    {
        return 'Bearer ' . base64_encode(json_encode(['secret' => $secret, 'payload' => $payload]));
    }

    /**
     * @return \PDO
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function pdo(): \PDO
    {
        return $this->getContainer()->get('AuthorRepositoryPdo')->pdo();
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

    /**
     * @param string|array $body
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function setRawInput($body)
    {
        if (is_array($body)) {
            $body = json_encode($body);
        }
        $this->container->get('RawInput')->set($body);
    }

    /**
     * @param int $length
     * @return string
     */
    protected function generateString(int $length): string
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', (int)ceil($length/strlen($x)) )),1,$length);
    }
}
