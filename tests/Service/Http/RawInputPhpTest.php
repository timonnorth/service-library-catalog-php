<?php

declare(strict_types=1);

namespace Tests\Service\Http;

use LibraryCatalog\Service\Http\RawInputPhp;
use Tests\TestCase;

class RawInputPhpTest extends TestCase
{
    public function testRawInput()
    {
        $rawInput = new RawInputPhp();
        $this->assertEquals('', $rawInput->get());
    }
}
