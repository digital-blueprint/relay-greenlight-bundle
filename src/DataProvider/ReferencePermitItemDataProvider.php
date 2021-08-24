<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Dbp\Relay\GreenlightBundle\Entity\ReferencePermit;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReferencePermitItemDataProvider extends AbstractController implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var GreenlightService
     */
    private $greenlightService;

    public function __construct(GreenlightService $greenlightService)
    {
        $this->greenlightService = $greenlightService;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return ReferencePermit::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?ReferencePermit
    {
        return $this->greenlightService->getReferencePermitById($id);
    }
}
