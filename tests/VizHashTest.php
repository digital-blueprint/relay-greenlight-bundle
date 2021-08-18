<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use PHPStan\Testing\TestCase;

class VizHashTest extends TestCase
{
    public function testGenerateBackground()
    {
        // Generate the background
        $background = VizHash::generateBackground('test', 400, 400);
        $this->assertNotNull($background);

        // Blend in the photo
        $photo = imagecreatefromjpeg(__DIR__.'/erika.jpg');
        VizHash::blendPhoto($background, $photo, 0.85, 0.9);
        imagedestroy($photo);

        // Save to png
        $png = VizHash::imageToPng($background);
        $this->assertNotNull($png);

        imagedestroy($background);
    }
}
