<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get" = {
 *             "path" = "/greenlight/reference-permits",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "path" = "/greenlight/reference-permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"}
 *             },
 *         }
 *     },
 *     iri="https://schema.org/Permit",
 *     shortName="GreenlightReferencePermit",
 *     normalizationContext={
 *         "groups" = {"GreenlightPermit:output"},
 *         "jsonld_embed_context" = true
 *     }
 * )
 */
class ReferencePermit
{
    use PermitTrait;
}
