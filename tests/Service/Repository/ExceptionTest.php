<?php

declare(strict_types=1);

namespace Tests\Service\Repository;

use LibraryCatalog\Service\Repository\Exception;

class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test message created.
     */
    public function testAutoMessage()
    {
        self::assertEquals('Repository exception', (new Exception())->getMessage());
    }

    /**
     * Test message passed.
     */
    public function testManualMessage()
    {
        self::assertEquals('Tiesto', (new Exception('Tiesto'))->getMessage());
    }
}
