<?php

declare(strict_types=1);

namespace Tests\Exception;

use LibraryCatalog\Exception\AppException;

class AppExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test message passed.
     */
    public function testManualMessage()
    {
        self::assertEquals('Tiesto', (new AppException('Tiesto'))->getMessage());
    }
}
