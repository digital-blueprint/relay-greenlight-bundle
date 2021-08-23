<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\TestUtils;

use DBP\API\BaseBundle\Entity\Person;
use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;

class DummyPersonPhotoProvider implements PersonPhotoProviderInterface
{
    public function getPhotoData(Person $person): ?string
    {
        return 'Test';
    }
}
