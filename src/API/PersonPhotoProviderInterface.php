<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\API;

use Exception;

interface PersonPhotoProviderInterface
{
    /**
     * Returns the photo of the current user as binary data.
     *
     * @throws Exception
     */
    public function getPhotoDataForCurrentUser(): string;
}
