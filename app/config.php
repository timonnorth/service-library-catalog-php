<?php

return [
    'Json' => \DI\create(LibraryCatalog\Transformer\Encoder\Json::class),
    'SerializerEntity' => \DI\create(LibraryCatalog\Transformer\Entity::class),
    'Serializer' => \DI\create(LibraryCatalog\Transformer\Serializer::class)
        ->constructor(new LibraryCatalog\Transformer\Encoder\Json()),
    'AuthorRepositoryPdo' => \Di\create(LibraryCatalog\Repository\AuthorRepositoryPdo::class)
        ->constructor(
            \Di\get('SerializerEntity'),
            getenv('MYSQL_HOST'),
            getenv('MYSQL_USER'),
            getenv('MYSQL_PASSWORD'),
            getenv('MYSQL_DBNAME'),
        ),
    'Catalogue' => \DI\create(LibraryCatalog\Service\Catalogue::class)
        ->constructor(
            \Di\get('AuthorRepositoryPdo'),
        )
];
