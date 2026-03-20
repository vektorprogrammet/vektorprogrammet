<?php

namespace App\Organization\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Organization\Infrastructure\Repository\ExecutiveBoardRepository;
use App\Shared\Repository\SemesterRepository;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Support\Infrastructure\GeoLocation;
use Symfony\Component\Routing\Attribute\Route;

class BoardAndTeamController extends BaseController
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepo,
        private readonly ExecutiveBoardRepository $executiveBoardRepo,
        private readonly UserRepository $userRepo,
        private readonly GeoLocation $geoLocation,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/styretogteam', name: 'boardandteam_show', methods: ['GET'])]
    #[Route('/team', name: 'team')]
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

        $departmentStats = [];
        /** @var Department $department */
        foreach ($departments as $department) {
            $currentSemester = $this->getCurrentSemester();
            $departmentStats[$department->getCity()] = [
                'numTeamMembers' => sizeof($this->userRepo->findUsersInDepartmentWithTeamMembershipInSemester($department, $currentSemester)),
                'numAssistants' => sizeof($this->userRepo->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $currentSemester)),
            ];
        }

        return $this->render('team/board_and_team.html.twig', [
            'departments' => $departments,
            'board' => $board,
            'numberOfTeams' => $numberOfTeams,
            'departmentStats' => $departmentStats,
        ]);
    }
}
