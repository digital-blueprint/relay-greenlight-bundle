<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests\Service;

use Dbp\Relay\GreenlightBundle\Service\VizHashProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class VizHashProviderTest extends TestCase
{
    public function testCreateReferenceImage()
    {
        $prov = new VizHashProvider(new ParameterBag());
        $res = $prov->createReferenceImage('test', 100);
        $this->assertNotEmpty(imagecreatefromstring($res));
    }

    public function testCreateImageMissingPhoto()
    {
        $prov = new VizHashProvider(new ParameterBag());
        $res = $prov->createImageMissingPhoto('test', 100);
        $this->assertNotEmpty(imagecreatefromstring($res));
    }

    public function testCreateImageWithPhoto()
    {
        $prov = new VizHashProvider(new ParameterBag());
        $photoData = file_get_contents(__DIR__.'/../../src/Assets/example_photo.jpg');
        $res = $prov->createImageWithPhoto('test', $photoData, 100);
        $this->assertNotEmpty(imagecreatefromstring($res));
    }

    public function testGetCurrentInput()
    {
        $prov = new VizHashProvider(new ParameterBag(['kernel.secret' => 'mysecret']));
        $input = $prov->getCurrentInput();
        $this->assertIsString($input);
    }
}
