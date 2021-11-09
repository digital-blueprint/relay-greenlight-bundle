<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use Dbp\Relay\BasePersonBundle\Entity\Person;
use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;

class DummyPersonPhotoProvider implements PersonPhotoProviderInterface
{
    /**
     * Returns the photo of a person as binary data.
     */
    public function getPhotoData(Person $person): string
    {
        // TODO: Add some code to fetch the photo for $person
        return '';
    }
}
