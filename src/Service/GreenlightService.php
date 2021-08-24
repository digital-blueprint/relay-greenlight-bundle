<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use DBP\API\BaseBundle\API\PersonProviderInterface;
use DBP\API\BaseBundle\Entity\Person;
use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\CoreBundle\Helpers\MimeTools;
use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Dbp\Relay\GreenlightBundle\Entity\PermitPersistence;
use Dbp\Relay\GreenlightBundle\Entity\ReferencePermit;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class GreenlightService
{
    public const REFERENCE_PERMIT_ID = '95c1d1fe-45c5-459f-bcd7-1f4c41fd7692';

    /**
     * @var PersonProviderInterface
     */
    private $personProvider;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PersonPhotoProviderInterface
     */
    private $personPhotoProviderInterface;

    /**
     * @var VizHashProvider
     */
    private $vizHashProvider;

    public function __construct(
        PersonProviderInterface $personProvider,
        ManagerRegistry $managerRegistry,
        PersonPhotoProviderInterface $personPhotoProviderInterface,
        VizHashProvider $vizHashProvider
    ) {
        $this->personProvider = $personProvider;
        $this->personPhotoProviderInterface = $personPhotoProviderInterface;
        $manager = $managerRegistry->getManager('dbp_relay_greenlight_bundle');
        assert($manager instanceof EntityManagerInterface);
        $this->em = $manager;
        $this->vizHashProvider = $vizHashProvider;
    }

    private function getCurrentPerson(): Person
    {
        $person = $this->personProvider->getCurrentPerson();

        if (!$person) {
            throw ApiError::withDetails(Response::HTTP_FORBIDDEN, "Current person wasn't found!", 'greenlight:current-person-not-found');
        }

        return $person;
    }

    /**
     * Fetches a Permit.
     */
    public function getPermitById(string $identifier): ?Permit
    {
        /** @var PermitPersistence $permitPersistence */
        $permitPersistence = $this->em
            ->getRepository(PermitPersistence::class)
            ->find($identifier);

        if (!$permitPersistence) {
            throw ApiError::withDetails(Response::HTTP_NOT_FOUND, 'Permit was not found!', 'greenlight:permit-not-found');
        }

        $permit = Permit::fromPermitPersistence($permitPersistence);

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

        $permitPersistences = $this->em
            ->getRepository(PermitPersistence::class)
            ->findBy(['personId' => $person->getIdentifier()]);

        return Permit::fromPermitPersistences($permitPersistences);
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
        $criteria->where($expr->lt('validUntil', new \DateTime('now')));

        $result = $this->em
            ->getRepository(PermitPersistence::class)
            ->matching($criteria);

        return Permit::fromPermitPersistences($result->getValues());
    }

    /**
     * Fetches a Permit for the current person.
     */
    public function getPermitByIdForCurrentPerson(string $identifier): ?Permit
    {
        $permit = $this->getPermitById($identifier);
        $person = $this->getCurrentPerson();

        if ($person->getIdentifier() !== $permit->getPersonId()) {
            throw ApiError::withDetails(Response::HTTP_FORBIDDEN, "Current person doesn't own this permit!", 'greenlight:person-does-not-own-permit');
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
        // Prevent "Detached entity cannot be removed" error by fetching the PermitPersistence
        // instead of using "PermitPersistence::fromPermit($permit)".
        // "$this->em->merge" would fix it too, but is deprecated
        /** @var PermitPersistence $permitPersistence */
        $permitPersistence = $this->em
            ->getRepository(PermitPersistence::class)
            ->find($permit->getIdentifier());

        $this->em->remove($permitPersistence);
        $this->em->flush();
    }

    public function createPermitForCurrentPerson(Permit $permit): Permit
    {
        $personId = $this->personProvider->getCurrentPerson()->getIdentifier();

        $permitPersistence = PermitPersistence::fromPermit($permit);
        $permitPersistence->setIdentifier((string) Uuid::v4());
        $permitPersistence->setPersonId($personId);
        $permitPersistence->setValidFrom(new \DateTime('now'));
        $permitPersistence->setValidUntil((new \DateTime('now'))->add(new \DateInterval('PT12H')));
        $permitPersistence->setImage($this->fetchBase64PhotoForPersonId($personId));

        $this->em->persist($permitPersistence);
        $this->em->flush();

        return Permit::fromPermitPersistence($permitPersistence);
    }

    /**
     * Returns the photo (or a fallback photo) of a person.
     */
    protected function fetchBase64PhotoForPersonId(string $personId): string
    {
        $base64PhotoData = '';

        // try to get a photo of the person
        try {
            $person = $this->personProvider->getPerson($personId);
            $base64PhotoData = $this->personPhotoProviderInterface->getPhotoData($person);
        } catch (NotFoundHttpException $e) {
        }

        // use missing_photo.png as fallback photo
        if ($base64PhotoData === '') {
            $photoData = file_get_contents(__DIR__.'/../src/Assets/missing_photo.png');

            if ($photoData !== '') {
                $base64PhotoData = base64_encode($photoData);
            }
        }

        return $base64PhotoData;
    }

    public function getReferencePermitById(string $id): ReferencePermit
    {
        switch ($id) {
            case self::REFERENCE_PERMIT_ID:
                $currentInput = $this->vizHashProvider->getCurrentInput();
                $image = $this->vizHashProvider->createReferenceImage($currentInput, 600);
                $mimeType = MimeTools::getMimeType($image);
                $imageText = MimeTools::getDataURI($image, $mimeType);

                $referencePermit = new ReferencePermit();
                $referencePermit->setIdentifier($id);
                $referencePermit->setConsentAssurance(false);
                $referencePermit->setManualCheckRequired(false);
                $referencePermit->setImage($imageText);

                return $referencePermit;
        }

        throw ApiError::withDetails(Response::HTTP_NOT_FOUND, 'ReferencePermit was not found!');
    }

    public function removeAllPermitsForCurrentPerson()
    {
        $reviews = $this->getPermitsForCurrentPerson();

        foreach ($reviews as $permit) {
            $this->removePermit($permit);
        }
    }
}
