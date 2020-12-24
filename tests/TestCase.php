<?php

declare(strict_types=1);

namespace Tests;

use DI\Container;
use DI\ContainerBuilder;
use LibraryCatalog\Transformer\Serializer;
use Predis\Client;
use Psr\Http\Message\ResponseInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var Container */
    protected static Container $container;
    /** @var \PDO */
    protected static ?\PDO $pdo;

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

        parent::__construct($name, $data, $dataName);
    }

    /**
     * @return Container
     * @throws \Exception
     */
    protected function getContainer(): Container
    {
        if (!isset(static::$container)) {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->addDefinitions(__DIR__ . '/config.php');

            static::$container = $containerBuilder->build();

            // Prepare Redis mock.
            static::setRedisMock();
        }

        return static::$container;
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
     * @param bool $clear
     * @throws \Exception
     */
    protected static function runDbMigration(bool $clear = false): void
    {
        require_once __APPDIR__ . "/app/migrations/20111101000145_CreateAuthorsTable.php";
        require_once __APPDIR__ . "/app/migrations/20111101000146_CreateBooksTable.php";

        if ($clear) {
            $sources = [
                (new \CreateBooksTable(0))->sqlDownSqlite(),
                (new \CreateAuthorsTable(0))->sqlDownSqlite(),
            ];
        } else {
            $sources = [
                (new \CreateAuthorsTable(0))->sqlUpSqlite(),
                (new \CreateBooksTable(0))->sqlUpSqlite(),
            ];
        }

        foreach ($sources as $sql) {
            $stmt = static::pdo()->prepare($sql);
            if ($stmt === false) {
                throw new \Exception(json_encode(static::pdo()->errorInfo()));
            }
            if (!$stmt->execute()) {
                throw new \Exception(json_encode(static::pdo()->errorInfo()));
            }
        }

        if ($clear) {
            // Clear redis.
            static::setRedisMock();
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
     */
    protected static function pdo(): \PDO
    {
        if (!isset(static::$pdo)) {
            static::$pdo = new \PDO(
                'sqlite::memory:',
                null,
                null,
                array(\PDO::ATTR_PERSISTENT => true),
            );
        }

        return static::$pdo;
    }

    /**
     * @return void
     */
    protected static function setRedisMock(): void
    {
        $factory          = new \M6Web\Component\RedisMock\RedisMockFactory();
        $myRedisMockClass = $factory->getAdapterClass('\Predis\Client');
        $myRedisMock      = new $myRedisMockClass([]);

        // Emulate flushall.
        $myRedisMock->del($myRedisMock->keys('*'));

        static::$container->set('Redis', $myRedisMock);
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
     * @return TestCase
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function setRawInput($body): TestCase
    {
        if (is_array($body)) {
            $body = json_encode($body);
        }
        $this->getContainer()->get('RawInput')->set($body);
        return $this;
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
