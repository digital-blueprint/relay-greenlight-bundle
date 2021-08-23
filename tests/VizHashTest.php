<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use Dbp\Relay\GreenlightBundle\VizHash\Utils;
use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use PHPStan\Testing\TestCase;

class VizHashTest extends TestCase
{
    public function testGenerateVizHash()
    {
        $photoData = file_get_contents(__DIR__.'/../src/Assets/example_photo.jpg');
        $font = __DIR__.'/../src/Assets/sourcesanspro.ttf';

        $jpeg = VizHash::create('foobar', $photoData, 600, 'REFERENCE TICKET', $font, 80);
        $this->assertNotNull($jpeg);
    }

    public function testGetRollingInput20MinPastHour()
    {
        $t1 = Utils::getRollingInput20MinPastHour(new \DateTimeImmutable('2021-08-23T13:25:59Z'));
        $t2 = Utils::getRollingInput20MinPastHour(new \DateTimeImmutable('2021-08-23T14:19:59Z'));
        $t3 = Utils::getRollingInput20MinPastHour(new \DateTimeImmutable('2021-08-23T14:20:59Z'));

        $this->assertSame($t1, $t2);
        $this->assertNotSame($t2, $t3);
    }
}
