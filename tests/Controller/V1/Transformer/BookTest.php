<?php

declare(strict_types=1);

namespace Tests\Controller\V1\Transformer;

use LibraryCatalog\Controller\TransformerException;
use LibraryCatalog\Controller\V1\Transformer\Book;
use Tests\TestCase;

class BookTest extends TestCase
{
    public function testTransformerException()
    {
        $transformer = new Book();
        $this->expectException(TransformerException::class);
        $this->expectExceptionMessage('Tried to transform not a Book');
        $transformer->transform('test');
    }
}
