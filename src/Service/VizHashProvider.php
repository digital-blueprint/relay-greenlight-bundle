<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use Dbp\Relay\GreenlightBundle\VizHash\VizHash;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VizHashProvider
{
    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    private const INTERVAL = '1 hour';

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Create an JPEG image.
     */
    public function createImage(string $input, string $photoData, int $size, string $text): string
    {
        $font = __DIR__.'/../Assets/sourcesanspro.ttf';

        return VizHash::create($input, $photoData, $size, $text, $font, 80);
    }

    /**
     * This can be passed to createImage() as $input.
     */
    public function getCurrentInput(): string
    {
        return hash('sha256', $this->getTimeInput().$this->getServerInput());
    }

    /**
     * Returns a different string on every server.
     */
    private function getServerInput(): string
    {
        return $this->parameters->has('kernel.secret') ? $this->parameters->get('kernel.secret') : '';
    }

    /**
     * This returns a different string every hour.
     */
    private function getTimeInput(): string
    {
        $dateInterval = \DateInterval::createFromDateString(self::INTERVAL);
        $reference = new \DateTimeImmutable();
        $endTime = $reference->add($dateInterval);
        $diff = $endTime->getTimestamp() - $reference->getTimestamp();
        $timestamp = (new \DateTimeImmutable())->getTimestamp();

        return (string) intdiv($timestamp, $diff);
    }
}
