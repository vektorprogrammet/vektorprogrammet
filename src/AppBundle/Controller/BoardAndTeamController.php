<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Service\GeoLocation;

class BoardAndTeamController extends BaseController
{
    public function showAction()
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository(Department::class)->findActive();
        $departments = $this->get(GeoLocation::class)->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->getDoctrine()->getRepository(ExecutiveBoard::class)->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = array();
        /** @var Department $department */
        foreach ($departments as $department) {
            $currentSemester = $this->getDoctrine()->getRepository(Semester::class)->findCurrentSemester();
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $departmentStats[$department->getCity()] = array(
                'numTeamMembers' => sizeof($userRepository->findUsersInDepartmentWithTeamMembershipInSemester($department, $currentSemester)),
                'numAssistants' => sizeof($userRepository->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $currentSemester)),
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
