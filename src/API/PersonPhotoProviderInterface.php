<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\API;

use Dbp\Relay\GreenlightBundle\Exception\PhotoServiceException;

interface PersonPhotoProviderInterface
{
    /**
     * Returns the photo of the current user as binary data.
     *
     * @throws PhotoServiceException
     */
    public function getPhotoDataForCurrentUser(): string;
}
