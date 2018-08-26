<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BoardAndTeamController extends Controller
{
    public function showAction()
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findActive();
        $departments = $this->get('app.geolocation')->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = array();
        /** @var \AppBundle\Entity\Department $department */
        foreach ($departments as $department) {
            $currentOrLatestSemester = $department->getCurrentOrLatestSemester();
            $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
            $departmentStats[$department->getCity()] = array(
                'numTeamMembers' => sizeof($userRepository->findUsersWithTeamMembershipInSemester($currentOrLatestSemester)),
                'numAssistants' => sizeof($userRepository->findUsersWithAssistantHistoryInSemester($currentOrLatestSemester)),
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
