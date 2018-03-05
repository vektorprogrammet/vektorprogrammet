<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BoardAndTeamController extends Controller
{
    public function showAction()
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        return $this->render('team/board_and_team.html.twig', array(
            'departments' => $departments,
            'board' => $board,
            'numberOfTeams' => $numberOfTeams,
        ));
    }
}
