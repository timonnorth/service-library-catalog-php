<?php

declare(strict_types=1);

namespace LibraryCatalog\Service;

use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\GenericRole;
use Laminas\Permissions\Acl\Role\RoleInterface;
use LibraryCatalog\ValueObject\SessionUser;

class Acl extends \Laminas\Permissions\Acl\Acl implements AclInterface
{
    // Roles.
    public const GUEST = 'guest';
    public const USER = 'user';
    public const ADMIN = 'admin';

    // Resources.
    public const BOOK = 'book';
    public const AUTHOR = 'author';

    // Privileges.
    public const READ = 'read';
    public const ADD = 'add';

    /**
     * Acl constructor.
     */
    public function __construct()
    {
        $guest = new GenericRole(static::GUEST);
        $user = new GenericRole(static::USER);
        $admin = new GenericRole(static::ADMIN);

        $book = new GenericResource(static::BOOK);
        $author = new GenericResource(static::AUTHOR);

        $this->addRole($guest)
            ->addRole($user, $guest)
            ->addRole($admin, $user)
            ->addResource($book)
            ->addResource($author);

        $this->allow($guest, [$book, $author], static::READ)
            ->allow($user, $book, static::ADD)
            ->allow($admin, $author, static::ADD);
    }

    /**
     * Generate SessionUser from payload;
     * Payload example: "user:182".
     *
     * @param string $payload
     * @return SessionUser
     */
    public function parseUserPayload(string $payload): SessionUser
    {
        return SessionUser::createFromPayload($payload);
    }

    /**
     * SessionUser can be used instead of Role.
     *
     * @param  RoleInterface|SessionUser|string $role
     * @param  ResourceInterface|string $resource
     * @param  string $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        // Autotransform SessionUser to RoleId.
        if ($role instanceof SessionUser) {
            $role = $role->roleId;
        }

        try {
            $res = parent::isAllowed($role, $resource, $privilege);
        } catch (\InvalidArgumentException $e) {
            $res = false;
        }
        return $res;
    }
}
