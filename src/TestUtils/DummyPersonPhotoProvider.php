<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\TestUtils;

use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;

class DummyPersonPhotoProvider implements PersonPhotoProviderInterface
{
    public function getPhotoDataForCurrentUser(): string
    {
        return 'Test';
    }
}
