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
    public string $deathdate;
    /** @var string */
    public string $biography;
    /** @var string */
    public string $summary;

    /**
     * @param string $name
     * @param string $birthdate
     * @param string $deathdate
     * @param string $biography
     * @param string $summary
     * @return Author
     */
    public static function createAuthor(
        string $name,
        string $birthdate = '',
        string $deathdate = '',
        string $biography = '',
        string $summary = ''
    ): Author {
        $author = new Author();

        $author->name = $name;
        $author->birthdate = $birthdate;
        $author->deathdate = $deathdate;
        $author->biography = $biography;
        $author->summary = $summary;

        return $author;
    }
}
