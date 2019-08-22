<?php

use AppBundle\Entity\InfoMeeting;
use AppBundle\Utils\InfoMeetingUtil;
use PHPUnit\Framework\TestCase;

class InfoMeetingUtilTest extends TestCase
{
    public function testShouldSendInfoMeetingNotification()
    {
        $infoMeeting = new InfoMeeting();
        $infoMeeting->setDate(new \DateTime());
        $infoMeeting->setShowOnPage(true);
        $this->assertTrue(InfoMeetingUtil::shouldSendInfoMeetingNotification($infoMeeting));
    }

    public function testShouldSendInfoMeetingNotificationWithNull()
    {
        $this->assertFalse(InfoMeetingUtil::shouldSendInfoMeetingNotification(null));
    }

    public function testShouldSendInfoMeetingNotificationNoShowOnPage()
    {
        $infoMeeting = new InfoMeeting();
        $infoMeeting->setDate(new \DateTime());
        $infoMeeting->setShowOnPage(false);
        $this->assertFalse(InfoMeetingUtil::shouldSendInfoMeetingNotification($infoMeeting));
    }

    public function testShouldSendInfoMeetingNotificationNotToday()
    {
        $infoMeeting = new InfoMeeting();
        $infoMeeting->setDate((new \DateTime())->modify('-1 day'));
        $infoMeeting->setShowOnPage(true);
        $this->assertFalse(InfoMeetingUtil::shouldSendInfoMeetingNotification($infoMeeting));
    }
}
