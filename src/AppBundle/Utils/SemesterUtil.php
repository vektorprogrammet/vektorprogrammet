<?php

namespace AppBundle\Utils;

use DateTime;

class SemesterUtil
{
    /**
     * @param DateTime $time
     *
     * @return string
     */
    public static function timeToYear(DateTime $time): string
    {
        return $time->format('Y');
    }

    /**
     * @param DateTime $time
     *
     * @return string
     */
    public static function timeToSemesterTime(DateTime $time): string
    {
        return $time->format('m') <= 7 ? 'Vår' : 'Høst';
    }
}
