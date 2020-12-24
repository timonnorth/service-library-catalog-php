<?php

declare(strict_types=1);

namespace Tests;

class TestCaseMigration extends TestCase
{
    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::runDbMigration();
    }

    /**
     * @throws \Exception
     */
    public static function tearDownAfterClass(): void
    {
        static::runDbMigration(true);
        parent::tearDownAfterClass();
    }
}
