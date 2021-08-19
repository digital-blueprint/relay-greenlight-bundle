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
        VizHash::blendPhoto($background, $photo, [20, 20, 80, 20], 0.8);
        imagedestroy($photo);

        // Add text to Bottom
        $font = __DIR__.'/sourcesanspro.ttf';
        VizHash::addBottomText($background, 'Erika Musterfrau 1970', 80, 10, $font, 0.8);

        // Save to png
        $png = VizHash::imageToPng($background);
        $this->assertNotNull($png);

        imagedestroy($background);
    }
}
