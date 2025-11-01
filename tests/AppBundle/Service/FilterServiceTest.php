<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamMembership;
use AppBundle\Service\FilterService;
use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FilterServiceTest extends KernelTestCase
{
    /**
     * @var FilterService
     */
    private $service;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->service = $kernel->getContainer()->get(FilterService::class);
    }

    public function testFilterTeamMembershipsByTeam()
    {
        $team1 = new Team();
        $team2 = new Team();

        $membership1 = new TeamMembership();
        $membership1->setTeam($team1);

        $membership2 = new TeamMembership();
        $membership2->setTeam($team2);

        $membership3 = new TeamMembership();
        $membership3->setTeam($team1);

        $memberships = [$membership1, $membership2, $membership3];

        $filtered = $this->service->filterTeamMembershipsByTeam($memberships, $team1);

        $this->assertCount(2, $filtered);
        $this->assertContains($membership1, $filtered);
        $this->assertContains($membership3, $filtered);
        $this->assertNotContains($membership2, $filtered);
    }

    public function testFilterTeamMembershipsByTeamReturnsEmptyArray()
    {
        $team1 = new Team();
        $team2 = new Team();

        $membership1 = new TeamMembership();
        $membership1->setTeam($team2);

        $memberships = [$membership1];

        $filtered = $this->service->filterTeamMembershipsByTeam($memberships, $team1);

        $this->assertEmpty($filtered);
    }

    public function testFilterDepartmentsByActiveAdmission()
    {
        $semester = new Semester();
        $semester->setSemester('H2024');

        $admissionPeriodWithAdmission = new AdmissionPeriod();
        $admissionPeriodWithAdmission->setActiveAdmission(true);

        $admissionPeriodWithoutAdmission = new AdmissionPeriod();
        $admissionPeriodWithoutAdmission->setActiveAdmission(false);

        $department1 = new Department();
        $department1->setShortName('NTNU');
        $department1->setCurrentAdmissionPeriod($admissionPeriodWithAdmission);

        $department2 = new Department();
        $department2->setShortName('UiO');
        $department2->setCurrentAdmissionPeriod($admissionPeriodWithoutAdmission);

        $department3 = new Department();
        $department3->setShortName('UiB');
        $department3->setCurrentAdmissionPeriod(null);

        $departments = [$department1, $department2, $department3];

        $filtered = $this->service->filterDepartmentsByActiveAdmission($departments, true);

        $this->assertCount(1, $filtered);
        $this->assertContains($department1, $filtered);
        $this->assertNotContains($department2, $filtered);
        $this->assertNotContains($department3, $filtered);
    }

    public function testFilterDepartmentsByActiveAdmissionReturnsOnlyInactive()
    {
        $admissionPeriodWithAdmission = new AdmissionPeriod();
        $admissionPeriodWithAdmission->setActiveAdmission(true);

        $admissionPeriodWithoutAdmission = new AdmissionPeriod();
        $admissionPeriodWithoutAdmission->setActiveAdmission(false);

        $department1 = new Department();
        $department1->setShortName('NTNU');
        $department1->setCurrentAdmissionPeriod($admissionPeriodWithAdmission);

        $department2 = new Department();
        $department2->setShortName('UiO');
        $department2->setCurrentAdmissionPeriod($admissionPeriodWithoutAdmission);

        $department3 = new Department();
        $department3->setShortName('UiB');
        $department3->setCurrentAdmissionPeriod(null);

        $departments = [$department1, $department2, $department3];

        $filtered = $this->service->filterDepartmentsByActiveAdmission($departments, false);

        $this->assertCount(2, $filtered);
        $this->assertNotContains($department1, $filtered);
        $this->assertContains($department2, $filtered);
        $this->assertContains($department3, $filtered);
    }
}

