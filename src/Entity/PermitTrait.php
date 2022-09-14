<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

trait PermitTrait
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"GreenlightPermit:output"})
     */
    private $identifier;

    /**
     * @ApiProperty(iri="https://schema.org/identifier")
     * @Groups({"GreenlightPermit:output"})
     *
     * @var string
     */
    private $personId = '';

    /**
     * @ApiProperty(iri="https://schema.org/validFrom")
     * @Groups({"GreenlightPermit:output"})
     *
     * @var \DateTime
     */
    private $validFrom;

    /**
     * @ApiProperty(iri="https://schema.org/validUntil")
     * @Groups({"GreenlightPermit:output"})
     *
     * @var \DateTime
     */
    private $validUntil;

    /**
     * @Groups({"GreenlightPermit:output"})
     *
     * @var int
     */
    private $imageValidFor;

    /**
     * @ApiProperty(iri="https://schema.org/image")
     * @Groups({"GreenlightPermit:output"})
     *
     * @var string
     */
    private $image = '';

    /**
     * @ApiProperty(iri="https://schema.org/Boolean")
     * @Groups({"GreenlightPermit:output", "GreenlightPermit:input"})
     *
     * @var bool
     */
    private $consentAssurance;

    /**
     * @ApiProperty(iri="https://schema.org/Boolean")
     * @Groups({"GreenlightPermit:output", "GreenlightPermit:input"})
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

    public function getValidFrom(): ?\DateTime
    {
        return $this->validFrom;
    }

    public function setValidUntil(\DateTime $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function getValidUntil(): ?\DateTime
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

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getConsentAssurance(): bool
    {
        return $this->consentAssurance;
    }

    public function setConsentAssurance(bool $consentAssurance): void
    {
        $this->consentAssurance = $consentAssurance;
    }

    public function getAdditionalInformation(): string
    {
        return $this->additionalInformation;
    }

    public function setAdditionalInformation(string $additionalInformation): void
    {
        $this->additionalInformation = $additionalInformation;
    }

    public function getImageValidFor(): int
    {
        return $this->imageValidFor;
    }

    public function setImageValidFor(int $imageValidFor): void
    {
        $this->imageValidFor = $imageValidFor;
    }
}
