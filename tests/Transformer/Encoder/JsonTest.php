<?php

declare(strict_types=1);

namespace Tests\Transformer\Encoder;

use LibraryCatalog\Transformer\Encoder\Exception;
use LibraryCatalog\Transformer\Encoder\Json;

class JsonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Encode different types.
     */
    public function testEncodeOk()
    {
        $encoder = new Json();

        $str = "I am string";
        self::assertEquals('"I am string"', $encoder->encode($str));

        $int = 12345;
        self::assertEquals(12345, $encoder->encode($int));

        $ar = [123, 23 => "tiesto"];
        self::assertEquals('{"0":123,"23":"tiesto"}', $encoder->encode($ar));
    }

    /**
     * Decode different types.
     */
    public function testDecodeOk()
    {
        $encoder = new Json();

        self::assertEquals("I am string", $encoder->decode('"I am string"'));

        self::assertEquals(12345, $encoder->decode('12345'));

        self::assertEquals([123, 23 => "tiesto"], $encoder->decode('{"0":123,"23":"tiesto"}'));
    }

    /**
     * Test errors.
     */
    public function testEncodeException()
    {
        $recurs = new \stdClass();
        $recurs->inside = $recurs;
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Can not encode JSON");
        (new Json())->encode($recurs);
    }

    /**
     * Test errors.
     */
    public function testDecodeException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Can not decode JSON");
        (new Json())->decode("tiesto");
    }
}
