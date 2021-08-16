<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Dbp\Relay\GreenlightBundle\Controller\CreatePermitAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "post" = {
 *             "method" = "POST",
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits",
 *             "controller" = CreatePermitAction::class,
 *             "deserialize" = false,
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *                 "requestBody" = {
 *                     "content" = {
 *                         "multipart/form-data" = {
 *                             "schema" = {
 *                                 "type" = "object",
 *                                 "properties" = {
 *                                     "place" = {"description" = "Place", "type" = "string", "example" = "somedata"},
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         },
 *         "delete" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
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
 * @ORM\Entity
 * @ORM\Table(name="greenlight_permits")
 */
class Permit
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"GreenlightPermit:output"})
     * @ORM\Id
     * @ORM\Column(type="string", length=50)
     */
    private $identifier;

    /**
     * @ApiProperty(iri="https://schema.org/validFor")
     * @Groups({"GreenlightPermit:output", "GreenlightPermit:input"})
     *
     * @var \DateInterval
     */
    private $validFor;

    /**
     * @ApiProperty(iri="https://schema.org/expires")
     * @Groups({"GreenlightPermit:output", "GreenlightPermit:input"})
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $expires;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @var string
     */
    private $personId;

    public function getIdentifier(): string
    {
        return (string) $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getValidFor(): \DateInterval
    {
        return (new \DateTime('now'))->diff($this->expires);
    }

    public function getExpires(): \DateTime
    {
        return $this->expires;
    }

    public function setExpires(\DateTime $expires): void
    {
        $this->expires = $expires;
    }

    public function getPersonId(): string
    {
        return $this->personId;
    }

    public function setPersonId(string $personId): void
    {
        $this->personId = $personId;
    }
}
