<?php

declare(strict_types=1);

use Timonnorth\ServiceLibraryCatalogPhp\SkeletonClass;

class ExampleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that true does in fact equal true.
     */
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * Test skeleton.
     */
    public function testSkeleton()
    {
        $this->assertEquals("Hello world!", (new SkeletonClass())->echoPhrase("Hello world!"));
    }
}
