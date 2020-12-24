<?php

declare(strict_types=1);

namespace LibraryCatalog\Exception;

class HttpUnauthorizedException extends AppException
{
    public function __construct($message = "", $code = 0, ?\Throwable $previous = null)
    {
        if ($message == '') {
            $message = 'Unauthorized';
        }
        parent::__construct($message, $code, $previous);
    }
}
