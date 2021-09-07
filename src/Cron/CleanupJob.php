<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Cron;

use Dbp\Relay\CoreBundle\Cron\CronEvent;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;

class CleanupJob
{
    private const SCHEDULE = '0 * * * *'; // Every hour

    /** @var GreenlightService */
    private $greenlightService;

    public function __construct(GreenlightService $greenlightService)
    {
        $this->greenlightService = $greenlightService;
    }

    public function onDbpRelayCron(CronEvent $event)
    {
        if (!$event->isDue('greenlight-cleanup', self::SCHEDULE)) {
            return;
        }

        $reviews = $this->greenlightService->getExpiredPermits();
        foreach ($reviews as $review) {
            $this->greenlightService->removePermit($review);
        }
    }
}
