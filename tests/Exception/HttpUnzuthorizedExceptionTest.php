<?php

declare(strict_types=1);

namespace Tests\Exception;

use LibraryCatalog\Exception\HttpUnauthorizedException;

class HttpUnzuthorizedExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test message created.
     */
    public function testAutoMessage()
    {
        self::assertEquals('Unauthorized', (new HttpUnauthorizedException())->getMessage());
    }

    /**
     * Test message passed.
     */
    public function testManualMessage()
    {
        self::assertEquals('Tiesto', (new HttpUnauthorizedException('Tiesto'))->getMessage());
    }
}
