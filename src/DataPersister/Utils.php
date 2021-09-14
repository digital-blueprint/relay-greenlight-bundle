<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\DataPersister;

use Base64Url\Base64Url;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A256GCM;
use Jose\Component\Encryption\Algorithm\KeyEncryption\PBES2HS256A128KW;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Encryption\JWEDecrypter;
use Jose\Component\Encryption\JWELoader;
use Jose\Component\Encryption\Serializer\CompactSerializer;
use Jose\Component\Encryption\Serializer\JWESerializerManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Utils
{
    /**
     * This "decrypts" the additional information string using the current oauth2
     * token, using A256GCM and PBES2-HS256+A128KW.
     *
     * Since we can't do any server side validation the user needs to confirm in the
     * UI that he/she won't abuse the system.
     *
     * By using the token we make replaying an older requests harder and by using
     * JOSE which needs crypto APIs, abusing the system can't reasonably be done by
     * accident but only deliberately.
     *
     * This doesn't make things more secure, it just makes the intent of the user
     * more clear in case the API isn't used through our UI flow.
     */
    public static function decodeAdditionalInformation(Request $request, string $token): string
    {
        $secret = explode(' ', $request->headers->get('Authorization', ' '), 2)[1];

        $jwk = new JWK([
            'kty' => 'oct',
            'k' => Base64Url::encode($secret),
        ]);

        $keyEncryptionAlgorithmManager = new AlgorithmManager([new PBES2HS256A128KW()]);
        $contentEncryptionAlgorithmManager = new AlgorithmManager([new A256GCM()]);
        $compressionMethodManager = new CompressionMethodManager([new Deflate()]);
        $jweDecrypter = new JWEDecrypter(
            $keyEncryptionAlgorithmManager,
            $contentEncryptionAlgorithmManager,
            $compressionMethodManager
        );

        $serializerManager = new JWESerializerManager([new CompactSerializer()]);
        $jweLoader = new JWELoader(
            $serializerManager,
            $jweDecrypter,
            null
        );

        try {
            $jwe = $jweLoader->loadAndDecryptWithKey($token, $jwk, $recipient);
        } catch (\Exception $e) {
            throw new AccessDeniedException();
        }

        $res = $jwe->getPayload();
        assert(is_string($res));

        return $res;
    }
}
