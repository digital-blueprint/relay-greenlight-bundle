<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
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
     * @param mixed $data
     *
     * @return Permit
     */
    public function persist($data, array $context = [])
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SCOPE_GREENLIGHT');

        $permit = $data;
        assert($permit instanceof Permit);

        $data->setAdditionalInformation(
            Utils::decodeAdditionalInformation($this->requestStack->getCurrentRequest(), $permit->getAdditionalInformation()));

        return $this->greenlightService->createPermitForCurrentPerson($permit);
    }

    /**
     * @param mixed $data
     *
     * @return void
     */
    public function remove($data, array $context = [])
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted('ROLE_SCOPE_GREENLIGHT');

        $permit = $data;
        assert($permit instanceof Permit);
        $this->greenlightService->removePermitByIdForCurrentPerson($permit->getIdentifier());
    }
}
