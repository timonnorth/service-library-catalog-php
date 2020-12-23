<?php

require_once "Repository/AuthorRepositoryPdoSqlite.php";
require_once "Repository/BookRepositoryPdoSqlite.php";

return [
    'Json' => \DI\create(LibraryCatalog\Transformer\Encoder\Json::class),
    'HttpTransformer' => \DI\create(LibraryCatalog\Transformer\JsonHttpSerializer::class)
        ->constructor(new LibraryCatalog\Transformer\Encoder\Json()),
    'Serializer' => \DI\create(LibraryCatalog\Transformer\Serializer::class)
        ->constructor(new LibraryCatalog\Transformer\Encoder\Json()),
    'AuthorRepository' => \Di\create(Repository\AuthorRepositoryPdoSqlite::class)
        ->constructor(\Di\get('Serializer'), "", "", "", ""),
    'BookRepository' => \Di\create(Repository\BookRepositoryPdoSqlite::class)
        ->constructor(\Di\get('Serializer'), "", "", "", ""),
    'Catalogue' => \DI\create(LibraryCatalog\Service\Catalogue::class)
        ->constructor(
            \Di\get('AuthorRepository'),
            \Di\get('BookRepository'),
        ),
    'AuthIn' => \DI\create(\LibraryCatalog\Service\AuthInBearer::class)
        ->constructor('test_secret'),
    'Acl' => \DI\create(\LibraryCatalog\Service\Acl::class),
];
