<?php

declare(strict_types=1);

namespace LibraryCatalog\Service;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Entity\Book;
use LibraryCatalog\Repository\AuthorRepositoryInterface;
use LibraryCatalog\Repository\BookRepositoryInterface;

class Catalogue
{
    /** @var AuthorRepositoryInterface */
    protected AuthorRepositoryInterface $authorRepository;
    /** @var BookRepositoryInterface */
    protected BookRepositoryInterface $bookRepository;

    /**
     * Catalogue constructor.
     * @param AuthorRepositoryInterface $authorRepository
     * @param BookRepositoryInterface $bookRepository
     */
    public function __construct(
        AuthorRepositoryInterface $authorRepository,
        BookRepositoryInterface $bookRepository
    ) {
        $this->authorRepository = $authorRepository;
        $this->bookRepository = $bookRepository;
    }

    /**
     * @param $id
     * @param bool $withBooks
     * @return Author|null
     */
    public function fetchAuthor($id, bool $withBooks = false): ?Author
    {
        return $this->authorRepository->load($id, $withBooks);
    }

    /**
     * @param Author $author
     */
    public function createAuthor(Author $author): void
    {
        $this->authorRepository->save($author);
    }

    /**
     * @param $id
     * @return Book|null
     */
    public function fetchBook($id): ?Book
    {
        return $this->bookRepository->load($id);
    }

    /**
     * @param Book $book
     */
    public function createBook(Book $book): void
    {
        $this->bookRepository->save($book);
    }
}
