<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BoardAndTeamController extends Controller
{
	public function showAction() {

		// Find all work histories
		$workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistories();

		// Find all teams
		$teams = $this->getDoctrine()->getRepository('AppBundle:Team')->findAll();

		// Find all departments
		$departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

		// Find user's department
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			$department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
		}


		return $this->render('about/board_and_team.html.twig', array(
			'WorkHistories' => $workHistories,
			'Teams' => $teams,
			'Departments' => $departments,
			'Department' => $department,
		));

	}
}