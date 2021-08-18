<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use PHPStan\Testing\TestCase;

class VizHashTest extends TestCase
{
    public function testGenerateBackground()
    {
        $image = VizHash::generateBackground('test', 400, 400);
        $this->assertNotNull($image);
        $png = VizHash::imageToPng($image);
        $this->assertNotNull($png);
        imagedestroy($image);
    }
}
