<?php

declare(strict_types=1);

namespace LibraryCatalog\Service\Repository;

interface WarmRepositoryInterface
{
    /**
     * Warm (save) object only in current repository (not in parent non-warm).
     *
     * @param object $object
     */
    public function warm(object $object): void;

    /**
     * Rest object by id (invalidate cache) in current repository (not in parent non-warm).
     *
     * @param mixed $id
     */
    public function reset($id): void;
}
