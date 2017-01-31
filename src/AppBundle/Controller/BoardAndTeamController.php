<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BoardAndTeamController extends Controller
{
    public function showAction()
    {
        // Find all departments
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
        
        return $this->render('about/board_and_team.html.twig', array(
            'departments' => $departments,
        ));
    }
}
