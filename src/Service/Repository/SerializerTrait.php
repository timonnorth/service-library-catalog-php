<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Repository;

use LibraryCatalog\Transformer\Serializer;

trait SerializerTrait
{
    /** @var Serializer */
    protected Serializer $serializer;

    /**
     * @param $data
     * @param string $classname
     * @return object|null
     * @throws Serializer\Exception
     * @throws Serializer\HydrateException
     * @throws \LibraryCatalog\Transformer\Encoder\Exception
     */
    protected function deserialize($data, string $classname): ?object
    {
        $object = null;

        if ($data != '') {
            $object = $this->serializer->deserialize((string)$data);

            if (!($object instanceof $classname)) {
                // Do not generate error, just ignore not correct value.
                $object = null;
            }
        }

        return $object;
    }
}
