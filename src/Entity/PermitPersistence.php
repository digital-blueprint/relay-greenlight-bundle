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
     * @ORM\Column(type="binary", length=10485760)
     *
     * @var string|resource
     */
    private $imageOriginal;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $imageGenerated;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $imageGeneratedGray;

    /**
     * @ORM\Column(type="string", length=64)
     *
     * @var string
     */
    private $inputHash;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $consentAssurance;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $additionalInformation;

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

    public function getConsentAssurance(): bool
    {
        return $this->consentAssurance;
    }

    public function setConsentAssurance(bool $consentAssurance): void
    {
        $this->consentAssurance = $consentAssurance;
    }

    public function getImageOriginal(): string
    {
        return gettype($this->imageOriginal) === 'resource' ?
            stream_get_contents($this->imageOriginal) : $this->imageOriginal;
    }

    public function setImageOriginal(string $imageOriginal): void
    {
        $this->imageOriginal = $imageOriginal;
    }

    public function getAdditionalInformation(): string
    {
        return $this->additionalInformation;
    }

    public function setAdditionalInformation(string $additionalInformation): void
    {
        $this->additionalInformation = $additionalInformation;
    }

    public function getImageGenerated(): string
    {
        return $this->imageGenerated;
    }

    public function setImageGenerated(string $imageGenerated): void
    {
        $this->imageGenerated = $imageGenerated;
    }

    public function getImageGeneratedGray(): string
    {
        return $this->imageGeneratedGray;
    }

    public function setImageGeneratedGray(string $imageGeneratedGray): void
    {
        $this->imageGeneratedGray = $imageGeneratedGray;
    }

    public function getInputHash(): string
    {
        return $this->inputHash;
    }

    public function setInputHash(string $inputHash): void
    {
        $this->inputHash = $inputHash;
    }

    public static function fromPermit(Permit $permit): PermitPersistence
    {
        $permitPersistence = new PermitPersistence();
        $permitPersistence->setIdentifier($permit->getIdentifier());
        $permitPersistence->setPersonId($permit->getPersonId() === null ? '' : $permit->getPersonId());
        $permitPersistence->setImageOriginal($permit->getImage() === null ? '' : $permit->getImage());

        if ($permit->getValidUntil() !== null) {
            $permitPersistence->setValidUntil($permit->getValidUntil());
        }

        if ($permit->getValidFrom() !== null) {
            $permitPersistence->setValidFrom($permit->getValidFrom());
        }

        $permitPersistence->setAdditionalInformation($permit->getAdditionalInformation());
        $permitPersistence->setConsentAssurance($permit->getConsentAssurance());

        return $permitPersistence;
    }
}
