<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Dbp\Relay\GreenlightBundle\Controller\LoggedInOnly;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get" = {
 *             "path" = "/greenlight/permits",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         },
 *         "put" = {
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         },
 *         "delete" = {
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         },
 *         "loggedin_only" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "method" = "GET",
 *             "path" = "/greenlight/permits/{identifier}/loggedin-only",
 *             "controller" = LoggedInOnly::class,
 *             "openapi_context" = {
 *                 "summary" = "Only works when logged in.",
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
 */
class Permit
{
    /**
     * @ApiProperty(identifier=true)
     */
    private $identifier;

    /**
     * @ApiProperty(iri="https://schema.org/name")
     * @Groups({"GreenlightPermit:output", "GreenlightPermit:input"})
     *
     * @var string
     */
    private $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }
}
