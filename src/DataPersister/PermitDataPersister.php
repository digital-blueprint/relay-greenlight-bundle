<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Dbp\Relay\GreenlightBundle\DataProvider\Utils;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class PermitDataPersister extends AbstractController implements ContextAwareDataPersisterInterface
{
    /**
     * @var GreenlightService
     */
    private $greenlightService;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(GreenlightService $greenlightService, RequestStack $requestStack)
    {
        $this->greenlightService = $greenlightService;
        $this->requestStack = $requestStack;
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
        // Remove all previous permits of the current person before creating a new permit
        $this->greenlightService->removeAllPermitsForCurrentPerson();

        $data->setAdditionalInformation(
            Utils::securityByObscurity($this->requestStack->getCurrentRequest(), $data->getAdditionalInformation()));

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
