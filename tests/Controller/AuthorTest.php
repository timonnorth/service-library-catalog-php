<?php

declare(strict_types=1);

namespace Tests\Transformer;

use Tests\TestCase;

class AuthorTest extends TestCase
{
    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->runDbMigration();
    }

    public function testAuthorNotFound()
    {
        $response = $this->route('GET', '/author/122');

        self::assertEquals(404, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Author not found","code":""}',
            (string)$response->getBody()
        );
    }
}
