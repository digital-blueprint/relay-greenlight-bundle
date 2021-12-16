<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Service;

use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;

class DummyPersonPhotoProvider implements PersonPhotoProviderInterface
{
    /**
     * Returns the photo of the current user as binary data.
     */
    public function getPhotoDataForCurrentUser(): string
    {
        // TODO: Add some code to fetch the photo for the current user
        return '';
    }
}
