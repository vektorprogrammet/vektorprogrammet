<?php

namespace AppBundle\Utils;

class TimeUtil
{
    public static function dateTimeIsToday(\DateTime $date): bool
    {
        return $date->format('Ymd') === (new \DateTime())->format('Ymd');
    }

    public static function dateTimeIsInTheFuture(\DateTime $date): bool
    {
        return $date > new \DateTime();
    }
}
