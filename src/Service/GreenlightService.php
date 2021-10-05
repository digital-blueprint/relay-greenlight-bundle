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
use Dbp\Relay\GreenlightBundle\Exception\PhotoServiceException;
use Dbp\Relay\GreenlightBundle\VizHash\Utils;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class GreenlightService
{
    public const REFERENCE_PERMIT_ID = '95c1d1fe-45c5-459f-bcd7-1f4c41fd7692';
    private const REFERENCE_DESCRIPTION = 'Erika Musterfrau';
    private const IMAGE_SIZE = 600;

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

    /**
     * @var ?CacheItemPoolInterface
     */
    private $cachePool;

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

    public function setCache(?CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
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
    public function getPermitById(string $identifier, ?string $additionalInformation = null): ?Permit
    {
        /** @var PermitPersistence $permitPersistence */
        $permitPersistence = $this->em
            ->getRepository(PermitPersistence::class)
            ->find($identifier);

        if (!$permitPersistence) {
            throw ApiError::withDetails(Response::HTTP_NOT_FOUND, 'Permit was not found!', 'greenlight:permit-not-found');
        }

        // Update the generated image if it needs an update
        $this->updateGeneratedImageForPermitPersistenceIfNeeded($permitPersistence);

        return Permit::fromPermitPersistence($permitPersistence, $additionalInformation);
    }

    /**
     * Fetches all Permit entities for the current person.
     *
     * @return Permit[]
     */
    public function getPermitsForCurrentPerson(?string $additionalInformation = null): array
    {
        $person = $this->getCurrentPerson();

        $permitPersistences = $this->em
            ->getRepository(PermitPersistence::class)
            ->findBy(['personId' => $person->getIdentifier()]);

        foreach ($permitPersistences as $permitPersistence) {
            // Update the generated image if it needs an update
            $this->updateGeneratedImageForPermitPersistenceIfNeeded($permitPersistence);
        }

        return Permit::fromPermitPersistences($permitPersistences, $additionalInformation);
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
    public function getPermitByIdForCurrentPerson(string $identifier, ?string $additionalInformation = null): ?Permit
    {
        $permit = $this->getPermitById($identifier, $additionalInformation);
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

    private function getImageDescription(): string
    {
        $person = $this->getCurrentPerson();

        return ($person->getGivenName() ?? '').' '.($person->getFamilyName() ?? '');
    }

    public function createPermitForCurrentPerson(Permit $permit): Permit
    {
        if (!$permit->isAdditionalInformationValid()) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST, 'Additional information is not valid!', 'greenlight:additional-information-not-valid');
        }

        if (!$permit->getConsentAssurance()) {
            throw ApiError::withDetails(Response::HTTP_BAD_REQUEST, 'Consent assurance must be true!', 'greenlight:consent-assurance-not-true');
        }

        $personId = $this->personProvider->getCurrentPerson()->getIdentifier();

        // We try look for an existing permit and reuse it instead if deleting the existing one to gain a speed
        // improvement and to not get "Duplicate entry" errors when benchmarking with high load on the same person
        $permitPersistences = $this->em
            ->getRepository(PermitPersistence::class)
            ->findBy(['personId' => $personId]);

        // If we can't reuse an existing permit we want to create a new one
        if (count($permitPersistences) === 0) {
            $permitPersistence = PermitPersistence::fromPermit($permit);
            $permitPersistence->setIdentifier((string) Uuid::v4());
        } else {
            $permitPersistence = $permitPersistences[0];
            $permitPersistence->setAdditionalInformation($permit->getAdditionalInformation());
            $permitPersistence->setConsentAssurance($permit->getConsentAssurance());
        }

        $permitPersistence->setPersonId($personId);
        $permitPersistence->setValidFrom(new \DateTime('now'));
        $permitPersistence->setValidUntil((new \DateTime('now'))->add(new \DateInterval('P1Y')));
        $permitPersistence->setImageOriginal($this->fetchPhotoForPersonId($personId));
        $permitPersistence->setImageGenerated('');
        $permitPersistence->setImageGeneratedGray('');
        $permitPersistence->setInputHash('');

        try {
            $this->em->persist($permitPersistence);
            $this->em->flush();
        } catch (\Exception $e) {
            throw ApiError::withDetails(Response::HTTP_INTERNAL_SERVER_ERROR, 'Permit could not be created!', 'greenlight:permit-not-created', ['message' => $e->getMessage()]);
        }

        // Fetch the generated image
        $this->updateGeneratedImageForPermitPersistenceIfNeeded($permitPersistence);

        return Permit::fromPermitPersistence($permitPersistence);
    }

    public function updateGeneratedImageForPermitPersistenceIfNeeded(PermitPersistence $permitPersistence): bool
    {
        $currentInput = $this->vizHashProvider->getCurrentInput();

        // Check if the input hash has changed
        if ($currentInput === $permitPersistence->getInputHash()) {
            return false;
        }

        $imageOriginal = $permitPersistence->getImageOriginal();

        // Fetch VizHash image based on original image (in "color")
        $imageText = $this->createVizHashImage($currentInput, $imageOriginal);
        $permitPersistence->setImageGenerated($imageText);

        // Fetch VizHash image based on original image (in "gray")
        $imageText = $this->createVizHashImage($currentInput, $imageOriginal, true);
        $permitPersistence->setImageGeneratedGray($imageText);

        // Store input hash to later check if we need to regenerate the images
        $permitPersistence->setInputHash($currentInput);

        $this->em->persist($permitPersistence);
        $this->em->flush();

        return true;
    }

    /**
     * Returns the photo (or a fallback photo) of a person.
     * Or an empty string in case none was available.
     */
    protected function fetchPhotoForPersonId(string $personId): string
    {
        $photoData = '';

        // try to get a photo of the person
        try {
            $person = $this->personProvider->getPerson($personId);
        } catch (NotFoundHttpException $e) {
            throw ApiError::withDetails(Response::HTTP_FORBIDDEN, "Current person wasn't found!", 'greenlight:current-person-not-found');
        }

        try {
            $photoData = $this->personPhotoProviderInterface->getPhotoData($person);
        } catch (PhotoServiceException $e) {
            throw ApiError::withDetails(Response::HTTP_INTERNAL_SERVER_ERROR, 'The photo service had an error!', 'greenlight:photo-service-error', ['message' => $e->getMessage()]);
        }

        return $photoData;
    }

    public function getReferenceImageCached(): string
    {
        assert($this->cachePool !== null);
        $currentInput = $this->vizHashProvider->getCurrentInput();
        $cacheKey = $currentInput;
        $item = $this->cachePool->getItem($cacheKey);
        $image = $item->get();
        if ($image === null) {
            $image = $this->vizHashProvider->createReferenceImage($currentInput, self::REFERENCE_DESCRIPTION, self::IMAGE_SIZE);
            $item->set($image);
            $expiresAfter = Utils::getRollingInput20MinPastHourValidFor(
                new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            $item->expiresAfter($expiresAfter);
            $this->cachePool->save($item);
        }

        return $image;
    }

    public function getReferencePermitById(string $id): ReferencePermit
    {
        switch ($id) {
            case self::REFERENCE_PERMIT_ID:
                $image = $this->getReferenceImageCached();
                $mimeType = MimeTools::getMimeType($image);
                $imageText = MimeTools::getDataURI($image, $mimeType);

                $referencePermit = new ReferencePermit();
                $referencePermit->setIdentifier($id);
                $referencePermit->setImageValidFor(Utils::getRollingInput20MinPastHourValidFor(
                    new \DateTimeImmutable('now', new \DateTimeZone('UTC'))));
                $referencePermit->setConsentAssurance(false);
                $referencePermit->setAdditionalInformation('');
                $referencePermit->setImage($imageText);

                return $referencePermit;
        }

        throw ApiError::withDetails(Response::HTTP_NOT_FOUND, 'ReferencePermit was not found!');
    }

    /**
     * Removes all permits of the current person.
     *
     * Because of the unique key only a maximum of one permit should be removed,
     * so there is no real need to do that in one query.
     */
    public function removeAllPermitsForCurrentPerson()
    {
        $reviews = $this->getPermitsForCurrentPerson();

        foreach ($reviews as $permit) {
            $this->removePermit($permit);
        }
    }

    protected function createVizHashImage(string $currentInput, string $imageOriginal, bool $grayScale = false): string
    {
        $description = $this->getImageDescription();
        if ($imageOriginal === '') {
            // Fail if no photo is available
            throw ApiError::withDetails(Response::HTTP_SERVICE_UNAVAILABLE, 'Photo for current person could not be loaded!', 'greenlight:current-person-no-photo');
//            $image = $this->vizHashProvider->createImageMissingPhoto($currentInput, $description, self::IMAGE_SIZE, $grayScale);
        } else {
            $image = $this->vizHashProvider->createImageWithPhoto($currentInput, $description, $imageOriginal, self::IMAGE_SIZE, $grayScale);
        }
        $mimeType = MimeTools::getMimeType($image);

        return MimeTools::getDataURI($image, $mimeType);
    }
}
