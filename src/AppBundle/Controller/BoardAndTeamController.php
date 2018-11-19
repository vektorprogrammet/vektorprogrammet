<?php

namespace AppBundle\Controller;

use AppBundle\Service\GeoLocation;

class BoardAndTeamController extends BaseController
{
    public function showAction()
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();
        $departments = $this->get(GeoLocation::class)->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = array();
        /** @var \AppBundle\Entity\Department $department */
        foreach ($departments as $department) {
            $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemester();
            $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
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
