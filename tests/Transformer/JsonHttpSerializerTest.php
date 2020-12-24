<?php

declare(strict_types=1);

namespace Tests\Transformer;

use LibraryCatalog\Transformer\JsonHttpSerializer;
use Tests\TestCase;

class JsonHttpSerializerTest extends TestCase
{
    public function testResponseHeaders()
    {
        $serializer = new JsonHttpSerializer($this->getContainer('Json')->get('Json'));
        self::assertEquals(['Content-Type' => 'application/json'], $serializer->getResponseHeaders());
    }
}
