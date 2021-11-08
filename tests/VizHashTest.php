<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use Dbp\Relay\GreenlightBundle\VizHash\Utils;
use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use PHPUnit\Framework\TestCase;

class VizHashTest extends TestCase
{
    public function testGenerateVizHash()
    {
        $photoData = file_get_contents(__DIR__.'/../src/Assets/example_photo.jpg');
        $font = __DIR__.'/../src/Assets/SourceSansPro-SemiBold.ttf';

        $jpeg = VizHash::create('foobar', 'description', $photoData, 600, 'REFERENCE TICKET', $font, 80);
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

    public function testGetRollingInput20MinPastHourValidFor()
    {
        $t1 = Utils::getRollingInput20MinPastHourValidFor(new \DateTimeImmutable('2021-08-23T13:25:59Z'));
        $t2 = Utils::getRollingInput20MinPastHourValidFor(new \DateTimeImmutable('2021-08-23T14:19:59Z'));
        $t3 = Utils::getRollingInput20MinPastHourValidFor(new \DateTimeImmutable('2021-08-23T14:20:59Z'));
        $t4 = Utils::getRollingInput20MinPastHourValidFor(new \DateTimeImmutable('2021-08-23T14:00:00Z'));
        $t5 = Utils::getRollingInput20MinPastHourValidFor(new \DateTimeImmutable('2021-08-23T14:20:00Z'));

        $this->assertSame($t1, 3241);
        $this->assertSame($t2, 1);
        $this->assertSame($t3, 3541);
        $this->assertSame($t4, 1200);
        $this->assertSame($t5, 3600);
    }

    public function testInvalidPhotoData()
    {
        $font = __DIR__.'/../src/Assets/SourceSansPro-SemiBold.ttf';
        $this->expectException(\RuntimeException::class);
        VizHash::create('foobar', 'description', 'foobar', 600, 'REFERENCE TICKET', $font, 80);
    }

    public function testInvalidFont()
    {
        $photoData = file_get_contents(__DIR__.'/../src/Assets/example_photo.jpg');
        $this->expectException(\RuntimeException::class);
        VizHash::create('foobar', 'description', $photoData, 600, 'REFERENCE TICKET', '', 80);
    }
}
