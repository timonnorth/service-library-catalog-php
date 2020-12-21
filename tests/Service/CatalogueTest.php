<?php

declare(strict_types=1);

namespace Tests\Service;

use LibraryCatalog\Entity\Book;
use LibraryCatalog\Service\Catalogue;
use Tests\Entity\AuthorTrait;
use Tests\Entity\BookTrait;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use AuthorTrait;
    use BookTrait;

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
        self::assertNull($this->getCatalogue()->fetchAuthor(1));
    }

    public function testCreateAuthorOk()
    {
        $author1 = $this->createAuthor1();
        $author1->name = "Changed Bob";
        $this->getCatalogue()->createAuthor($author1);
        self::assertEquals(1, $author1->id);
    }

    public function testFetchAuthorOk()
    {
        $authorExpected = $this->createAuthor1();
        $author = $this->getCatalogue()->fetchAuthor(1);
        self::assertNotEquals($authorExpected->name, $author->name);
        $authorExpected->name = "Changed Bob";
        self::assertJsonStringEqualsJsonString($this->getSerializer()->serialize($authorExpected), $this->getSerializer()->serialize($author));
    }

    public function testBookNotFound()
    {
        self::assertNull($this->getCatalogue()->fetchBook(1));
    }

    public function testCreateBooks()
    {
        // Additional author for books.
        $author = $this->createAuthor1(2);
        $this->getCatalogue()->createAuthor($author);

        // Two Books.
        $book1 = $this->createBook1((string)$author->id);
        $this->getCatalogue()->createBook($book1);
        self::assertEquals(1, $book1->id);
        $book2 = $this->createBook2((string)$author->id);
        $this->getCatalogue()->createBook($book2);
        self::assertEquals(2, $book2->id);

        // And third book for first test author.
        $book3 = Book::create('1', 'third book');
        $book3->id = 3;
        $this->getCatalogue()->createBook($book3);
        self::assertEquals(3, $book3->id);
    }

    public function testFetchAuthorWithBooks()
    {
        $author = $this->getCatalogue()->fetchAuthor(2, true);
        $authorExpected = $this->createAuthor1("2");
        $dataExpected = json_decode($this->getSerializer()->serialize($authorExpected), true);
        $dataExpected['books'] = [
            // Alphabetic order for result!
            json_decode($this->getSerializer()->serialize($this->createBook2(2)), true),
            json_decode($this->getSerializer()->serialize($this->createBook1(2)), true),
        ];
        self::assertJsonStringEqualsJsonString(json_encode($dataExpected), $this->getSerializer()->serialize($author));
    }

    /**
     * @return Catalogue
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function getCatalogue(): Catalogue
    {
        return $this->getContainer()->get('Catalogue');
    }
}
