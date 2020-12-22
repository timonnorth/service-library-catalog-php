<?php

declare(strict_types=1);

namespace LibraryCatalog\Service;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Entity\Book;
use LibraryCatalog\Service\Repository\AuthorRepositoryInterface;
use LibraryCatalog\Service\Repository\BookRepositoryInterface;
use LibraryCatalog\Service\Repository\WarmRepositoryInterface;

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
        $author = $this->authorRepository->load($id, $withBooks);
        if ($author && $withBooks && !$author->areBooksLoaded()) {
            // Load and set books for Author.
            $author->setBooks($this->bookRepository->loadByAuthorId($author->id));
            if ($this->authorRepository instanceof WarmRepositoryInterface) {
                // We can warm cache-repository with books.
                $this->authorRepository->save($author);
            }
        }
        return $author;
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
     * @param bool $withAuthor
     * @return Book|null
     */
    public function fetchBook($id, bool $withAuthor = false): ?Book
    {
        $book = $this->bookRepository->load($id, true);
        if ($book && $withAuthor && !$book->isAuthorLoaded()) {
            // Load and set author for book.
            $book->setAuthor($this->authorRepository->load($book->authorId));
            if ($this->bookRepository instanceof WarmRepositoryInterface) {
                // We can warm cache-repository with author.
                $this->bookRepository->save($book);
            }
        }
        return $book;
    }

    /**
     * @param Book $book
     */
    public function createBook(Book $book): void
    {
        $this->bookRepository->save($book);
    }
}
