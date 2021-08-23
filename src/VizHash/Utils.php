<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\VizHash;

class Utils
{
    public static function getRollingInput20MinPastHour(\DateTimeImmutable $currentTime)
    {
        // create a new string 20 minutes after every hour
        $dateInterval = \DateInterval::createFromDateString('20 min');
        $currentTime = $currentTime->sub($dateInterval);

        return $currentTime->format("Y-m-d\TH");
    }
}
