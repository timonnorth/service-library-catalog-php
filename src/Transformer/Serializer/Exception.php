<?php

declare(strict_types=1);

namespace LibraryCatalog\Transformer\Serializer;

use LibraryCatalog\Exception\AppException;
use Throwable;

class Exception extends AppException
{
    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, ?Throwable $previous = null)
    {
        if ($message == '') {
            $message = 'Can not serialize/deserialize object';
        }
        if ($code == 0) {
            $code = 'serializer.error';
        }
        parent::__construct($message, $code, $previous);
    }
}
