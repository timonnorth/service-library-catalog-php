<?php

declare(strict_types=1);

namespace LibraryCatalog\Transformer\Serializer;

use Throwable;

class HydrateException extends Exception
{
    /**
     * HydrateException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, ?Throwable $previous = null)
    {
        if ($message == '') {
            $message = 'Can not hydrate object';
        }
        parent::__construct($message, $code, $previous);
    }
}
