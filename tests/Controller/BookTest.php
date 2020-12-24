<?php

declare(strict_types=1);

namespace Tests\Controller;

use Tests\Entity\AuthorTrait;
use Tests\Entity\BookTrait;
use Tests\TestCaseMigration;

class BookTest extends TestCaseMigration
{
    use AuthorTrait,
        BookTrait;

    public function testBookForbidden()
    {
        $response = $this->route('GET', '/book/122', $this->getAuthorization('unknown:3'));

        self::assertEquals(403, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Forbidden","code":""}',
            (string)$response->getBody()
        );
    }

    public function testBookNotFound()
    {
        $response = $this->route('GET', '/book/1', $this->getAuthorization('user:3'));

        self::assertEquals(404, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Book not found","code":""}',
            (string)$response->getBody()
        );
    }

    public function testCreateForbidden()
    {
        $this->setRawInput([]);
        $response = $this->route('POST', '/book', $this->getAuthorization('guest:3'));
        self::assertEquals(403, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"message":"Forbidden","code":""}',
            (string)$response->getBody()
        );

        $response = $this->route('POST', '/book', $this->getAuthorization('somebody:3'));
        self::assertEquals(403, $response->getStatusCode());
    }

    public function testCreateValidators()
    {
        $expected = [
            'message' => 'Validation error',
            'code' => '',
            'fields' => [
                "title" => "The Title maximum is 4096",
                "summary" => "The Summary maximum is 65534",
                "authorId" => "Author should exist",
            ],
        ];
        $response = $this->setRawInput([
            'title' => $this->generateString(4097),
            'summary' => $this->generateString(65535),
            'authorId' => 123,
            'foo' => 'bar'
        ])->route('POST', '/book', $this->getAuthorization('user:3'));

        self::assertEquals(400, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode($expected), (string)$response->getBody());
    }

    public function testCreateOk()
    {
        // Need some author first.
        $author = $this->createAuthor1();
        $this->getContainer()->get('Catalogue')->createAuthor($author);

        $bookExpected = $this->createBook1($author->id);
        $this->getContainer()->get('BookRepositoryPdo')->setIdForce(1);
        $input = $this->getSerializer()->extractFields($bookExpected);
        $response = $this->setRawInput($input)
            ->route('POST', '/book', $this->getAuthorization('user:3'));
        unset($bookExpected->authorId);

        self::assertJsonStringEqualsJsonString(json_encode(get_object_vars($bookExpected)), (string)$response->getBody());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testLoadAfterCreation()
    {
        // Books includes Author in GET.
        $bookExpected = $this->createBook1(1);
        $bookExpected->setAuthor($this->createAuthor1());

        $response = $this->route('GET', '/book/1', $this->getAuthorization('guest:'));
        unset($bookExpected->authorId);

        self::assertEquals(200, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(get_object_vars($bookExpected)), (string)$response->getBody());
    }

    public function testLoadAuthorWithBooks()
    {
        $authorExpected = $this->createAuthor1();
        $bookExpected = $this->createBook1($authorExpected->id);
        unset($bookExpected->authorId);
        $authorExpected->books = [$bookExpected];

        $response = $this->route('GET', '/author/1', $this->getAuthorization('guest:'));

        self::assertEquals(200, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(get_object_vars($authorExpected)), (string)$response->getBody());
    }
}
