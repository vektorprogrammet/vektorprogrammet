<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParticipantHistoryController extends Controller
{
    public function showAction() {
		
		// Only Team members or above can see this page
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
		
			// Find all work histories
			$workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findAll();
			
			// Find all assistantHistories
			$assistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findAll();
			
			// Find all departments
			$departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
			
			return $this->render('participant_history/index.html.twig', array(
				'workHistories' => $workHistories,
				'assistantHistories' => $assistantHistories,
				'departments' => $departments, 
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
		
    }
}