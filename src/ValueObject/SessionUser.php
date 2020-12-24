<?php

declare(strict_types=1);

namespace LibraryCatalog\ValueObject;

class SessionUser
{
    /** @var string */
    public string $roleId;
    /** @var string */
    public string $userId;

    /**
     * @param string $payload
     * @return SessionUser
     */
    public static function createFromPayload(string $payload): SessionUser
    {
        $res = new static();
        $ar = explode(':', $payload);
        if (is_array($ar)) {
            if (isset($ar[0])) {
                $res->roleId = (string)$ar[0];
            }
            if (isset($ar[1])) {
                $res->userId = (string)$ar[1];
            }
        }
        return $res;
    }
}
