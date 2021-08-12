<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use Dbp\Relay\GreenlightBundle\Entity\Permit;

class ExternalApi implements PermitProviderInterface
{
    private $places;

    public function __construct(MyCustomService $service)
    {
        $service = $service;

        $this->places = [];
        $place1 = new Permit();
        $place1->setIdentifier('graz');
        $place1->setName('Graz');

        $place2 = new Permit();
        $place2->setIdentifier('vienna');
        $place2->setName('Vienna');

        $this->places[] = $place1;
        $this->places[] = $place2;
    }

    public function getPermitById(string $identifier): ?Permit
    {
        foreach ($this->places as $place) {
            if ($place->getIdentifier() === $identifier) {
                return $place;
            }
        }

        return null;
    }

    public function getPermits(): array
    {
        return $this->places;
    }
}
