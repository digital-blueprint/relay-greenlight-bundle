<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\API;

use Dbp\Relay\BaseBundle\Entity\Person;
use Dbp\Relay\GreenlightBundle\Exception\PhotoServiceException;

interface PersonPhotoProviderInterface
{
    /**
     * Returns the photo of a person as binary data.
     *
     * @throws PhotoServiceException
     */
    public function getPhotoData(Person $person): string;
}
