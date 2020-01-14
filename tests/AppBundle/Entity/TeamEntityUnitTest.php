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

    // Check whether the setDeadline function is working correctly
    public function testSetDeadlineAndAcceptApplication()
    {
        // Deadline requires accept application to be true
        // new entity
        $team = new Team();

        // dummy entity
        $deadline = new \DateTime("now +3 days");

        // Use the setDeadline method
        $team->setDeadline($deadline);

        // Assert the result
        $this->assertNotEquals($deadline, $team->getDeadline());

        // Try again with accept application true
        $team->setAcceptApplication(true);
        $team->setDeadline($deadline);

        // Assert the result
        $this->assertEquals($deadline, $team->getDeadline());
    }
}
