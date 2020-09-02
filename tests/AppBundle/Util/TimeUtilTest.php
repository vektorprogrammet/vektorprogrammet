<?php

use AppBundle\Utils\TimeUtil;
use PHPUnit\Framework\TestCase;

class TimeUtilTest extends TestCase
{
    public function testDateTimeIsTodayWithTodayDate()
    {
        $today = new DateTime();
        $this->assertTrue(TimeUtil::dateTimeIsToday($today));
    }

    public function testDateTimeIsTodayWithYesterdaysDate()
    {
        $yesterday = (new DateTime())->modify('-1 day');
        $this->assertFalse(TimeUtil::dateTimeIsToday($yesterday));
    }

    public function testDateTimeIsTodayWithTomorrowsDate()
    {
        $tomorrow = (new DateTime())->modify('+1 day');
        $this->assertFalse(TimeUtil::dateTimeIsToday($tomorrow));
    }

    public function testDateTimeIsInTheFutureWithFutureDate()
    {
        $tomorrow = (new DateTime())->modify('+1 day');
        $this->assertTrue(TimeUtil::dateTimeIsInTheFuture($tomorrow));
    }

    public function testDateTimeIsInTheFutureWithPastDate()
    {
        $yesterday = (new DateTime())->modify('-1 day');
        $this->assertFalse(TimeUtil::dateTimeIsInTheFuture($yesterday));
    }

    public function testDateTimeIsInTheFutureWithCurrentDateTime()
    {
        $now = new DateTime();
        $this->assertFalse(TimeUtil::dateTimeIsInTheFuture($now));
    }
}
