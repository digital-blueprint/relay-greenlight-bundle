<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Controller;

use DBP\API\BaseBundle\API\PersonProviderInterface;
use Dbp\Relay\CoreBundle\Exception\ApiError;
use DBP\Hcert;
use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class CreatePermitAction extends AbstractController
{
    /**
     * @var GreenlightService
     */
    private $greenlightService;

    /**
     * @var PersonProviderInterface
     */
    private $personProvider;

    public function __construct(GreenlightService $greenlightService, PersonProviderInterface $personProvider)
    {
        $this->greenlightService = $greenlightService;
        $this->personProvider = $personProvider;
    }

    public function __invoke(Request $request): Permit
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $data = '';

        if ($request->request->has('digital_covid_certificate_data')) {
            $data = trim($request->request->all()['digital_covid_certificate_data']);
        }

        if ($data === '') {
            throw ApiError::withDetails(Response::HTTP_BAD_GATEWAY, 'digital_covid_certificate_data was not set!', 'eu-dcc:certificate-data-not-set');
        }

        // TODO: Hcert Web API is currently down
/*
        try {
            $hcert = Hcert::fromQrcodePayload($data, true);
        } catch (\Exception $e) {
            throw ApiError::withDetails(Response::HTTP_BAD_GATEWAY, 'Hcert error: '.$e->getMessage(), 'eu-dcc:hcert-error', ['message' => $e->getMessage()]);
        }

        if (!$hcert->isSignatureValid()) {
            // TODO: User new error handling
            throw new ApiError(Response::HTTP_BAD_GATEWAY, 'Certificate signature is not valid!');
        }

//        dump($hcert);

        $firstName = $hcert->firstName();
        $lastName = $hcert->lastName();
        $birthdayString = $hcert->dayOfBirth();
*/

        $firstName = 'Max';
        $lastName = 'Mustermann';
        $birthdayString = '1990-03-20';

        // Check if currently logged-in persons matches person from certificate
//        if (!$this->greenlightService->isCurrentPersonSimilarTo($firstName, $lastName, $birthdayString)) {
//            // TODO: User new error handling
//            throw new ApiError(Response::HTTP_FORBIDDEN, "Person from QR code doesn't match with current person!");
//        }

        // TODO: Set expires datetime
        $expires = new \DateTime('2021-12-08 10:00:00');

        // TODO: Check if certificate is still valid

        // Remove all previous reviews of the current person before creating a new review
        $reviews = $this->greenlightService->getPermitsForCurrentPerson();
        foreach ($reviews as $review) {
            $this->greenlightService->removePermitByIdForCurrentPerson($review->getIdentifier());
        }

        $permit = new Permit();
        $permit->setIdentifier((string) Uuid::v4());
        $permit->setExpires($expires);
        $permit->setPersonId($this->personProvider->getCurrentPerson()->getIdentifier());

        $entityManager = $this->getDoctrine()->getManager("dbp_relay_eu_dcc_bundle");
        $entityManager->persist($permit);

        $entityManager->flush();

        return $permit;
    }
}
