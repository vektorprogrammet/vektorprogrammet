<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\InfoMeeting;
use AppBundle\Entity\Department;
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

    // Check whether the setstartDate function is working correctly
    public function testSetstartDate()
    {

        // New datetime variable
        $today = new DateTime('now');

        // new entity
        $admissionPeriod = new AdmissionPeriod();

        // Use the setstartDate method
        $admissionPeriod->setStartDate($today);

        // Assert the result
        $this->assertEquals($today, $admissionPeriod->getStartDate());
    }

    // Check whether the setendDate function is working correctly
    public function testSetendDate()
    {

        // New datetime variable
        $today = new DateTime('now');

        // new entity
        $admissionPeriod = new AdmissionPeriod();

        // Use the setendDate method
        $admissionPeriod->setEndDate($today);

        // Assert the result
        $this->assertEquals($today, $admissionPeriod->getEndDate());
    }

    // Check whether the setDepartment function is working correctly
    public function testSetDepartment()
    {
        // new entity
        $admissionPeriod = new AdmissionPeriod();

        // Dummy entity
        $department = new Department();

        // Use the setDepartment method
        $admissionPeriod->setDepartment($department);

        // Assert the result
        $this->assertEquals($department, $admissionPeriod->getDepartment());
    }
}
