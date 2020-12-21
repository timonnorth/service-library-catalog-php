<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\ValueObject;

class Status
{
    /** @var string */
    public string $status;

    public static function create(string $status): Status
    {
        $object = new static();
        $object->status = _($status);

        return $object;
    }
}
