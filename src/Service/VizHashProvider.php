<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use Dbp\Relay\GreenlightBundle\VizHash\Utils;
use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VizHashProvider
{
    private const FONTPATH = __DIR__.'/../Assets/SourceSansPro-SemiBold.ttf';
    private const JPEG_QUALITY = 85;

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Create a jpeg image with a centered photo.
     */
    public function createImageWithPhoto(string $input, string $description, string $photoData, int $size, $grayScale = false): string
    {
        return VizHash::create($input, $description, $photoData, $size, null, self::FONTPATH, self::JPEG_QUALITY, $grayScale);
    }

    /**
     * Create a jpeg image with a centered image indicating a missing photo.
     */
    public function createImageMissingPhoto(string $input, string $description, int $size, $grayScale = false): string
    {
        $photoData = file_get_contents(__DIR__.'/../Assets/missing_photo.png');

        return VizHash::create($input, $description, $photoData, $size, null, self::FONTPATH, self::JPEG_QUALITY, $grayScale);
    }

    /**
     * Create a jpeg image with an example photo and a watermark.
     */
    public function createReferenceImage(string $input, string $description, int $size, $grayScale = false): string
    {
        $photoData = file_get_contents(__DIR__.'/../Assets/example_photo.jpg');

        return VizHash::create($input, $description, $photoData, $size, 'REFERENCE TICKET', self::FONTPATH, self::JPEG_QUALITY, $grayScale);
    }

    /**
     * This can be passed to createImage() as $input. The result changes ~ every hour.
     */
    public function getCurrentInput(): string
    {
        if (!$this->parameters->has('kernel.secret')) {
            throw new \RuntimeException('secret required');
        }
        // Returns a different string on every server.
        $serverInput = $this->parameters->get('kernel.secret');
        assert(is_string($serverInput));

        // This returns a different string 20 minutes after every hour.
        $currentTime = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $timeInput = Utils::getRollingInput20MinPastHour($currentTime);

        return hash('sha256', $timeInput.$serverInput);
    }
}
