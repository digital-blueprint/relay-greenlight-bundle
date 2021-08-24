<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Dbp\Relay\CoreBundle\Helpers\ArrayFullPaginator;
use Dbp\Relay\GreenlightBundle\Entity\ReferencePermit;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ReferencePermitCollectionDataProvider extends AbstractController implements CollectionDataProviderInterface, RestrictedDataProviderInterface
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

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): ArrayFullPaginator
    {
        $perPage = 30;
        $page = 1;

        $filters = $context['filters'] ?? [];
        if (isset($filters['page'])) {
            $page = (int) $filters['page'];
        }
        if (isset($filters['perPage'])) {
            $perPage = (int) $filters['perPage'];
        }

        $permits = [
            $this->greenlightService->getReferencePermitById(GreenlightService::REFERENCE_PERMIT_ID),
        ];

        return new ArrayFullPaginator($permits, $page, $perPage);
    }
}
