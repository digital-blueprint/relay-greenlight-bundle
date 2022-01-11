<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\HealthCheck;

use Dbp\Relay\CoreBundle\HealthCheck\CheckInterface;
use Dbp\Relay\CoreBundle\HealthCheck\CheckOptions;
use Dbp\Relay\CoreBundle\HealthCheck\CheckResult;
use Dbp\Relay\GreenlightBundle\Service\GreenlightService;

class HealthCheck implements CheckInterface
{
    private $service;

    public function __construct(GreenlightService $service)
    {
        $this->service = $service;
    }

    public function getName(): string
    {
        return 'greenlight';
    }

    private function checkDbConnection(): CheckResult
    {
        $result = new CheckResult('Check if we can connect to the DB');

        try {
            $this->service->checkConnection();
        } catch (\Throwable $e) {
            $result->set(CheckResult::STATUS_FAILURE, $e->getMessage(), ['exception' => $e]);

            return $result;
        }
        $result->set(CheckResult::STATUS_SUCCESS);

        return $result;
    }

    public function check(CheckOptions $options): array
    {
        return [$this->checkDbConnection()];
    }
}
