<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamInterest;
use PHPUnit\Framework\TestCase;

class TeamInterestEntityUnitTest extends TestCase
{

    /**
     * @var TeamInterest
     */
    private $teamInterest;

    public function __construct()
    {
        parent::__construct();
        $this->teamInterest = new TeamInterest();
    }

    public function testSetName()
    {
        $this->teamInterest->setName("test");
        $this->assertEquals("test", $this->teamInterest->getName());
    }

    public function testSetEmail()
    {
        $this->teamInterest->setEmail("test@test.com");
        $this->assertEquals("test@test.com", $this->teamInterest->getEmail());
    }

    public function testSetPotentialTeams()
    {
        $teams = array(new Team(), new Team());
        $this->teamInterest->setPotentialTeams($teams);
        $this->assertEquals($teams, $this->teamInterest->getPotentialTeams());
    }

    public function testSetSemester()
    {
        $semester = new Semester();
        $this->teamInterest->setSemester($semester);
        $this->assertEquals($semester, $this->teamInterest->getSemester());
    }

    public function testSetDepartment()
    {
        $department = new Department();
        $this->teamInterest->setDepartment($department);
        $this->assertEquals($department, $this->teamInterest->getDepartment());
    }
}
