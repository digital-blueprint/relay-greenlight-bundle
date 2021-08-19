<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use PHPStan\Testing\TestCase;

class VizHashTest extends TestCase
{
    public function testGenerateVizHash()
    {
        $photoData = file_get_contents(__DIR__.'/../src/Assets/example_photo.png');
        $font = __DIR__.'/../src/Assets/sourcesanspro.ttf';

        $jpeg = VizHash::create('foobar', $photoData, 600, 'Erika Musterfrau 1970', $font, 80);
        $this->assertNotNull($jpeg);
    }
}
