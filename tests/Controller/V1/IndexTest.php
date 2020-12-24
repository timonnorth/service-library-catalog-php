<?php

declare(strict_types=1);

namespace Tests\Controller\V1;

use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexAction()
    {
        $response = $this->route('GET', '/');

        self::assertEquals(200, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"status":"Ok"}',
            (string)$response->getBody()
        );
    }

    public function testHealthcheckAction()
    {
        $response = $this->route('GET', '/healthcheck');
        self::assertEquals(200, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"status":"Ok"}',
            (string)$response->getBody()
        );

        $response = $this->route('GET', '/api/v1/healthcheck');
        self::assertEquals(200, $response->getStatusCode());
        self::assertJsonStringEqualsJsonString(
            '{"status":"Ok"}',
            (string)$response->getBody()
        );
    }
}
