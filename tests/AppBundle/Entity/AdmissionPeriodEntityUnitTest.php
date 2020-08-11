<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\InfoMeeting;
use DateTime;
use PHPUnit\Framework\TestCase;

class AdmissionPeriodEntityUnitTest extends TestCase
{
    public function testShouldSendInfoMeetingNotification()
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setInfoMeeting(new InfoMeeting());
        $admissionPeriod->getInfoMeeting()->setDate((new DateTime())->modify('+1 hour'));
        $admissionPeriod->getInfoMeeting()->setShowOnPage(true);
        $this->assertTrue($admissionPeriod->shouldSendInfoMeetingNotifications());
    }

    public function testShouldSendInfoMeetingNotificationWithNull()
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setInfoMeeting(null);
        $this->assertFalse($admissionPeriod->shouldSendInfoMeetingNotifications());
    }

    public function testShouldSendInfoMeetingNotificationNoShowOnPage()
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setInfoMeeting(new InfoMeeting());
        $admissionPeriod->getInfoMeeting()->setDate((new DateTime())->modify('+1 hour'));
        $admissionPeriod->getInfoMeeting()->setShowOnPage(false);
        $this->assertFalse($admissionPeriod->shouldSendInfoMeetingNotifications());
    }

    public function testShouldSendInfoMeetingNotificationNotToday()
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setInfoMeeting(new InfoMeeting());
        $admissionPeriod->getInfoMeeting()->setDate((new DateTime())->modify('-1 day'));
        $admissionPeriod->getInfoMeeting()->setShowOnPage(true);
        $this->assertFalse($admissionPeriod->shouldSendInfoMeetingNotifications());
    }

    public function testShouldSendInfoMeetingNotificationOneHourAgo()
    {
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setInfoMeeting(new InfoMeeting());
        $admissionPeriod->getInfoMeeting()->setDate((new DateTime())->modify('-1 hour'));
        $admissionPeriod->getInfoMeeting()->setShowOnPage(true);
        $this->assertFalse($admissionPeriod->shouldSendInfoMeetingNotifications());
    }
}
