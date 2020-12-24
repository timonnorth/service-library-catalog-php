<?php

declare(strict_types=1);

namespace LibraryCatalog\Exception;

class HttpBadRequestException extends AppException
{
    public function __construct($message = "", $code = 0, ?\Throwable $previous = null)
    {
        if ($message == '') {
            $message = 'Bad request';
        }
        parent::__construct($message, $code, $previous);
    }
}
