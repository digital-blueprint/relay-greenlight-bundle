<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\API;

use Dbp\Relay\CoreBundle\Exception\ApiError;

interface PersonPhotoProviderInterface
{
    /**
     * Returns the photo of the current user as binary data.
     *
     * @throws ApiError
     */
    public function getPhotoDataForCurrentUser(): string;
}
