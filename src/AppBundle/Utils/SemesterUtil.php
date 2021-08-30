<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Semester;
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

    /**
     * @param DateTime $time
     * @return Semester
     */
    public static function timeToSemester(Datetime $time): Semester
    {
        $semester = new Semester();
        $semester->setYear(self::timeToYear($time));
        $semester->setSemesterTime(self::timeToSemesterTime($time));
        return $semester;
    }
}
