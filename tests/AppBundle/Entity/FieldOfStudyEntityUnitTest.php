<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\FieldOfStudy;
use AppBundle\Entity\Department;
use PHPUnit\Framework\TestCase;

class FieldOfStudyEntityUnitTest extends TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {

        // new entity
        $fos = new FieldOfStudy();

        // Use the setName method
        $fos->setName('BITx2');

        // Assert the result
        $this->assertEquals('BITx2', $fos->getName());
    }

    // Check whether the setShortName function is working correctly
    public function testSetShortName()
    {

        // new entity
        $fos = new FieldOfStudy();

        // Use the setShortName method
        $fos->setShortName('BIT');

        // Assert the result
        $this->assertEquals('BIT', $fos->getShortName());
    }

    // Check whether the setDepartment function is working correctly
    public function testSetDepartment()
    {

        // new entity
        $fos = new FieldOfStudy();

        // A new test department entity
        $department = new Department();
        $department->setName('NTNU');

        // Use the setDepartment method
        $fos->setDepartment($department);

        // Assert the result
        $this->assertEquals($department, $fos->getDepartment());
    }
}
