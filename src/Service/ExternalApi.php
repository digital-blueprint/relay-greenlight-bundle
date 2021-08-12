<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use Dbp\Relay\GreenlightBundle\Entity\Permit;

class ExternalApi implements PermitProviderInterface
{
    private $permits;

    public function __construct(MyCustomService $service)
    {
        $this->permits = [];
        $permit1 = new Permit();
        $permit1->setIdentifier('graz');
        $permit1->setName('Graz');

        $permit2 = new Permit();
        $permit2->setIdentifier('vienna');
        $permit2->setName('Vienna');

        $this->permits[] = $permit1;
        $this->permits[] = $permit2;
    }

    public function getPermitById(string $identifier): ?Permit
    {
        foreach ($this->permits as $permit) {
            if ($permit->getIdentifier() === $identifier) {
                return $permit;
            }
        }

        return null;
    }

    public function getPermits(): array
    {
        return $this->permits;
    }
}
