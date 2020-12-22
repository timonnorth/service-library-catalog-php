<?php

declare(strict_types=1);

namespace LibraryCatalog\Infrastructure\Persistence;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Service\Repository\AuthorRepositoryInterface;
use LibraryCatalog\Service\Repository\SerializerTrait;
use LibraryCatalog\Service\Repository\WarmRepositoryInterface;
use LibraryCatalog\Transformer\Serializer;
use Predis\Client;

class AuthorRepositoryRedis implements AuthorRepositoryInterface, WarmRepositoryInterface
{
    use SerializerTrait;

    /** @var AuthorRepositoryInterface|null */
    protected ?AuthorRepositoryInterface $parentRepository;
    /** @var Client */
    protected Client $client;
    /** @var string */
    protected string $keyPrefix;

    public function __construct(
        ?AuthorRepositoryInterface $parentRepository,
        Serializer $serializer,
        string $redisParams,
        string $versionPrefix
    ) {
        $this->parentRepository = $parentRepository;
        $this->serializer = $serializer;
        $this->client = new Client($redisParams);
        $this->keyPrefix = $versionPrefix === '' ?: $versionPrefix . '-';
    }

    /**
     * @param mixed $id
     * @param bool $withBooks
     * @return Author|null
     * @throws Serializer\Exception
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     */
    public function load($id, bool $withBooks = false): ?Author
    {
        if ($id == '') {
            $author = null;
        } else {
            $data = $this->client->get($this->formatKey($id, $withBooks));
            if ($withBooks && !$data) {
                // Try to load from cache but without books.
                $data = $this->client->get($this->formatKey($id, false));
                $wasReloaded = true;
            }

            $author = $this->deserialize($data, Author::class);

            if ($withBooks && !isset($wasReloaded) && $author) {
                // It means we got books from cache also, should mark in the entity.
                $author->setBooks($author->books);
            }
        }
        if (!$author && $this->parentRepository) {
            // We use parent Repository (usually DB) if data is not present or has invalidated by prefix.
            $author = $this->parentRepository->load($id);
            // And after that we can warm our temporary-storage.
            if ($author) {
                try {
                    $this->save($author);
                } catch (\LibraryCatalog\Service\Repository\Exception $e) {
                    // @todo Log
                    // Return result as we can live with parent storage only.
                }
            }
        }
        return $author;
    }

    /**
     * @param Author $author
     * @return void
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     * @throws \LibraryCatalog\Service\Repository\Exception
     */
    public function save(Author $author): void
    {
        if ($author->id) {
            if (!$this->client->set(
                $this->formatKey($author->id, $author->areBooksLoaded()),
                $this->serializer->serialize($author)
            )) {
                throw new \LibraryCatalog\Service\Repository\Exception("Can not save Author to the Redis");
            }
        }
    }

    /**
     * @param mixed $id
     * @param bool $withIncludes
     * @return string
     */
    protected function formatKey($id, bool $withIncludes): string
    {
        $res = $this->keyPrefix . 'author-';
        if ($withIncludes) {
            $res .= 'wi-';
        }
        return $res . (string)$id;
    }
}
