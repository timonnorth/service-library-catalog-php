<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller;

use LibraryCatalog\Exception\AppException;

class TransformerException extends AppException
{
    public function __construct($message = "", $code = 0, ?\Throwable $previous = null)
    {
        if ($message == '') {
            $message = 'Can not transform object';
        }
        parent::__construct($message, $code, $previous);
    }
}
