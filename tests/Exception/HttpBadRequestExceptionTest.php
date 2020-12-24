<?php

declare(strict_types=1);

namespace Tests\Exception;

use LibraryCatalog\Exception\HttpBadRequestException;

class HttpBadRequestExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test message created.
     */
    public function testAutoMessage()
    {
        self::assertEquals('Bad request', (new HttpBadRequestException())->getMessage());
    }

    /**
     * Test message passed.
     */
    public function testManualMessage()
    {
        self::assertEquals('Tiesto', (new HttpBadRequestException('Tiesto'))->getMessage());
    }
}
