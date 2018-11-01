<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Entity\FieldOfStudy;
use AppBundle\Entity\School;
use AppBundle\Entity\Team;
use PHPUnit\Framework\TestCase;

class DepartmentEntityUnitTest extends TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {

        // new entity
        $department = new Department();

        // Use the setName method
        $department->setName('NTNUx2');

        // Assert the result
        $this->assertEquals('NTNUx2', $department->getName());
    }

    // Check whether the setShortName function is working correctly
    public function testSetShortName()
    {

        // new entity
        $department = new Department();

        // Use the setShortName method
        $department->setShortName('NTNU');

        // Assert the result
        $this->assertEquals('NTNU', $department->getShortName());
    }

    // Check whether the addFieldOfStudy function is working correctly
    public function testAddFieldOfStudy()
    {

        // new entity
        $department = new Department();

        $fos = new FieldOfStudy();

        $fos->setName('Test');

        // Use the addFieldOfStudy method
        $department->addFieldOfStudy($fos);

        // Field of studies is stored in an array
        $fieldOfStudies = $department->getFieldOfStudy();

        // Loop through the array and check for matches
        foreach ($fieldOfStudies as $study) {
            if ($fos == $study) {
                // Assert the result
                $this->assertEquals($fos, $study);
            }
        }
    }

    // Check whether the removeFieldOfStudy function is working correctly
    public function testRemoveFieldOfStudy()
    {

        // new entity
        $department = new Department();

        $fos1 = new FieldOfStudy();
        $fos1->setName('fos1');
        $fos2 = new FieldOfStudy();
        $fos2->setName('fos2');
        $fos3 = new FieldOfStudy();
        $fos3->setName('fos3');

        // Use the addFieldOfStudy method
        $department->addFieldOfStudy($fos1);
        $department->addFieldOfStudy($fos2);
        $department->addFieldOfStudy($fos3);

        // Remove $fos1 from department
        $department->removeFieldOfStudy($fos1);

        // Field of studies is stored in an array
        $fieldOfStudies = $department->getFieldOfStudy();

        // Loop through the array
        foreach ($fieldOfStudies as $study) {
            // Assert the result
            $this->assertNotEquals($fos1, $study);
        }
    }

    // Check whether the setEmail function is working correctly
    public function testSetEmail()
    {

        // new entity
        $department = new Department();

        // Use the setEmail method
        $department->setEmail('NTNU@mail.com');

        // Assert the result
        $this->assertEquals('NTNU@mail.com', $department->getEmail());
    }

    // Check whether the addSchool function is working correctly
    public function testAddSchool()
    {

        // new entity
        $department = new Department();

        $school1 = new School();

        $school1->setName('Skole1');

        // Use the addSchool method
        $department->addSchool($school1);

        // Schools are stored in an array
        $schools = $department->getSchools();

        // Loop through the array and check for matches
        foreach ($schools as $school) {
            if ($school1 == $school) {
                // Assert the result
                $this->assertEquals($school1, $school);
            }
        }
    }

    // Check whether the removeSchool function is working correctly
    public function testRemoveSchool()
    {

        // new entity
        $department = new Department();

        $school1 = new School();
        $school1->setName('School1');
        $school2 = new School();
        $school2->setName('school2');
        $school3 = new School();
        $school3->setName('school3');

        // Use the addSchool method
        $department->addSchool($school1);
        $department->addSchool($school2);
        $department->addSchool($school3);

        // Remove $school1 from department
        $department->removeSchool($school1);

        // Schools are stored in an array
        $schools = $department->getSchools();

        // Loop through the array
        foreach ($schools as $school) {
            // Assert the result
            $this->assertNotEquals($school1, $school);
        }
    }

    // Check whether the setAddess function is working correctly
    public function testSetAddress()
    {

        // new entity
        $department = new Department();

        // Use the setAddress  method
        $department->setAddress('Storgata 12');

        // Assert the result
        $this->assertEquals('Storgata 12', $department->getAddress());
    }

    public function testAddAdmissionPeriod()
    {
        $department = new Department();
        $admissionPeriod1 = new AdmissionPeriod();
        $department->addAdmissionPeriod($admissionPeriod1);
        $semesters = $department->getAdmissionPeriods();

        foreach ($semesters as $semester) {
            if ($admissionPeriod1 == $semester) {
                $this->assertEquals($admissionPeriod1, $semester);
            }
        }
    }

    // Check whether the addTeam function is working correctly
    public function testAddTeam()
    {

        // new entity
        $department = new Department();

        $team1 = new Team();

        $team1->setName('Team1');

        // Use the addTeam method
        $department->addTeam($team1);

        // Teams are stored in an array
        $teams = $department->getTeams();

        // Loop through the array and check for matches
        foreach ($teams as $team) {
            if ($team1 == $team) {
                // Assert the result
                $this->assertEquals($team1, $team);
            }
        }
    }

    // Check whether the removeTeam function is working correctly
    public function testRemoveTeam()
    {

        // new entity
        $department = new Department();

        $team1 = new Team();
        $team1->setName('Team1');
        $team2 = new Team();
        $team2->setName('Team2');
        $team3 = new Team();
        $team3->setName('Team3');

        // Use the addTeam method
        $department->addTeam($team1);
        $department->addTeam($team2);
        $department->addTeam($team3);

        // Remove $team1 from department
        $department->removeTeam($team1);

        // Teams are stored in an array
        $teams = $department->getTeams();

        // Loop through the array
        foreach ($teams as $team) {
            // Assert the result
            $this->assertNotEquals($team1, $team);
        }
    }

    public function testSetLogoPath()
    {
        $department = new Department();
        $fakePath = 'fake path';
        $department->setLogoPath($fakePath);

        $this->assertEquals($fakePath, $department->getLogoPath());
    }
}
