<?php

use AppBundle\Utils\TimeUtil;
use PHPUnit\Framework\TestCase;

class TimeUtilTest extends TestCase
{
    public function testDateIsTodayWithTodayDate()
    {
        $today = new \DateTime();
        $this->assertTrue(TimeUtil::dateIsToday($today));
    }

    public function testDateIsTodayWithYesterdaysDate()
    {
        $yesterday = (new \DateTime())->modify('-1 day');
        $this->assertFalse(TimeUtil::dateIsToday($yesterday));
    }

    public function testDateIsTodayWithTomorrowsDate()
    {
        $tomorrow = (new \DateTime())->modify('+1 day');
        $this->assertFalse(TimeUtil::dateIsToday($tomorrow));
    }
}
