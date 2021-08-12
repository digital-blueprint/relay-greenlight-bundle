<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Controller;

use Dbp\Relay\GreenlightBundle\Entity\Permit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LoggedInOnly extends AbstractController
{
    public function __invoke(Permit $data, Request $request): Permit
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $data;
    }
}
