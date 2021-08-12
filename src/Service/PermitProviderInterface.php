<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use Dbp\Relay\GreenlightBundle\Entity\Permit;

interface PermitProviderInterface
{
    public function getPermitById(string $identifier): ?Permit;

    public function getPermits(): array;
}
