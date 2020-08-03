<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use DateTime;

class AdmissionPeriodEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    // Check whether the setAdmissionStartDate function is working correctly
    public function testSetAdmissionStartDate()
    {

        // New datetime variable
        $today = new DateTime('now');

        // new entity
        $admissionPeriod = new AdmissionPeriod();

        // Use the setAdmissionStartDate method
        $admissionPeriod->setAdmissionStartDate($today);

        // Assert the result
        $this->assertEquals($today, $admissionPeriod->getAdmissionStartDate());
    }

    // Check whether the setAdmissionEndDate function is working correctly
    public function testSetAdmissionEndDate()
    {

        // New datetime variable
        $today = new DateTime('now');

        // new entity
        $admissionPeriod = new AdmissionPeriod();

        // Use the setAdmissionEndDate method
        $admissionPeriod->setAdmissionEndDate($today);

        // Assert the result
        $this->assertEquals($today, $admissionPeriod->getAdmissionEndDate());
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
