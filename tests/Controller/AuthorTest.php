<?php

declare(strict_types=1);

namespace Tests\Transformer;

use Tests\TestCase;

class AuthorTest extends TestCase
{
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
