<?php

declare(strict_types=1);

namespace LibraryCatalog\Repository;

use LibraryCatalog\Exception\AppException;

class Exception extends AppException
{
    public function __construct($message = "", $code = 0, ?\Throwable $previous = null)
    {
        if ($message == '') {
            $message = 'Repository exception';
        }
        if ($code == 0) {
            $code = 'repository.error';
        }
        parent::__construct($message, $code, $previous);
    }
}
