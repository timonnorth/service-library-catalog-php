<?php

declare(strict_types=1);

namespace LibraryCatalog\Infrastructure\Persistence;

use LibraryCatalog\Entity\Author;
use LibraryCatalog\Service\Repository\AuthorRepositoryInterface;
use LibraryCatalog\Service\Repository\RedisTrait;
use LibraryCatalog\Service\Repository\SerializerTrait;
use LibraryCatalog\Service\Repository\WarmRepositoryInterface;
use LibraryCatalog\Transformer\Serializer;
use Predis\Client;

class AuthorRepositoryRedis implements AuthorRepositoryInterface, WarmRepositoryInterface
{
    use SerializerTrait;
    use RedisTrait;

    protected const LOCK_TTL = 5;

    /** @var AuthorRepositoryInterface|null */
    protected ?AuthorRepositoryInterface $parentRepository;
    /** @var Client */
    protected Client $client;
    /** @var string */
    protected string $keyPrefix;

    /**
     * AuthorRepositoryRedis constructor.
     * @param AuthorRepositoryInterface|null $parentRepository
     * @param Serializer $serializer
     * @param Client $client
     * @param string $versionPrefix
     */
    public function __construct(
        ?AuthorRepositoryInterface $parentRepository,
        Serializer $serializer,
        Client $client,
        string $versionPrefix
    ) {
        $this->parentRepository = $parentRepository;
        $this->serializer = $serializer;
        $this->client = $client;
        $this->keyPrefix = $versionPrefix === '' ?: $versionPrefix . '-';
    }

    /**
     * @param mixed $id
     * @param bool $withBooks
     * @return Author|null
     * @throws Serializer\Exception
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     * @throws \Exception
     * @throws \Throwable
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

        // We use parent Repository (usually DB) if data is not present or has invalidated by prefix.
        if (!$author && $this->parentRepository) {
            // Implement lock to make other request waiting for cache warming.
            $author = $this->transaction(
                $this->client,
                $this->keyPrefix . 'lock-author-' . $id,
                static::LOCK_TTL,
                function () use ($id) {
                    $author = $this->parentRepository->load($id);
                    // And after that we can warm our temporary-storage.
                    if ($author) {
                        try {
                            $this->saveInternal($author);
                        } catch (\LibraryCatalog\Service\Repository\Exception $e) {
                            // @todo Log
                            // Return result as we can live with parent storage only.
                        }
                    }
                    return $author;
                }
            );
        }

        return $author;
    }

    /**
     * @param string $name
     * @param string $birthdate
     * @return Author|null
     */
    public function loadByNameBirthdate(string $name, string $birthdate): ?Author
    {
        $res = null;
        if ($this->parentRepository) {
            $res = $this->parentRepository->loadByNameBirthdate($name, $birthdate);
        }
        return $res;
    }

    /**
     * @param Author $author
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Service\Repository\Exception
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     */
    public function save(Author $author): void
    {
        // First save to parent repository (DB).
        if ($this->parentRepository) {
            $this->parentRepository->save($author);
        }
        $this->saveInternal($author);
    }

    /**
     * @param object $object
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Service\Repository\Exception
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     */
    public function warm(object $object): void
    {
        if ($object instanceof Author) {
            $this->saveInternal($object);
        }
        if ($this->parentRepository instanceof WarmRepositoryInterface) {
            $this->parentRepository->warm($object);
        }
    }

    /**
     * @param mixed $id
     * @throws \LibraryCatalog\Service\Repository\Exception
     */
    public function reset($id): void
    {
        if ($id != '') {
            $this->client->del([
                $this->formatKey($id, true),
                $this->formatKey($id, false),
            ]);
            if ($this->parentRepository instanceof WarmRepositoryInterface) {
                $this->parentRepository->reset($id);
            }
        }
    }

    /**
     * Saves only in current repository.
     *
     * @param Author $author
     * @return void
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     * @throws \LibraryCatalog\Service\Repository\Exception
     */
    public function saveInternal(Author $author): void
    {
        if ($author->id) {
            if (
                !$this->client->set(
                    $this->formatKey($author->id, $author->areBooksLoaded()),
                    $this->serializer->serialize($author)
                )
            ) {
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
        return $res . $id;
    }
}
