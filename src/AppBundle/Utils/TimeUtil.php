<?php

namespace AppBundle\Utils;

class TimeUtil
{
    public static function dateIsToday(\DateTime $date): bool
    {
        return $date->format('Ymd') === (new \DateTime())->format('Ymd');
    }
}
