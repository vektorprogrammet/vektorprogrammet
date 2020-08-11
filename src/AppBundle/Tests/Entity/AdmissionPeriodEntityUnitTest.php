<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use DateTime;

class AdmissionPeriodEntityUnitTest extends \PHPUnit_Framework_TestCase
{
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
