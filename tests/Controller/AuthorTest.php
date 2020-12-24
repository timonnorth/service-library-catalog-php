<?php

declare(strict_types=1);

namespace Tests\Controller;

use Tests\Entity\AuthorTrait;
use Tests\TestCaseMigration;

class AuthorTest extends TestCaseMigration
{
    use AuthorTrait;

    public function testNotAuthed()
    {
        $response = $this->route('GET', '/author/122');

        self::assertEquals(401, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Unauthorized","code":""}',
            (string)$response->getBody()
        );
    }

    public function testAuthorForbidden()
    {
        $response = $this->route('GET', '/author/122', $this->getAuthorization('unknown:3'));

        self::assertEquals(403, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Forbidden","code":""}',
            (string)$response->getBody()
        );
    }

    public function testAuthorNotFound()
    {
        $response = $this->route('GET', '/author/122', $this->getAuthorization('user:3'));

        self::assertEquals(404, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Author not found","code":""}',
            (string)$response->getBody()
        );
    }

    public function testCreateForbidden()
    {
        $this->setRawInput([]);
        $response = $this->route('POST', '/author', $this->getAuthorization('user:3'));
        self::assertEquals(403, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Forbidden","code":""}',
            (string)$response->getBody()
        );

        $response = $this->route('POST', '/author', $this->getAuthorization('guest:3'));
        self::assertEquals(403, $response->getStatusCode());

        $response = $this->route('POST', '/author', $this->getAuthorization('somebody:3'));
        self::assertEquals(403, $response->getStatusCode());
    }

    public function testCreateValidators()
    {
        $expected = [
            'message' => 'Validation error',
            'code' => '',
            'fields' => [
                "name" => "The Name maximum is 255",
                "birthdate" => "The Birthdate is required",
                "deathdate" => "The Deathdate is not valid date format",
                "biography" => "The Biography maximum is 65534",
                "summary" => "The Summary maximum is 65534",
            ],
        ];
        $response = $this->setRawInput([
            'name' => $this->generateString(256),
            'deathdate' => 'uno',
            'biography' => $this->generateString(65535),
            'summary' => $this->generateString(65535),
            'foo' => 'bar'
        ])->route('POST', '/author', $this->getAuthorization('admin:3'));

        self::assertEquals(400, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode($expected), (string)$response->getBody());
    }

    public function testCreateOk()
    {
        $authorExpected = $this->createAuthor1();
        $this->getContainer()->get('AuthorRepositoryPdo')->setIdForce(1);
        $input = $this->getSerializer()->extractFields($authorExpected);
        $response = $this->setRawInput($input)
            ->route('POST', '/author', $this->getAuthorization('admin:3'));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(get_object_vars($authorExpected)), (string)$response->getBody());
    }
}
