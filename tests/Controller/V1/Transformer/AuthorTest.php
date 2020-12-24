<?php

declare(strict_types=1);

namespace Tests\Controller\V1\Transformer;

use LibraryCatalog\Controller\TransformerException;
use LibraryCatalog\Controller\V1\Transformer\Author;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    public function testTransformerException()
    {
        $transformer = new Author();
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage('Tried to transform not an Author');
        $transformer->transform('test');
    }
}
