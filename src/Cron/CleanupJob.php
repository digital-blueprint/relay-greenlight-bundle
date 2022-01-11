<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Cron;

use Dbp\Relay\CoreBundle\Cron\CronJobInterface;
use Dbp\Relay\CoreBundle\Cron\CronOptions;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;

class CleanupJob implements CronJobInterface
{
    /** @var GreenlightService */
    private $greenlightService;

    public function __construct(GreenlightService $greenlightService)
    {
        $this->greenlightService = $greenlightService;
    }

    public function getName(): string
    {
        return 'Greenlight Expired Permit DB Cleanup';
    }

    public function getInterval(): string
    {
        return '0 * * * *'; // Every hour
    }

    public function run(CronOptions $options): void
    {
        $reviews = $this->greenlightService->getExpiredPermits();
        foreach ($reviews as $review) {
            $this->greenlightService->removePermit($review);
        }
    }
}
