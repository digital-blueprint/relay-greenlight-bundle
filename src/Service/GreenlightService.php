<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use DBP\API\BaseBundle\API\PersonProviderInterface;
use DBP\API\BaseBundle\Entity\Person;
use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class GreenlightService
{
    /**
     * @var PersonProviderInterface
     */
    private $personProvider;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PersonProviderInterface $personProvider, ManagerRegistry $managerRegistry)
    {
        $this->personProvider = $personProvider;
        $this->em = $managerRegistry->getManager("dbp_relay_greenlight_bundle");
    }

    private function getCurrentPerson(): Person
    {
        $person = $this->personProvider->getCurrentPerson();

        if (!$person) {
            // TODO: Use correct exception
            throw new ApiError(Response::HTTP_FORBIDDEN, "Person wasn't found!");
        }

        return $person;
    }

    /**
     * Fetches a Permit.
     */
    public function getPermitById(string $identifier): ?Permit
    {
        /** @var Permit $permit */
        $permit = $this->em
            ->getRepository(Permit::class)
            ->find($identifier);

        if (!$permit) {
            // TODO: Use correct exception
            throw new ApiError(Response::HTTP_NOT_FOUND, 'Permit was not found!');
        }

        return $permit;
    }

    /**
     * Fetches all Permit entities for the current person.
     *
     * @return Permit[]
     */
    public function getPermitsForCurrentPerson(): array
    {
        $person = $this->getCurrentPerson();

        return $this->em
            ->getRepository(Permit::class)
            ->findBy(['personId' => $person->getIdentifier()]);
    }

    /**
     * Fetches all expired Permit entities.
     *
     * @return Permit[]
     */
    public function getExpiredPermits(): array
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expr->lt('expires', new \DateTime('now')));

        $result = $this->em
            ->getRepository(Permit::class)
            ->matching($criteria);

        return $result->getValues();
    }

    /**
     * Fetches a Permit for the current person.
     */
    public function getPermitByIdForCurrentPerson(string $identifier): ?Permit
    {
        $permit = $this->getPermitById($identifier);
        $person = $this->getCurrentPerson();

        if ($person->getIdentifier() !== $permit->getPersonId()) {
            // TODO: Use correct exception
            throw new ApiError(Response::HTTP_FORBIDDEN, "Person doesn't own this permit!");
        }

        return $permit;
    }

    /**
     * Removes a Permit for the current person.
     */
    public function removePermitByIdForCurrentPerson(string $identifier): void
    {
        $permit = $this->getPermitByIdForCurrentPerson($identifier);

        if ($permit) {
            $this->removePermit($permit);
        }
    }

    /**
     * Removes a Permit.
     */
    public function removePermit(Permit $permit): void
    {
        $this->em->remove($permit);
        $this->em->flush();
    }
}
