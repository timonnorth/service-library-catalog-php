<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\ValueObject;

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
        $error = new static();
        $error->message = _($message);
        $error->code = $code;

        return $error;
    }
}
