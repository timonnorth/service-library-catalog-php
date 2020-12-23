<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Validation\Rule;

use LibraryCatalog\Service\Repository\AuthorRepositoryInterface;
use Rakit\Validation\Rule;

class AuthorExists extends Rule
{
    /** @var string */
    protected $message = "Author should exist";
    /** @var AuthorRepositoryInterface */
    protected AuthorRepositoryInterface $authorRepository;

    /**
     * AuthorExists constructor.
     * @param AuthorRepositoryInterface $authorRepository
     */
    public function __construct(AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return $this->authorRepository->load($value) !== null;
    }
}
