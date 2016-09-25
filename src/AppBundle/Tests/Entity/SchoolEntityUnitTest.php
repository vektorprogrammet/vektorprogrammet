<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\School;
use AppBundle\Entity\Department;
use AppBundle\Entity\Subforum;

class SchoolEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {

        // new entity
        $school = new School();

        // Use the setName method
        $school->setName('Heggen');

        // Assert the result
        $this->assertEquals('Heggen', $school->getName());
    }

    // Check whether the setContactPerson unction is working correctly
    public function testSetContactPerson()
    {

        // new entity
        $school = new School();

        // Use the setContactPerson method
        $school->setContactPerson('Vibeke');

        // Assert the result
        $this->assertEquals('Vibeke', $school->getContactPerson());
    }

    // Check whether the addDepartment function is working correctly
    public function testAddDepartment()
    {

        // new entity
        $school = new School();

        // dummy entity
        $department1 = new Department();
        $department1->setName('NTNU');

        // Use the addDepartment method
        $school->addDepartment($department1);

        // Departments are stored in an array
        $departments = $school->getDepartments();

        // Loop through the array and check for matches
        foreach ($departments as $d) {
            if ($department1 == $d) {
                // Assert the result
                $this->assertEquals($department1, $d);
            }
        }
    }

    // Check whether the removeDepartment function is working correctly
    public function testRemoveDepartment()
    {

        // new entity
        $school = new School();

        $department1 = new Department();
        $department1->setName('Department1');
        $department2 = new Department();
        $department2->setName('Department2');
        $department3 = new Department();
        $department3->setName('Department3');

        // Use the addDepartment method
        $school->addDepartment($department1);
        $school->addDepartment($department2);
        $school->addDepartment($department3);

        // Remove $department1
        $school->removeDepartment($department1);

        // Departments are stored in an array
        $departments = $school->getDepartments();

        // Loop through the array
        foreach ($departments as $d) {
            // Assert the result
            $this->assertNotEquals($department1, $d);
        }
    }

    // Check whether the setEmail unction is working correctly
    public function testSetEmail()
    {

        // new entity
        $school = new School();

        // Use the setEmail method
        $school->setEmail('Heggen@vgs.com');

        // Assert the result
        $this->assertEquals('Heggen@vgs.com', $school->getEmail());
    }

    // Check whether the setPhone unction is working correctly
    public function testSetPhone()
    {

        // new entity
        $school = new School();

        // Use the setPhone method
        $school->setPhone('12312312');

        // Assert the result
        $this->assertEquals('12312312', $school->getPhone());
    }

    // Check whether the addSubforum function is working correctly
    public function testAddSubforum()
    {

        // new entity
        $school = new School();

        // dummy entity
        $subforum1 = new Subforum();
        $subforum1->setName('NTNU');

        // Use the addSubforum method
        $school->addSubforum($subforum1);

        // Subforums are stored in an array
        $subforums = $school->getSubforums();

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
        $school = new School();

        $subforum1 = new Subforum();
        $subforum1->setName('Subforum1');
        $subforum2 = new Subforum();
        $subforum2->setName('Subforum2');
        $subforum3 = new Subforum();
        $subforum3->setName('Subforum3');

        // Use the addSubforum method
        $school->addSubforum($subforum1);
        $school->addSubforum($subforum2);
        $school->addSubforum($subforum3);

        // Remove $subforum1
        $school->removeSubforum($subforum1);

        // Subforums are stored in an array
        $subforums = $school->getSubforums();

        // Loop through the array
        foreach ($subforums as $subforum) {
            // Assert the result
            $this->assertNotEquals($subforum1, $subforum);
        }
    }
}
