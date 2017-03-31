<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BoardAndTeamController extends Controller
{
    public function showAction()
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
        $board = $this->getDoctrine()->getRepository('AppBundle:ExecutiveBoard')->find(1);

        return $this->render('about/board_and_team.html.twig', array(
            'departments' => $departments,
            'board' => $board,
        ));
    }
}
