<?php

declare(strict_types=1);

namespace LibraryCatalog\Controller\V1\ValueObject;

class ErrorWithValidation extends Error
{
    /** @var array */
    public array $fields;

    /**
     * @param array $fields
     * @return ErrorWithValidation
     */
    public function withFields(array $fields): ErrorWithValidation
    {
        $this->fields = [];
        foreach ($fields as $name => $value) {
            $this->fields[$name] = _($value);
        }

        return $this;
    }
}
