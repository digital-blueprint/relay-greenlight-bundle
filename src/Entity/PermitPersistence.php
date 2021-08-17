<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="greenlight_permits")
 */
class PermitPersistence
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50)
     */
    private $identifier;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $validFrom;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $validUntil;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @var string
     */
    private $personId;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $place;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $image;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $consentAssurance;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $manualCheckRequired;

    public function getIdentifier(): string
    {
        return (string) $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function setValidFrom(\DateTime $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getValidFrom(): \DateTime
    {
        return $this->validFrom;
    }

    public function setValidUntil(\DateTime $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getValidUntil(): \DateTime
    {
        return $this->validUntil;
    }

    public function getPersonId(): string
    {
        return $this->personId;
    }

    public function setPersonId(string $personId): void
    {
        $this->personId = $personId;
    }

    /**
     * @return bool
     */
    public function getConsentAssurance(): bool
    {
        return $this->consentAssurance;
    }

    /**
     * @param bool $consentAssurance
     */
    public function setConsentAssurance(bool $consentAssurance): void
    {
        $this->consentAssurance = $consentAssurance;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return bool
     */
    public function getManualCheckRequired(): bool
    {
        return $this->manualCheckRequired;
    }

    /**
     * @param bool $manualCheckRequired
     */
    public function setManualCheckRequired(bool $manualCheckRequired): void
    {
        $this->manualCheckRequired = $manualCheckRequired;
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @param string $place
     */
    public function setPlace(string $place): void
    {
        $this->place = $place;
    }

    /**
     * @param Permit $permit
     * @return PermitPersistence
     */
    static public function fromPermit(Permit $permit): PermitPersistence {
        $permitPersistence = new PermitPersistence();
        $permitPersistence->setIdentifier($permit->getIdentifier());
        $permitPersistence->setPlace($permit->getPlace() === null ? '' : $permit->getPlace());
        $permitPersistence->setPersonId($permit->getPersonId() === null ? '' : $permit->getPersonId());
        $permitPersistence->setImage($permit->getImage() === null ? '' : $permit->getImage());

        if ($permit->getValidUntil() !== null) {
            $permitPersistence->setValidUntil($permit->getValidUntil());
        }

        if ($permit->getValidFrom() !== null) {
            $permitPersistence->setValidFrom($permit->getValidFrom());
        }

        $permitPersistence->setManualCheckRequired($permit->getManualCheckRequired());
        $permitPersistence->setConsentAssurance($permit->getConsentAssurance());

        return $permitPersistence;
    }
}
