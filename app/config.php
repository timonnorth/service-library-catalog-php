<?php

return [
    'Json' => \DI\create(LibraryCatalog\Transformer\Encoder\Json::class),
    'HttpTransformer' => \DI\create(LibraryCatalog\Transformer\JsonHttpSerializer::class)
        ->constructor(new LibraryCatalog\Transformer\Encoder\Json()),
    'Serializer' => \DI\create(LibraryCatalog\Transformer\Serializer::class)
        ->constructor(new LibraryCatalog\Transformer\Encoder\Json()),
    'AuthorRepositoryRedis' => \Di\create(LibraryCatalog\Infrastructure\Persistence\AuthorRepositoryRedis::class)
        ->constructor(
            \Di\get('AuthorRepositoryPdo'),
            \Di\get('Serializer'),
            getenv('REDIS_PARAMS'),
            '1'
            ),
    'AuthorRepositoryPdo' => \Di\create(LibraryCatalog\Infrastructure\Persistence\AuthorRepositoryPdo::class)
        ->constructor(
            \Di\get('Serializer'),
            getenv('MYSQL_HOST'),
            getenv('MYSQL_USER'),
            getenv('MYSQL_PASSWORD'),
            getenv('MYSQL_DBNAME'),
            ),
    'BookRepositoryPdo' => \Di\create(LibraryCatalog\Infrastructure\Persistence\BookRepositoryPdo::class)
        ->constructor(
            \Di\get('Serializer'),
            getenv('MYSQL_HOST'),
            getenv('MYSQL_USER'),
            getenv('MYSQL_PASSWORD'),
            getenv('MYSQL_DBNAME'),
            ),
    'Catalogue' => \DI\create(LibraryCatalog\Service\Catalogue::class)
        ->constructor(
            \Di\get('AuthorRepositoryRedis'),
            \Di\get('BookRepositoryPdo'),
        )
];
