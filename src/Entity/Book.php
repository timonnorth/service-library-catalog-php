<?php

declare(strict_types=1);

namespace LibraryCatalog\Entity;

class Book
{
    /** @var mixed */
    public $id;
    /** @var string */
    public string $title;
    /** @var string */
    public ?string $summary;
    /** @var mixed */
    public $authorId;
    /** @var ?Author */
    public Author $author;

    /** @var bool */
    protected bool $isAuthorLoaded = false;

    /**
     * @param mixed $authorId
     * @param string $title
     * @param string|null $summary
     * @return Book
     */
    public static function create($authorId, string $title, string $summary = null): Book
    {
        $book = new static();

        $book->authorId = $authorId;
        $book->title = $title;
        $book->summary = $summary;

        return $book;
    }

    /**
     * @return bool
     */
    public function isAuthorLoaded(): bool
    {
        return $this->isAuthorLoaded;
    }

    /**
     * @param Author $author
     * @return Book
     */
    public function setAuthor(Author $author): Book
    {
        $this->author = $author;
        $this->isAuthorLoaded = true;
        return $this;
    }
}
