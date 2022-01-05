<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Dbp\Relay\GreenlightBundle\VizHash\Utils;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "post" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *                 "requestBody" = {
 *                     "content" = {
 *                         "application/json" = {
 *                             "schema" = {"type" = "object"},
 *                             "example" = {"consentAssurance" = true, "additionalInformation" = "encrypted additional information"}
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *                 "parameters" = {
 *                     {"name" = "additional-information", "in" = "query", "description" = "Set an additional information", "type" = "string", "enum" = {"full", "partial"}, "example" = "full"}
 *                 }
 *             },
 *         }
 *     },
 *     itemOperations={
 *         "get" = {
 *             "security" = "is_granted('IS_AUTHENTICATED_FULLY')",
 *             "path" = "/greenlight/permits/{identifier}",
 *             "openapi_context" = {
 *                 "tags" = {"Electronic Covid Access Permits"},
 *                 "parameters" = {
 *                     {"name" = "additional-information", "in" = "query", "description" = "Set an additional information", "type" = "string", "enum" = {"full", "partial"}, "example" = "full"}
 *                 }
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
    public const ADDITIONAL_INFORMATION_FULL = 'full';
    public const ADDITIONAL_INFORMATION_PARTIAL = 'partial';
    public const ADDITIONAL_INFORMATION_LOCAL_PROOF_DEPRECATED = 'local-proof';

    /**
     * @throws \Exception
     */
    public static function fromPermitPersistence(PermitPersistence $permitPersistence, ?string $additionalInformation = null): Permit
    {
        if (is_null($additionalInformation)) {
            $additionalInformation = $permitPersistence->getAdditionalInformation();
        }

        // transform the deprecated attribute value into the new one
        if ($additionalInformation === self::ADDITIONAL_INFORMATION_LOCAL_PROOF_DEPRECATED) {
            $additionalInformation = self::ADDITIONAL_INFORMATION_FULL;
        }

        // The additional information must be set to "full" in the $permitPersistence entity AND in the
        // $additionalInformation parameter for the image to be in color, instead of grayscale
        $image = $additionalInformation === self::ADDITIONAL_INFORMATION_FULL &&
        $permitPersistence->getAdditionalInformation() === self::ADDITIONAL_INFORMATION_FULL ?
            $permitPersistence->getImageGenerated() : $permitPersistence->getImageGeneratedGray();

        $permit = new Permit();
        $permit->setIdentifier($permitPersistence->getIdentifier());
        $permit->setPersonId($permitPersistence->getPersonId());
        $permit->setImage($image);
        $permit->setValidUntil($permitPersistence->getValidUntil());
        $permit->setValidFrom($permitPersistence->getValidFrom());
        $permit->setImageValidFor(Utils::getRollingInput20MinPastHourValidFor(new \DateTimeImmutable('now', new \DateTimeZone('UTC'))));
        $permit->setAdditionalInformation($permitPersistence->getAdditionalInformation());
        $permit->setConsentAssurance($permitPersistence->getConsentAssurance());

        return $permit;
    }

    /**
     * @param PermitPersistence[] $permitPersistences
     *
     * @return Permit[]
     */
    public static function fromPermitPersistences(array $permitPersistences, ?string $additionalInformation = null): array
    {
        $permits = [];

        foreach ($permitPersistences as $permitPersistence) {
            $permits[] = self::fromPermitPersistence($permitPersistence, $additionalInformation);
        }

        return $permits;
    }

    /**
     * Check if the additional information is valid.
     */
    public static function isAdditionalInformationValidForText(string $text): bool
    {
        $additionalInformationList = [
            '',
            self::ADDITIONAL_INFORMATION_FULL,
            self::ADDITIONAL_INFORMATION_LOCAL_PROOF_DEPRECATED,
            self::ADDITIONAL_INFORMATION_PARTIAL,
        ];

        return in_array($text, $additionalInformationList, true);
    }

    /**
     * Check if the current additional information is valid.
     */
    public function isAdditionalInformationValid(): bool
    {
        return self::isAdditionalInformationValidForText($this->additionalInformation);
    }
}
