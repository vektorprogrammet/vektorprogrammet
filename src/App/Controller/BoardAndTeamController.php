<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ExecutiveBoard;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\ExecutiveBoardRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\Semester;
use App\Entity\User;
use App\Service\GeoLocation;

class BoardAndTeamController extends BaseController
{
    public function __construct(
        private DepartmentRepository $departmentRepo,
        private ExecutiveBoardRepository $executiveBoardRepo,
        private UserRepository $userRepo,
        private GeoLocation $geoLocation,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function showAction()
    {
        // Find all departments
        $departments = $this->departmentRepo->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->executiveBoardRepo->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = array();
        /** @var Department $department */
        foreach ($departments as $department) {
            $currentSemester = $this->getCurrentSemester();
            $departmentStats[$department->getCity()] = array(
                'numTeamMembers' => sizeof($this->userRepo->findUsersInDepartmentWithTeamMembershipInSemester($department, $currentSemester)),
                'numAssistants' => sizeof($this->userRepo->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $currentSemester)),
            );
        }

        return $this->render('team/board_and_team.html.twig', array(
            'departments' => $departments,
            'board' => $board,
            'numberOfTeams' => $numberOfTeams,
            'departmentStats' => $departmentStats,
        ));
    }
}
