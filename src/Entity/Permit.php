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
    use PermitTrait;

    public static function fromPermitPersistence(PermitPersistence $permitPersistence): Permit
    {
        $permit = new Permit();
        $permit->setIdentifier($permitPersistence->getIdentifier());
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
     *
     * @return Permit[]
     */
    public static function fromPermitPersistences(array $permitPersistences): array
    {
        $permits = [];

        foreach ($permitPersistences as $permitPersistence) {
            $permits[] = self::fromPermitPersistence($permitPersistence);
        }

        return $permits;
    }
}
