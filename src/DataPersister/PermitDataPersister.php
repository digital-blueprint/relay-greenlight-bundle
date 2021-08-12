<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Dbp\Relay\GreenlightBundle\Service\PermitProviderInterface;

class PermitDataPersister implements DataPersisterInterface
{
    private $api;

    public function __construct(PermitProviderInterface $api)
    {
        $this->api = $api;
    }

    public function supports($data): bool
    {
        return $data instanceof Permit;
    }

    public function persist($data)
    {
        // TODO
    }

    public function remove($data)
    {
        // TODO
    }
}
