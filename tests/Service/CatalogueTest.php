<?php

declare(strict_types=1);

namespace Tests\Service;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Service\Catalogue;
use Tests\Entity\AuthorTrait;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use AuthorTrait;

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
        self::assertEquals($this->getSerializer()->serialize($authorExpected), $this->getSerializer()->serialize($author));
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
