<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\API;

use DBP\API\BaseBundle\Entity\Person;
use Dbp\Relay\GreenlightBundle\Exception\PhotoServiceNotAvailableException;

interface PersonPhotoProviderInterface
{
    /**
     * Returns the photo of a person as binary data.
     *
     * @throws PhotoServiceNotAvailableException
     */
    public function getPhotoData(Person $person): string;
}
