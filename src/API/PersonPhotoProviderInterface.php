<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\API;

use DBP\API\BaseBundle\Entity\Person;

interface PersonPhotoProviderInterface
{
    /**
     * Returns the photo of a person as binary data.
     */
    public function getPhotoData(Person $person): string;
}
