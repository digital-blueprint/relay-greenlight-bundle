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

    /**
     * Returns the amount of seconds an image from getRollingInput20MinPastHour is still valid.
     */
    public static function getRollingInput20MinPastHourValidFor(\DateTimeImmutable $currentTime): int
    {
        $minutes = (int) date('i', $currentTime->getTimestamp());
        $seconds = (int) date('s', $currentTime->getTimestamp());

        if ($minutes >= 20) {
            return 3600 - (60 * ($minutes - 20) + $seconds);
        } else {
            return 60 * (20 - $minutes) - $seconds;
        }
    }
}
