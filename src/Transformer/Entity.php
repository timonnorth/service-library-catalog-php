<?php

declare(strict_types=1);

namespace LibraryCatalog\Transformer;

class Entity
{
    /**
     * @param object $object
     * @return array
     */
    public function extractFields(object $object): array
    {
        return get_object_vars($object);
    }
}
