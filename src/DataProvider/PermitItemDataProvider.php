<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Dbp\Relay\GreenlightBundle\Service\PermitProviderInterface;

final class PermitItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $api;

    public function __construct(PermitProviderInterface $api)
    {
        $this->api = $api;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Permit::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Permit
    {
        return $this->api->getPermitById($id);
    }
}
