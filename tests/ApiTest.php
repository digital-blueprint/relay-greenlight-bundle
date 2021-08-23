<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ApiTest extends ApiTestCase
{
    public function testNoAuth()
    {
        $endpoints = [
            ['POST', '/greenlight/permits', 401],
            ['GET', '/greenlight/permits', 401],
            ['GET', '/greenlight/permits/123', 401],
            ['DELETE', '/greenlight/permits/123', 401],
        ];

        foreach ($endpoints as $ep) {
            [$method, $path, $status] = $ep;
            $client = self::createClient();
            $response = $client->request($method, $path);
            $this->assertEquals($status, $response->getStatusCode(), $path);
        }
    }
}
