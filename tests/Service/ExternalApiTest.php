<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests\Service;

use Dbp\Relay\GreenlightBundle\Service\ExternalApi;
use Dbp\Relay\GreenlightBundle\Service\MyCustomService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExternalApiTest extends WebTestCase
{
    private $api;

    protected function setUp(): void
    {
        $service = new MyCustomService('secret-test-custom');
        $this->api = new ExternalApi($service);
    }

    public function test()
    {
        $this->assertTrue(true);
        $this->assertNotNull($this->api);
    }
}
