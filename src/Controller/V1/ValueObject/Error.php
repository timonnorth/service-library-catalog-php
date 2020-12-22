<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1\ValueObject;

class Error
{
    /** @var string */
    public string $message;
    /** @var string */
    public string $code;

    /**
     * @param string $message
     * @param string $code
     * @return Error
     */
    public static function create(string $message, string $code = ''): Error
    {
        $object = new static();
        $object->message = _($message);
        $object->code = $code;

        return $object;
    }
}
