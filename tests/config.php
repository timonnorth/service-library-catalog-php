<?php

require_once "Repository/AuthorRepositoryPdoSqlite.php";
require_once "Repository/BookRepositoryPdoSqlite.php";

return [
    'RawInput' => \DI\create(\LibraryCatalog\Service\Http\RawInputDummy::class),
    'Json' => \DI\create(LibraryCatalog\Transformer\Encoder\Json::class),
    'HttpTransformer' => \DI\create(LibraryCatalog\Transformer\JsonHttpSerializer::class)
        ->constructor(new LibraryCatalog\Transformer\Encoder\Json()),
    'Serializer' => \DI\create(LibraryCatalog\Transformer\Serializer::class)
        ->constructor(new LibraryCatalog\Transformer\Encoder\Json()),
    'Redis' => \DI\create(\Predis\Client::class),
    'AuthorRepositoryPdo' => \Di\create(Repository\AuthorRepositoryPdoSqlite::class)
        ->constructor(\Di\get('Serializer'), "", "", "", ""),
    'AuthorRepositoryRedis' => \Di\create(\LibraryCatalog\Infrastructure\Persistence\AuthorRepositoryRedis::class)
        ->constructor(
            \Di\get('AuthorRepositoryPdo'),
            \Di\get('Serializer'),
            \Di\get('Redis'),
            '1',
            ),
    'BookRepositoryPdo' => \Di\create(Repository\BookRepositoryPdoSqlite::class)
        ->constructor(\Di\get('Serializer'), "", "", "", ""),
    'BookRepositoryRedis' => \Di\create(\LibraryCatalog\Infrastructure\Persistence\BookRepositoryRedis::class)
        ->constructor(
            \Di\get('BookRepositoryPdo'),
            \Di\get('Serializer'),
            \Di\get('Redis'),
            '1',
            ),
    'Catalogue' => \DI\create(LibraryCatalog\Service\Catalogue::class)
        ->constructor(
            \Di\get('AuthorRepositoryRedis'),
            \Di\get('BookRepositoryRedis'),
        ),
    'AuthIn' => \DI\create(\LibraryCatalog\Service\AuthInBearer::class)
        ->constructor('test_secret'),
    'Acl' => \DI\create(\LibraryCatalog\Service\Acl::class),
];
