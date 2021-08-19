<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use PHPStan\Testing\TestCase;

class VizHashTest extends TestCase
{
    public function testGenerateBackground()
    {
        $size = 800;
        $p = $size / 100;
        // Generate the background
        $background = VizHash::generateBackground('test', $size, $size);
        $this->assertNotNull($background);

        // Pixelate and
        imagefilter($background, IMG_FILTER_PIXELATE, $p * 4, true);
        imagefilter($background, IMG_FILTER_MEAN_REMOVAL);

        // Blend in the photo
        $photo = imagecreatefromjpeg(__DIR__.'/erika.jpg');
        VizHash::blendPhoto($background, $photo, [5 * $p, 5 * $p, 20 * $p, 5], 0.8);
        imagedestroy($photo);

        // Add text to Bottom
        $font = __DIR__.'/sourcesanspro.ttf';
        VizHash::addBottomText($background, 'Erika Musterfrau 1970', 15 * $p, 2 * $p, $font, 0.8);

        // Save to png
        $png = VizHash::imageToPng($background);
        $this->assertNotNull($png);

        imagedestroy($background);
    }
}
