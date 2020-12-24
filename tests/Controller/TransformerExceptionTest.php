<?php

declare(strict_types=1);

namespace Tests\Transformer\Encoder;

use LibraryCatalog\Controller\TransformerException as Exception;

class TransformerExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test message created.
     */
    public function testAutoMessage()
    {
        self::assertEquals('Can not transform object', (new Exception())->getMessage());
    }

    /**
     * Test message passed.
     */
    public function testManualMessage()
    {
        self::assertEquals('Tiesto', (new Exception('Tiesto'))->getMessage());
    }
}
