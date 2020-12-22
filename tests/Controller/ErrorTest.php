<?php

declare(strict_types=1);

namespace Tests\Controller;

class ErrorTest extends \PHPUnit\Framework\TestCase
{
    public function testErrorNotFound()
    {
        self::assertEquals(1, 1);
    }
}
