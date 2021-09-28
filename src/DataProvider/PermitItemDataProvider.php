<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PermitItemDataProvider extends AbstractController implements ItemDataProviderInterface, RestrictedDataProviderInterface
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
        return Permit::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Permit
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SCOPE_GREENLIGHT');

        $filters = $context['filters'] ?? [];
        $additionalInformation = $filters['additional-information'] ?? '';

        return $this->greenlightService->getPermitByIdForCurrentPerson($id, $additionalInformation);
    }
}
