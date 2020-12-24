<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Validation\Rule;

use LibraryCatalog\Service\Repository\AuthorRepositoryInterface;
use Rakit\Validation\Rule;

class AuthorUniqueName extends Rule
{
    /** @var string */
    protected $message = "Name and birthdate should be unique";
    /** @var AuthorRepositoryInterface */
    protected AuthorRepositoryInterface $authorRepository;
    /** @var string */
    protected string $birthdate;

    /**
     * AuthorUniqueName constructor.
     * @param AuthorRepositoryInterface $authorRepository
     * @param string $birthdate
     */
    public function __construct(AuthorRepositoryInterface $authorRepository, string $birthdate)
    {
        $this->authorRepository = $authorRepository;
        $this->birthdate = $birthdate;
    }

    /**
     * @param $value
     * @return bool
     */
    public function check($value): bool
    {
        if ($this->birthdate === '') {
            return true;
        }

        return $this->authorRepository->loadByNameBirthdate((string)$value, $this->birthdate) === null;
    }
}
