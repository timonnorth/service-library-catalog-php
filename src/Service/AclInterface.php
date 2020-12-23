<?php

declare(strict_types=1);

namespace LibraryCatalog\Service;

use LibraryCatalog\ValueObject\SessionUser;

interface AclInterface extends \Laminas\Permissions\Acl\AclInterface
{
    /**
     * Generate SessionUser from payload;
     * Payload example: "user:182".
     *
     * @param string $payload
     * @return SessionUser
     */
    public function parseUserPayload(string $payload): SessionUser;
}
