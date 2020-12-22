<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Repository;

interface WarmRepositoryInterface
{
    /**
     * Warm (save) object only in current repository (not in parent).
     *
     * @param object $object
     */
    public function warm(object $object): void;
}
