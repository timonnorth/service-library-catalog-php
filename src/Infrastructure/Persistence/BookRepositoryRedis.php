<?php

declare(strict_types=1);

namespace LibraryCatalog\Infrastructure\Persistence;

use LibraryCatalog\Entity\Book;
use LibraryCatalog\Service\Repository\BookRepositoryInterface;
use LibraryCatalog\Service\Repository\SerializerTrait;
use LibraryCatalog\Service\Repository\WarmRepositoryInterface;
use LibraryCatalog\Transformer\Serializer;
use Predis\Client;

class BookRepositoryRedis implements BookRepositoryInterface, WarmRepositoryInterface
{
    use SerializerTrait;

    /** @var BookRepositoryInterface|null */
    protected ?BookRepositoryInterface $parentRepository;
    /** @var Client */
    protected Client $client;
    /** @var string */
    protected string $keyPrefix;

    public function __construct(
        ?BookRepositoryInterface $parentRepository,
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
     * @param bool $withAuthor
     * @return Book|null
     * @throws Serializer\Exception
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     */
    public function load($id, bool $withAuthor = false): ?Book
    {
        if ($id == '') {
            $book = null;
        } else {
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
        }

        if (!$book && $this->parentRepository) {
            // We use parent Repository (usually DB) if data is not present or has invalidated by prefix.
            $book = $this->parentRepository->load($id);
            // And after that we can warm our temporary-storage.
            if ($book) {
                try {
                    $this->save($book);
                } catch (\LibraryCatalog\Service\Repository\Exception $e) {
                    // @todo Log
                    // Return result as we can live with parent storage only.
                }
            }
        }

        return $book;
    }

    /**
     * @param Book $book
     * @return void
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     * @throws \LibraryCatalog\Service\Repository\Exception
     */
    public function save(Book $book): void
    {
        if ($book->id) {
            if (!$this->client->set(
                $this->formatKey($book->id, $book->isAuthorLoaded()),
                $this->serializer->serialize($book)
            )) {
                throw new \LibraryCatalog\Service\Repository\Exception("Can not save Book to the Redis");
            }
        }
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
