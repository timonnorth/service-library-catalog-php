<?php

declare(strict_types=1);

namespace Tests\Transformer\Encoder;

use LibraryCatalog\Transformer\Encoder\Exception;

class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test message created.
     */
    public function testAutoMessage()
    {
        self::assertEquals('Can not encode/decode JSON', (new Exception())->getMessage());
    }

    /**
     * Test message passed.
     */
    public function testManualMessage()
    {
        self::assertEquals('Tiesto', (new Exception('Tiesto'))->getMessage());
    }
}
