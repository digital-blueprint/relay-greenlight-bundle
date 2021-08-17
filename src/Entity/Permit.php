<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "post" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"}
 *             }
 *         },
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"}
 *             },
 *         },
 *         "delete" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"}
 *             },
 *         }
 *     },
 *     iri="https://schema.org/Permit",
 *     shortName="GreenlightPermit",
 *     normalizationContext={
 *         "groups" = {"GreenlightPermit:output"},
 *         "jsonld_embed_context" = true
 *     },
 *     denormalizationContext={
 *         "groups" = {"GreenlightPermit:input"},
 *         "jsonld_embed_context" = true
 *     }
 * )
 */
class Permit
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"GreenlightPermit:output"})
     */
    private $identifier;

    /**
     * @ApiProperty(iri="https://schema.org/validFrom")
     * @Groups({"GreenlightPermit:output"})
     *
     * @var string
     */
    private $personId = "";

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
     * @ApiProperty(iri="https://schema.org/Place")
     * @Groups({"GreenlightPermit:output", "GreenlightPermit:input"})
     *
     * @var string
     */
    private $place = "";

    /**
     * @ApiProperty(iri="https://schema.org/image")
     * @Groups({"GreenlightPermit:output"})
     *
     * @var string
     */
    private $image = "";

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
     * @param PermitPersistence $permitPersistence
     * @return Permit
     */
    static public function fromPermitPersistence(PermitPersistence $permitPersistence): Permit {
        $permit = new Permit();
        $permit->setIdentifier($permitPersistence->getIdentifier());
        $permit->setPlace($permitPersistence->getPlace());
        $permit->setPersonId($permitPersistence->getPersonId());
        $permit->setImage($permitPersistence->getImage());
        $permit->setValidUntil($permitPersistence->getValidUntil());
        $permit->setValidFrom($permitPersistence->getValidFrom());
        $permit->setManualCheckRequired($permitPersistence->getManualCheckRequired());
        $permit->setConsentAssurance($permitPersistence->getConsentAssurance());

        return $permit;
    }

    /**
     * @param PermitPersistence[] $permitPersistences
     * @return Permit[]
     */
    static public function fromPermitPersistences(array $permitPersistences): array {
        $permits = [];

        foreach($permitPersistences as $permitPersistence) {
            $permits[] = self::fromPermitPersistence($permitPersistence);
        }

        return $permits;
    }
}
