<?php

declare(strict_types=1);

namespace Tests\Entity;

use LibraryCatalog\Entity\Book;

trait BookTrait
{
    /**
     * @param int $authorId
     * @param string $id
     * @return Book
     */
    protected function createBook1($authorId = 1, $id = '1'): Book
    {
        $book = Book::create(
            (string)$authorId,
            "Dombey and Son",
            "Dombey and Son is a novel by English author Charles Dickens. It follows the fortunes of a shipping firm owner, who is frustrated at the lack of a son to follow him in his footsteps; he initially rejects his daughter’s love before eventually becoming reconciled with her before his death.",
            );
        // For SQLITE we should set ID, in Mysql it'll not be necessary.
        $book->id = $id;
        return $book;
    }

    /**
     * @param int $authorId
     * @param string $id
     * @return Book
     */
    protected function createBook2($authorId = 1, $id = '2'): Book
    {
        $book = Book::create(
            (string)$authorId,
            "David Copperfield",
            "David Copperfield is the eighth novel by Charles Dickens. The novel's full title is The Personal History, Adventures, Experience and Observation of David Copperfield the Younger of Blunderstone Rookery (Which He Never Meant to Publish on Any Account).[N 1] It was first published as a serial in 1849–50, and as a book in 1850.",
            );
        // For SQLITE we should set ID, in Mysql it'll not be necessary.
        $book->id = $id;
        return $book;
    }
}
