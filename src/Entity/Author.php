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
     * @param array $data
     * @return Author
     */
    public function fill(array $data): Author
    {
        if (isset($data['name'])) {
            $this->name = (string)$data['name'];
        }
        if (isset($data['birthdate'])) {
            $this->birthdate = (string)$data['birthdate'];
        }
        if (isset($data['deathdate'])) {
            $this->deathdate = (string)$data['deathdate'];
        }
        if (isset($data['biography'])) {
            $this->biography = (string)$data['biography'];
        }
        if (isset($data['summary'])) {
            $this->summary = (string)$data['summary'];
        }
        return $this;
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
