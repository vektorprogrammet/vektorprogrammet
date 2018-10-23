<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Team;
use AppBundle\Entity\Department;
use PHPUnit\Framework\TestCase;

class TeamEntityUnitTest extends TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {

        // new entity
        $team = new Team();

        // Use the setName method
        $team->setName('Hovedstyret');

        // Assert the result
        $this->assertEquals('Hovedstyret', $team->getName());
    }

    // Check whether the setDepartment function is working correctly
    public function testSetDepartment()
    {

        // new entity
        $team = new Team();

        // dummy entity
        $department = new Department();
        $department->setName('NTNU');

        // Use the setDepartment method
        $team->setDepartment($department);

        // Assert the result
        $this->assertEquals($department, $team->getDepartment());
    }
}
