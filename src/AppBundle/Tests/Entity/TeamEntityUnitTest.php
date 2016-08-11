<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Team;
use AppBundle\Entity\Department;
use AppBundle\Entity\Subforum;

class TeamEntityUnitTest extends \PHPUnit_Framework_TestCase
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

    // Check whether the addSubforum function is working correctly
    public function testAddSubforum()
    {

        // new entity
        $team = new Team();

        // New dummy entity 
        $subforum1 = new Subforum();
        $subforum1->setName('subforum1');

        // Use the addSubforum method 
        $team->addSubforum($subforum1);

        // Subforums is stored in an array 
        $subforums = $team->getSubforums();

        // Loop through the array and check for matches
        foreach ($subforums as $subforum) {
            if ($subforum1 == $subforum) {
                // Assert the result
                $this->assertEquals($subforum1, $subforum);
            }
        }
    }

    // Check whether the removeSubforum function is working correctly
    public function testRemoveSubforum()
    {

        // new entity
        $team = new Team();

        // New dummy entity 
        $subforum1 = new Subforum();
        $subforum1->setName('subforum1');
        $subforum2 = new Subforum();
        $subforum2->setName('subforum2');
        $subforum3 = new Subforum();
        $subforum3->setName('subforum3');

        // Use the addSubforum method 
        $team->addSubforum($subforum1);
        $team->addSubforum($subforum2);
        $team->addSubforum($subforum3);

        // Remove $subforum1 from department 
        $team->removeSubforum($subforum1);

        // subforums is stored in an array 
        $subforums = $team->getSubforums();

        // Loop through the array
        foreach ($subforums as $subforum) {
            // Assert the result 
            $this->assertNotEquals($subforum1, $subforum);
        }
    }
}
