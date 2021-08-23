<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PermitDataPersister extends AbstractController implements ContextAwareDataPersisterInterface
{
    /**
     * @var GreenlightService
     */
    private $greenlightService;

    public function __construct(GreenlightService $greenlightService)
    {
        $this->greenlightService = $greenlightService;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Permit;
    }

    /**
     * @param Permit $data
     *
     * @return Permit
     */
    public function persist($data, array $context = [])
    {
        return $this->greenlightService->createPermitForCurrentPerson($data);
    }

    /**
     * @param Permit $data
     *
     * @return void
     */
    public function remove($data, array $context = [])
    {
        $this->greenlightService->removePermitByIdForCurrentPerson($data->getIdentifier());
    }
}
