<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Entity\Position;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;
use AppBundle\Service\FilterService;
use AppBundle\Service\Sorter;
use AppBundle\Twig\Extension\TeamPositionSortExtension;
use DateTime;
use PHPUnit\Framework\TestCase;

class TeamPositionSortExtensionUnitTest extends TestCase
{
    private $sortExtension;
    private $activeSemester;
    private $latestAdmissionPeriod;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->sortExtension = new TeamPositionSortExtension(new Sorter(), new FilterService());

        $this->activeSemester = new Semester();
        $this->activeSemester
            ->setYear(2013)
            ->setSemesterTime('Vår');

        $this->latestAdmissionPeriod = new AdmissionPeriod();
        $this->latestAdmissionPeriod
            ->setSemester($this->activeSemester)
            ->setAdmissionStartDate(new DateTime('2013-01-01'))
            ->setAdmissionEndDate((new DateTime())->modify('+1day'));
    }

    public function testExecutiveMembers()
    {
        $users = array();
        $positions = ['Sekretær', 'Leder', '', 'Økonomi', 'Assistent', 'Medlem', 'Nestleder'];
        $board = new ExecutiveBoard();

        for ($x = 0; $x < 7; ++$x) {
            $user = new User();
            $membership = new ExecutiveBoardMembership();
            $membership->setPositionName($positions[$x])
                       ->setBoard($board)
                       ->setStartSemester($this->activeSemester);
            $user->setMemberships(array($membership));
            $users[] = $user;
        }

        $sortedMemberships = $this->sortExtension->teamPositionSortFilter($users, $board);
        $sortedPositions = ['Leder', 'Nestleder', 'Assistent', 'Medlem', 'Sekretær', 'Økonomi', ''];
        for ($x = 0; $x < 7; ++$x) {
            $this->assertEquals($sortedMemberships[$x]->getActiveExecutiveBoardMemberships()[0]->getPositionName(), $sortedPositions[$x]);
        }
    }

    public function testTeamMemberships()
    {
        $users = array();
        $positions = ['Sekretær', 'Leder', '', 'Økonomi', 'Assistent', 'Medlem', 'Nestleder'];
        $department = new Department();
        $department->addAdmissionPeriod($this->latestAdmissionPeriod);
        $team = new Team();
        $team->setDepartment($department);

        for ($x = 0; $x < 7; ++$x) {
            $user = new User();
            $membership = new TeamMembership();
            $position = new Position();
            $position->setName($positions[$x]);
            $membership->setPosition($position)
                ->setTeam($team)
                ->setStartSemester($this->activeSemester);
            $user->setMemberships(array($membership));
            $users[] = $user;
        }

        $sortedMemberships = $this->sortExtension->teamPositionSortFilter($users, $team);
        $sortedPositions = ['Leder', 'Nestleder', 'Assistent', 'Medlem', 'Sekretær', 'Økonomi', ''];
        for ($x = 0; $x < 7; ++$x) {
            $this->assertEquals($sortedMemberships[$x]->getActiveTeamMemberships()[0]->getPositionName(), $sortedPositions[$x]);
        }
    }
}
