<?php

declare(strict_types=1);

namespace LibraryCatalog\Infrastructure\Persistence;

use LibraryCatalog\Entity\Book;
use LibraryCatalog\Service\Repository\BookRepositoryInterface;
use LibraryCatalog\Service\Repository\RedisTrait;
use LibraryCatalog\Service\Repository\SerializerTrait;
use LibraryCatalog\Service\Repository\WarmRepositoryInterface;
use LibraryCatalog\Transformer\Serializer;
use Predis\Client;

class BookRepositoryRedis implements BookRepositoryInterface, WarmRepositoryInterface
{
    use SerializerTrait;
    use RedisTrait;

    protected const LOCK_TTL = 5;

    /** @var BookRepositoryInterface|null */
    protected ?BookRepositoryInterface $parentRepository;
    /** @var Client */
    protected Client $client;
    /** @var string */
    protected string $keyPrefix;

    public function __construct(
        ?BookRepositoryInterface $parentRepository,
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
     * @param bool $withAuthor
     * @return Book|null
     * @throws Serializer\Exception
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     * @throws \Throwable
     */
    public function load($id, bool $withAuthor = false): ?Book
    {
        if ($id == '') {
            return null;
        }

        $data = $this->client->get($this->formatKey($id, $withAuthor));
        if ($withAuthor && !$data) {
            // Try to load from cache but without author.
            $data = $this->client->get($this->formatKey($id, false));
            $wasReloaded = true;
        }

        $book = $this->deserialize($data, Book::class);

        if ($withAuthor && !isset($wasReloaded) && $book) {
            // It means we got books from cache also, should mark in the entity.
            $book->setAuthor($book->author);
        }

        // We use parent Repository (usually DB) if data is not present or has invalidated by prefix.
        if (!$book && $this->parentRepository) {
            // Implement lock to make other request waiting for cache warming.
            $book = $this->transaction(
                $this->client,
                $this->keyPrefix . 'lock-book-' . $id,
                static::LOCK_TTL,
                function () use ($id) {
                    $book = $this->parentRepository->load($id);
                    // And after that we can warm our temporary-storage.
                    if ($book) {
                        try {
                            $this->saveInternal($book);
                        } catch (\LibraryCatalog\Service\Repository\Exception $e) {
                            // @todo Log
                            // Return result as we can live with parent storage only.
                        }
                    }
                    return $book;
                }
            );
        }

        return $book;
    }

    /**
     * @param mixed $authorId
     * @return Book[]
     */
    public function loadByAuthorId($authorId): array
    {
        // As a cache repository it does not search.
        return $this->parentRepository->loadByAuthorId($authorId);
    }

    /**
     * @param Book $book
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Service\Repository\Exception
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     */
    public function save(Book $book): void
    {
        // First save to parent repository (DB).
        if ($this->parentRepository) {
            $this->parentRepository->save($book);
        }
        $this->saveInternal($book);
    }

    /**
     * @param object $object
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Service\Repository\Exception
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     */
    public function warm(object $object): void
    {
        if ($object instanceof Book) {
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
     * @param Book $book
     * @return void
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     * @throws \LibraryCatalog\Service\Repository\Exception
     */
    protected function saveInternal(Book $book): void
    {
        if ($book->id) {
            if (
                !$this->client->set(
                    $this->formatKey($book->id, $book->isAuthorLoaded()),
                    $this->serializer->serialize($book)
                )
            ) {
                throw new \LibraryCatalog\Service\Repository\Exception("Can not save Book to the Redis");
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
        $res = $this->keyPrefix . 'book-';
        if ($withIncludes) {
            $res .= 'wi-';
        }
        return $res . $id;
    }
}
