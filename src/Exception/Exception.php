<?php

declare(strict_types=1);

namespace Exception;

class AppException extends \Exception
{
    /**
     * @param mixed $code
     * @return AppException
     */
    public function setCode($code): AppException
    {
        $this->code = $code;
        return $this;
    }
}
