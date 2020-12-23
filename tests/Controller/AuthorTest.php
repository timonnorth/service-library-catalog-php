<?php

declare(strict_types=1);

namespace Tests\Controller;

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

    public function testNotAuthed()
    {
        $response = $this->route('GET', '/author/122');

        self::assertEquals(401, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Unauthorized","code":""}',
            (string)$response->getBody()
        );
    }

    public function testAuthorNotFound()
    {
        $response = $this->route('GET', '/author/122', $this->getAuthorization('3'));

        self::assertEquals(404, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Author not found","code":""}',
            (string)$response->getBody()
        );
    }
}
