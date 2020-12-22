<?php

declare(strict_types=1);

namespace LibraryCatalog\Entity;

class Author
{
    /** @var mixed */
    public $id;
    /** @var string */
    public string $name;
    /** @var string */
    public string $birthdate;
    /** @var string */
    public ?string $deathdate;
    /** @var string */
    public ?string $biography;
    /** @var string */
    public ?string $summary;
    /** @var Book[] */
    public array $books;

    /** @var bool */
    protected bool $areBooksLoaded = false;

    /**
     * @param string $name
     * @param string $birthdate
     * @param string $deathdate
     * @param string $biography
     * @param string $summary
     * @return Author
     */
    public static function create(
        string $name,
        string $birthdate,
        string $deathdate = null,
        string $biography = null,
        string $summary = null
    ): Author {
        $author = new static();

        $author->name = $name;
        $author->birthdate = $birthdate;
        $author->deathdate = $deathdate;
        $author->biography = $biography;
        $author->summary = $summary;

        return $author;
    }

    /**
     * @return bool
     */
    public function areBooksLoaded(): bool
    {
        return $this->areBooksLoaded;
    }

    /**
     * @param Book[] $books
     * @return Author
     */
    public function setBooks(array $books): Author
    {
        $this->books = $books;
        $this->areBooksLoaded = true;

        return $this;
    }
}
